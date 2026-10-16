<?php

namespace WPML\UserInterface\Web\Infrastructure\WordPress\CompositionRoot\Config\Event\Item\WordCount;

use WPML\DicInterface;
use WPML\UserInterface\Web\Infrastructure\WordPress\Events\Item\WordCount\CalculateWordsInPackageListener;
use WPML\UserInterface\Web\Infrastructure\WordPress\Events\Item\WordCount\CalculateWordsInPostListener;
use WPML\UserInterface\Web\Infrastructure\WordPress\Events\Item\WordCount\CalculateWordsInStringListener;
use WPML\UserInterface\Web\Infrastructure\WordPress\Events\Item\WordCount\CountListener;
use WPML\UserInterface\Web\Infrastructure\WordPress\Events\Item\WordCount\OnStringRegisteredInPackageListener;
use function WPML\PHP\Logger\error as logError;

class Events {

  private $dic;

  private $onStringRegisteredInPackageListener;


  public function __construct( DicInterface $dic ) {
    $this->dic = $dic;
    $this->register();
  }


  public function register() {
    add_filter(
      'wpml_word_count_calculate_package',
      function ( $currentValue, $stringId ) {
        return $this->dic->make( CalculateWordsInPackageListener::class )
            ->calculate( $currentValue, $stringId );
      },
      10,
      2
    );

    add_filter(
      'wpml_word_count_calculate_string',
      function ( $currentValue, $stringId ) {
        return $this->dic->make( CalculateWordsInStringListener::class )
            ->calculate( $currentValue, $stringId );
      },
      10,
      2
    );

    add_filter(
      'wpml_word_count_calculate_post',
      function ( $currentValue, $stringId ) {
        return $this->dic->make( CalculateWordsInPostListener::class )
            ->calculate( $currentValue, $stringId );
      },
      10,
      2
    );

    add_filter(
      'wpml_word_count_chars',
      function ( $_, $content ) {
        return $this->dic->make( CountListener::class )
            ->onCalculateChars( (string) $content );
      },
      10,
      2
    );

    add_filter(
      'wpml_word_count_words',
      function ( $_, $content ) {
        return $this->dic->make( CountListener::class )
            ->onCalculateWords( (string) $content );
      },
      10,
      2
    );

    add_action(
      'wpml_st_package_string_registered',
      function ( $package ) {
        if ( is_object( $package ) && isset( $package->ID ) ) {
          $this->getOnStringRegisteredInPackageListener()->registerPackage( $package->ID );
        }
      }
    );

    add_action( 'shutdown', [ $this, 'runShutdownBookkeeping' ] );
  }


  public function runShutdownBookkeeping() {
    try {
      $this->getOnStringRegisteredInPackageListener()->recalculatePackages();
    } catch ( \Throwable $e ) {
      logError( 'WPML word-count shutdown bookkeeping skipped: ' . $e->getMessage() );
    }
  }


  private function getOnStringRegisteredInPackageListener(): OnStringRegisteredInPackageListener {
    if ( $this->onStringRegisteredInPackageListener === null ) {
      $this->onStringRegisteredInPackageListener =
        $this->dic->make( OnStringRegisteredInPackageListener::class );
    }

    return $this->onStringRegisteredInPackageListener;
  }


}
