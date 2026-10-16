<?php

namespace WPML\Infrastructure\WordPress\Component\Taxonomy\Application\Query;

use WPML\Core\SharedKernel\Component\Item\Application\Query\Dto\UntranslatedTypeCountDto;
use WPML\Core\SharedKernel\Component\Item\Application\Query\UntranslatedTypesCountQueryInterface;
use WPML\Core\SharedKernel\Component\Language\Application\Query\LanguagesQueryInterface;
use WPML\Core\SharedKernel\Component\Taxonomy\Application\Query\Dto\TaxonomyDto;
use WPML\Core\SharedKernel\Component\Taxonomy\Application\Query\TranslatableTaxonomiesQueryInterface;

class UntranslatedTypesCountBeforeSetupQuery implements UntranslatedTypesCountQueryInterface {

  private $taxonomiesQuery;

  private $languagesQuery;

  private $wpdb;


  public function __construct(
    TranslatableTaxonomiesQueryInterface $taxonomiesQuery,
    LanguagesQueryInterface $languagesQuery,
    $wpdb = null
  ) {
    $this->taxonomiesQuery = $taxonomiesQuery;
    $this->languagesQuery  = $languagesQuery;
    $this->wpdb            = $wpdb ?: $GLOBALS['wpdb'];
  }


  public function forKind() {
    return UntranslatedTypesCountQueryInterface::KIND_TAXONOMY;
  }


  public function get( array $queryData = [] ): array {
    $taxonomies = $this->taxonomiesQuery->getTranslatable();
    if ( ! $taxonomies ) {
      return [];
    }

    $counts = $this->countTermsPerTaxonomy( $taxonomies );

    return array_map(
      function ( TaxonomyDto $taxonomy ) use ( $counts ) {
        return new UntranslatedTypeCountDto(
          $taxonomy->getPlural(),
          $taxonomy->getSingular(),
          $counts[ $taxonomy->getId() ] ?? 0,
          UntranslatedTypesCountQueryInterface::KIND_TAXONOMY,
          'tax_' . $taxonomy->getId()
        );
      },
      $taxonomies
    );
  }


  public function getSomeIds( $numberOfIdsToFetch, $offset, $type = '' ) {
    $taxonomiesIn = $this->taxonomiesIn();
    if ( $taxonomiesIn === '' ) {
      return [];
    }

    $limit  = absint( $numberOfIdsToFetch );
    $offset = absint( $offset );

    $ids = $this->wpdb->get_col(
      "SELECT tt.term_taxonomy_id
       FROM {$this->wpdb->term_taxonomy} AS tt
       {$this->translationJoin()}
       WHERE tt.taxonomy IN ({$taxonomiesIn})
         AND {$this->needsTranslationCondition()}
       ORDER BY tt.term_taxonomy_id ASC
       LIMIT {$limit} OFFSET {$offset}"
    );

    return array_map( 'intval', $ids );
  }


  private function translationJoin(): string {
    return "LEFT JOIN {$this->wpdb->prefix}icl_translations AS itr
              ON itr.element_id = tt.term_taxonomy_id
             AND itr.element_type = CONCAT( 'tax_', tt.taxonomy )";
  }


  private function needsTranslationCondition(): string {
    $conditions = [
      '( itr.translation_id IS NULL OR itr.source_language_code IS NULL )',
    ];

    $secondaryCount = count( $this->languagesQuery->getSecondary() );
    if ( $secondaryCount > 0 ) {
      $conditions[] = "(
        itr.trid IS NULL
        OR (
          SELECT COUNT(*)
          FROM {$this->wpdb->prefix}icl_translations AS sibling
          WHERE sibling.trid = itr.trid
            AND sibling.source_language_code IS NOT NULL
        ) < {$secondaryCount}
      )";
    }

    $wpmlOwned = $this->termTaxonomyIdsWpmlTranslatesItself();
    if ( $wpmlOwned ) {
      $conditions[] = 'tt.term_taxonomy_id NOT IN ( ' . implode( ',', $wpmlOwned ) . ' )';
    }

    return '( ' . implode( ' AND ', $conditions ) . ' )';
  }


  private function termTaxonomyIdsWpmlTranslatesItself(): array {
    $sitepress = $GLOBALS['sitepress'] ?? null;

    $ids = [];

    if ( is_object( $sitepress ) && method_exists( $sitepress, 'get_setting' ) ) {
      foreach ( (array) $sitepress->get_setting( 'default_categories', [] ) as $termTaxonomyId ) {
        $termTaxonomyId = (int) $termTaxonomyId;
        if ( $termTaxonomyId > 0 ) {
          $ids[] = $termTaxonomyId;
        }
      }
    }

    $defaultCategory       = get_option( 'default_category' );
    $defaultCategoryTermId = is_numeric( $defaultCategory ) ? (int) $defaultCategory : 0;
    if ( $defaultCategoryTermId > 0 ) {
      $term = get_term( $defaultCategoryTermId, 'category' );

      if ( $term instanceof \WP_Term ) {
        $ids[] = $term->term_taxonomy_id;
      }
    }

    return array_values( array_unique( $ids ) );
  }


  private function countTermsPerTaxonomy( array $taxonomies ): array {
    $taxonomiesIn = $this->taxonomiesIn( $taxonomies );
    if ( $taxonomiesIn === '' ) {
      return [];
    }

    $rows = $this->wpdb->get_results(
      "SELECT tt.taxonomy, COUNT(*) AS total
       FROM {$this->wpdb->term_taxonomy} AS tt
       {$this->translationJoin()}
       WHERE tt.taxonomy IN ({$taxonomiesIn})
         AND {$this->needsTranslationCondition()}
       GROUP BY tt.taxonomy",
      ARRAY_A
    );

    $counts = [];
    foreach ( (array) $rows as $row ) {
      $counts[ (string) $row['taxonomy'] ] = (int) $row['total'];
    }

    return $counts;
  }


  private function taxonomiesIn( $taxonomies = null ): string {
    if ( $taxonomies === null ) {
      $taxonomies = $this->taxonomiesQuery->getTranslatable();
    }

    if ( ! $taxonomies ) {
      return '';
    }

    $slugs = array_map(
      function ( TaxonomyDto $taxonomy ) {
        return esc_sql( $taxonomy->getId() );
      },
      $taxonomies
    );

    return "'" . implode( "','", $slugs ) . "'";
  }


}
