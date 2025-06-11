<?php

namespace Drupal\project_overview\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides a page listing all services in the container.
 */
class ServiceListController extends ControllerBase {

  /**
   * Lists all services in the service container.
   *
   * @return array
   *   A render array for the page.
   */
  public function listServices() {
    $container = \Drupal::getContainer();
    $service_ids = $container->getServiceIds();

    // Define custom module namespaces to filter services.
    $custom_modules = [
      'animal_classification', 'animal_genealogy', 'farm_entities',
      'farm_manager_core', 'entities_management', 'farm_entities_statistics',
      'farm_events', 'health_management', 'sd_utilities', 'project_overview',
      'login_redirect',
    ];
    $custom_namespaces = array_map(fn($module) => 'Drupal\\' . $module . '\\', $custom_modules);

    $services_by_module = [];

    foreach ($service_ids as $service_id) {
      try {
        $service_instance = $container->get($service_id);
        $service_class = get_class($service_instance);
      }
      catch (\Exception $e) {
        $service_class = t('Unavailable');
        // Skip services that cannot be instantiated.
        continue;
      }

      // Identify module.
      $module = NULL;
      foreach ($custom_namespaces as $index => $namespace) {
        if (strpos($service_class, $namespace) === 0) {
          $module = $custom_modules[$index];
          break;
        }
      }

      if (!$module) {
        // Skip non-custom services.
        continue;
      }

      // Get service description (docstring)
      $description = t('No description available');
      try {
        $reflection = new \ReflectionClass($service_class);
        $doc_comment = $reflection->getDocComment();
        if ($doc_comment) {
          $description = trim(str_replace(['/**', '*/', '*'], '', $doc_comment));
        }
      }
      catch (\ReflectionException $e) {
        // If reflection fails, we simply use the default message.
      }

      // Initialize module container if not set.
      if (!isset($services_by_module[$module])) {
        $services_by_module[$module] = [
          '#type' => 'details',
        // Prettify module name.
          '#title' => ucfirst(str_replace('_', ' ', $module)),
          '#open' => FALSE,
          'table' => [
            '#type' => 'table',
            '#header' => [t('Service ID'), t('Class Name'), t('Description')],
            '#rows' => [],
          ],
        ];
      }

      // Add service entry.
      $services_by_module[$module]['table']['#rows'][] = [
        $service_id,
        $service_class,
        $description,
      ];
    }

    // Create filtering form.
    $form = [
      '#type' => 'container',
      '#attributes' => ['id' => 'services-filter-container'],
      'filter' => [
        '#type' => 'select',
        '#title' => t('Filter by module'),
        '#options' => ['all' => t('All Modules')] + array_combine($custom_modules, array_map(fn($m) => ucfirst(str_replace('_', ' ', $m)), $custom_modules)),
    // JS function to filter services.
        '#attributes' => ['onchange' => 'Drupal.servicesFilter.update()'],
      ],
      'services' => [
        '#type' => 'container',
        '#attributes' => ['id' => 'services-list'],
        'modules' => $services_by_module,
      ],
    ];

    // Attach JavaScript to handle filtering.
    $form['#attached']['library'][] = 'core/drupal.ajax';
    $form['#attached']['drupalSettings']['servicesFilter'] = [
      'modules' => $custom_modules,
    ];
    $form['#attached']['html_head'][] = [
      [
        '#tag' => 'script',
        '#value' => <<<JS
        (function (Drupal, drupalSettings) {
          Drupal.servicesFilter = {
            update: function () {
              var selectedModule = document.querySelector('[name="filter"]').value;
              document.querySelectorAll('#services-list > details').forEach(detail => {
                if (selectedModule === 'all' || detail.getAttribute('data-module') === selectedModule) {
                  detail.style.display = '';
                } else {
                  detail.style.display = 'none';
                }
              });
            }
          };
        })(Drupal, drupalSettings);
        JS
      ],
      'services-filter-js',
    ];

    return $form;
  }

}
