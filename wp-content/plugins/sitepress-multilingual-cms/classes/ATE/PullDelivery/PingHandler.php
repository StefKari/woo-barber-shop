<?php

namespace WPML\TM\ATE\PullDelivery;

use WPML\TM\ATE\REST\PullCollect;
use WPML\TM\Jobs\JobLog;

class PingHandler {

	private $pacer;

	private $spawner;

	private $watchdog;

	private $liveSignals;

	private $noCreditResume;

	private $parkedJobs;

	public function __construct(
		Pacer $pacer,
		Spawner $spawner,
		Watchdog $watchdog,
		LiveSignals $liveSignals,
		NoCreditResume $noCreditResume,
		ParkedJobs $parkedJobs
	) {
		$this->pacer          = $pacer;
		$this->spawner        = $spawner;
		$this->watchdog       = $watchdog;
		$this->liveSignals    = $liveSignals;
		$this->noCreditResume = $noCreditResume;
		$this->parkedJobs     = $parkedJobs;
	}

	public function handle( $tabId, $force = false, $wake = false ) {
		$state = $this->watchdog->evaluate();

		$state = $this->liveSignals->refresh( $state );

		$state = $this->parkedJobs->prune( $state );

		$state = $this->noCreditResume->maybeResume( $state, $wake );

		if ( $force ) {
			$state = $this->pickUp( $state );
		}

		$pacing = $this->pacer->pace( $tabId, $state );
		$state  = $pacing['state'];

		$sinceWebhook = time() - (int) $state['last_webhook_at'];

		if (
			! $force
			&& $pacing['isPacer']
			&& State::isCollectionDue( $state )
			&& (int) $state['last_webhook_at'] > 0
			&& $sinceWebhook < Cadence::RECENT_WEBHOOK_QUIET
		) {
			$deferBy = Cadence::RECENT_WEBHOOK_QUIET - $sinceWebhook;
			$state   = State::deferCollection( time() + $deferBy );

			JobLog::maybeInitRequest();
			JobLog::createNewGroup( JobLog::GROUP_ID_DOWNLOAD_JOBS, 'ATE pull delivery ping' );
			JobLog::add(
				'pull_collect_deferred_recent_webhook',
				[
					'since_webhook' => $sinceWebhook,
					'deferred_by'   => $deferBy,
				]
			);
			JobLog::finishCurrentGroup();
		} elseif ( ( $force || $pacing['isPacer'] ) && State::isCollectionDue( $state ) ) {
			$state = State::onCollectionArranged();

			$this->spawner->spawn();
		} elseif ( ( $force || $pacing['isPacer'] ) && State::isSignalsReadDue( $state ) ) {
			$state = State::onSignalsReadArranged();

			$this->spawner->spawn();
		}

		$answer = [
			'next_ping_in' => Cadence::pingCadence(
				$state['mode'],
				$state['empty_runs'],
				$pacing['isPacer'],
				$state['pending_count']
			),
			'snapshot'     => State::snapshot( $state ),
		];

		$handoff = Spawner::handoffToken();
		if ( $handoff ) {
			$answer['collect'] = [
				'url'   => PullCollect::url(),
				'token' => $handoff,
			];
		}

		return $answer;
	}

	private function pickUp( array $state ) {
		if ( Modes::isPull( $state['mode'] ) ) {
			return State::forceManualPull();
		}

		if ( (int) $state['pending_count'] < 1 ) {
			return $state;
		}

		return State::onPickUpRequested( $state['oldest_pending_at'] );
	}
}
