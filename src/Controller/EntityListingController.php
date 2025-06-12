<?php

namespace Drupal\project_overview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\field\Entity\FieldStorageConfig;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a controller to list ECK entity types and their entities.
 */
class EntityListingController extends ControllerBase {

/**
   * Lists all fields and their configurations for content types.
   *
   * Supports output formats of HTML, JSON, or Markdown based on the query
   * parameter "format". HTML is returned by default.
   *
   * @return \Symfony\Component\HttpFoundation\Response|array
   *   A Response object for JSON/Markdown or a render array for HTML output.
   */
  public function listContentTypeFields() {
    $format = \Drupal::request()->query->get('format', 'html');
    $grouped_fields = $this->getGroupedContentTypeFields();

    switch (strtolower($format)) {
      case 'json':
        return new JsonResponse($grouped_fields);

      case 'markdown':
        return $this->buildMarkdownResponse($grouped_fields);

      case 'html':
      default:
        return $this->buildHtmlRenderArray($grouped_fields);
    }
  }

  /**
   * Returns an array of fields grouped by content type, excluding certain entity types.
   */
  private function getGroupedContentTypeFields(): array {
    $field_storage_configs = FieldStorageConfig::loadMultiple();
    $excluded = [
      'media',
      'product',
      'user',
      'profile',
      'commerce_product',
      'block_content',
      'comment',
    ];
    $grouped_fields = [];

    foreach ($field_storage_configs as $field_storage_config) {
      if (!in_array($field_storage_config->getTargetEntityTypeId(), $excluded)) {
        foreach ($field_storage_config->getBundles() as $content_type) {
          $grouped_fields[$content_type][] = [
            'field_name' => $field_storage_config->getName() . ' ' . $field_storage_config->getTargetEntityTypeId(),
            'field_type' => $field_storage_config->getType(),
            'settings' => $field_storage_config->getSettings(),
          ];
        }
      }
    }
    return $grouped_fields;
  }

  /**
   * Builds a render array for HTML output.
   */
  private function buildHtmlRenderArray(array $grouped_fields): array {
    $output = [];
    foreach ($grouped_fields as $content_type => $fields) {
      $rows = [];
      foreach ($fields as $field) {
        $rows[] = [
          $field['field_name'],
          $field['field_type'],
          json_encode($field['settings'], JSON_PRETTY_PRINT),
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
   * Builds a Markdown response for the grouped fields.
   */
  private function buildMarkdownResponse(array $grouped_fields): Response {
    $markdown = '';
    foreach ($grouped_fields as $type => $fields) {
      $markdown .= '## ' . $type . "\n";
      foreach ($fields as $field) {
        $markdown .= '* **' . $field['field_name'] . '** (' . $field['field_type'] . ")\n";
        $markdown .= '  - `settings`: ' . json_encode($field['settings']) . "\n";
      }
      $markdown .= "\n";
    }
    return new Response($markdown, 200, ['Content-Type' => 'text/markdown']);
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
