<?php

namespace WPML\TM\ATE\PullDelivery;

use WPML\Core\Component\ATE\Application\Service\CreditsService;
use WPML\FP\Obj;
use WPML\TM\API\ATE\Account;
use WPML\TM\ATE\API\SpendCap;
use WPML\TM\ATE\API\SpendCapState;
use WPML\TM\ATE\AutoTranslate\Endpoint\Resume;
use WPML\TM\Jobs\JobLog;

class NoCreditResume {

	private $resume;

	private $wake = false;

	public function __construct( Resume $resume ) {
		$this->resume = $resume;
	}

	private function floor() {
		if ( $this->wake && SpendCapState::isReached() ) {
			return Cadence::wakeFloor();
		}

		return Cadence::activeFloor();
	}

	private function readCredits() {
		return $this->wake ? $this->freshCredits() : $this->credits();
	}

	public function maybeResume( array $state, $wake = false ) {
		$this->wake = (bool) $wake;
		$floor      = $this->floor();
		$parked     = $this->parkedJobs( $state );

		if ( ! $parked ) {
			if ( SpendCapState::isReached() && time() - (int) $state['resume_checked_at'] >= $floor ) {
				$state = State::update( [ 'resume_checked_at' => time() ] );
				$this->readCredits();
			}

			return $state;
		}

		$fingerprint = $this->fingerprint( $parked );

		if ( $fingerprint === (string) $state['resume_attempted_for'] && ! SpendCapState::isReached() ) {
			return $state;
		}

		if ( time() - (int) $state['resume_checked_at'] < $floor ) {
			return $state;
		}

		$state = State::update( [ 'resume_checked_at' => time() ] );

		if ( ! $this->hasCreditsForWorkInProgress() ) {
			return $state;
		}

		$this->resume->run( wpml_collect( [ 'ateJobIds' => $parked ] ) );

		JobLog::maybeInitRequest();
		JobLog::createNewGroup( JobLog::GROUP_ID_DOWNLOAD_JOBS, 'ATE pull delivery resume' );
		JobLog::add( 'pull_resume_no_credit_jobs', [ 'ateJobIds' => $parked ] );
		JobLog::finishCurrentGroup();

		if ( $this->wake ) {
			$paused = SpendCapState::get();
			if ( $paused ) {
				SpendCapState::save( $paused->asResumed() );
			}
		}

		return State::update(
			[
				'resume_attempted_for'             => $fingerprint,
				'insufficient_balance_ate_job_ids' => [],
				'insufficient_balance_job_ids'     => [],
			]
		);
	}

	private function parkedJobs( array $state ) {
		$ids = array_map( 'intval', (array) $state['insufficient_balance_ate_job_ids'] );

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	private function fingerprint( array $parked ) {
		sort( $parked );

		return md5( implode( ',', $parked ) );
	}

	protected function hasCreditsForWorkInProgress() {
		$credits = $this->readCredits();

		if ( ! is_array( $credits ) || ! $credits ) {
			return false;
		}

		if ( Obj::propOr( false, 'pay_as_you_go', $credits ) && 0 === (int) Obj::propOr( 0, 'subscription_debt', $credits ) ) {
			return $this->spendCapHasRoomAgain( $credits );
		}

		$available  = (int) Obj::propOr( 0, 'available_balance', $credits );
		$inProgress = (int) $this->creditsInProgress();

		return $inProgress > 0 && $available >= $inProgress;
	}

	private function spendCapHasRoomAgain( array $credits ) {
		$paused = SpendCapState::get();
		if ( ! $paused ) {
			return false;
		}

		$cap = SpendCap::fromCredits( $credits );

		if ( null === $cap ) {
			return true;
		}

		return $paused->readingMovedSincePause( $cap );
	}

	protected function credits() {
		return Account::getCredits( true )->getOrElse( null );
	}

	protected function freshCredits() {
		return Account::getCredits( false )->getOrElse( null );
	}

	protected function creditsInProgress() {
		global $wpml_dic;

		if ( ! $wpml_dic ) {
			return 0;
		}

		try {
			$service = $wpml_dic->make( CreditsService::class );

			return (int) $service->getCreditsInProgress()->getCount();
		} catch ( \Throwable $e ) {
			return 0;
		}
	}
}
