<?php

namespace Drupal\project_overview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Provides a controller to list ECK entity types and their entities.
 */
class EntityListingController extends ControllerBase {

  /**
   * Lists all fields and their configurations for content types.
   *
   * @return array
   *   A render array for the page.
   */
  public function listContentTypeFields() {
    // Get all field storage configurations.
    $field_storage_configs = FieldStorageConfig::loadMultiple();

    // Prepare an array to group fields by content type.
    $grouped_fields = [];

    foreach ($field_storage_configs as $field_storage_config) {
      // Only include fields attached to nodes (content types).
      // if ($field_storage_config->getTargetEntityTypeId() === 'node') {.
      if ($field_storage_config->getTargetEntityTypeId() !== 'media' && $field_storage_config->getTargetEntityTypeId() !== 'product' && $field_storage_config->getTargetEntityTypeId() !== 'user'  && $field_storage_config->getTargetEntityTypeId() !== 'profile' &&  $field_storage_config->getTargetEntityTypeId() !== 'commerce_product' && $field_storage_config->getTargetEntityTypeId() !== 'profile'
      && $field_storage_config->getTargetEntityTypeId() !== 'block_content' && $field_storage_config->getTargetEntityTypeId() !== 'comment') {
        $bundles = $field_storage_config->getBundles();
        foreach ($bundles as $content_type) {
          $grouped_fields[$content_type][] = [
            'field_name' => $field_storage_config->getName() . ' ' . $field_storage_config->getTargetEntityTypeId(),
            'field_type' => $field_storage_config->getType(),
            'settings' => json_encode($field_storage_config->getSettings(), JSON_PRETTY_PRINT),
          ];
        }
      }
    }

    // Build the render array with grouped fields.
    $output = [];
    foreach ($grouped_fields as $content_type => $fields) {
      $rows = [];
      foreach ($fields as $field) {
        $rows[] = [
          $field['field_name'],
          $field['field_type'],
          $field['settings'],
        ];
      }

      $output[] = [
        '#type' => 'details',
        '#title' => $this->t('Content Type: @type', ['@type' => $content_type]),
        '#open' => FALSE,
        'table' => [
          '#type' => 'table',
          '#header' => [
            $this->t('Field Name'),
            $this->t('Field Type'),
            $this->t('Settings'),
          ],
          '#rows' => $rows,
          '#empty' => $this->t('No fields found for this content type.'),
        ],
      ];
    }

    return $output;
  }

  /**
   * Lists all entity types and their bundles.
   */
  public function listProviders() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $entity_bundle_manager = \Drupal::service('entity_type.bundle.info');
    $entity_types = $entity_type_manager->getDefinitions();
    $output = [];

    // Group entity types by provider.
    foreach ($entity_types as $entity_type_id => $entity_type) {
      $provider = $entity_type->getProvider();
      $label = $entity_type->getLabel();

      // Initialize provider container if not set.
      if (!isset($output[$provider])) {
        $output[$provider] = [
          '#type' => 'details',
          '#title' => ucfirst($provider),
        // Keep it collapsed by default.
          '#open' => FALSE,
          'table' => [
            '#type' => 'table',
            '#header' => ['Entity Type', 'Bundles'],
            '#rows' => [],
          ],
        ];
      }

      // Get bundles for this entity type.
      $bundles = $entity_bundle_manager->getBundleInfo($entity_type_id);
      $bundle_list = empty($bundles)
        ? t('No bundles')
        : implode(', ', array_keys($bundles));

      // Add entity type as a table row (fixing incorrect #markup usage)
      $output[$provider]['table']['#rows'][] = [
        $label . ' (' . $entity_type_id . ')',
        $bundle_list,
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity-listing']],
      'content' => $output,
    ];
  }

}
