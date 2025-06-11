<?php

namespace Drupal\project_overview\Controller;

use Symfony\Component\Yaml\Yaml;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides a page listing all services in the container.
 */
class RouteListController extends ControllerBase {

  /**
   * Lists all services in the service container.
   *
   * @return array
   *   A render array for the page.
   */
  public function listRoutes() {
    $base_directories = [
      'modules/custom',
      'modules/dev',
      'modules/serious_design',
    ];

    $routes_by_module = [];

    foreach ($base_directories as $base_dir) {
      $module_paths = glob(DRUPAL_ROOT . "/$base_dir/*", GLOB_ONLYDIR);

      foreach ($module_paths as $module_path) {
        $module = basename($module_path);
        $routing_file = "$module_path/{$module}.routing.yml";

        if (!file_exists($routing_file)) {
          // Skip modules without routing files.
          continue;
        }

        // Parse the YAML file.
        $routing_data = Yaml::parseFile($routing_file);

        if (!$routing_data) {
          // Skip if empty or invalid.
          continue;
        }

        // Initialize module container.
        if (!isset($routes_by_module[$module])) {
          $routes_by_module[$module] = [
            '#type' => 'details',
            '#title' => ucfirst(str_replace('_', ' ', $module)),
            '#open' => FALSE,
            'table' => [
              '#type' => 'table',
              '#header' => [t('Route Name'), t('Path'), t('Methods')],
              '#rows' => [],
            ],
          ];
        }

        // Process each route.
        foreach ($routing_data as $route_name => $route_info) {
          $path = $route_info['path'] ?? t('N/A');
          $methods = $route_info['requirements']['_method'] ?? 'GET, POST';

          // Add route to module section.
          $routes_by_module[$module]['table']['#rows'][] = [
            $route_name,
            $path,
            $methods,
          ];
        }
      }
    }

    // Create filtering form.
    $form = [
      '#type' => 'container',
      '#attributes' => ['id' => 'routes-filter-container'],
      'filter' => [
        '#type' => 'select',
        '#title' => t('Filter by module'),
        '#options' => ['all' => t('All Modules')] + array_combine(array_keys($routes_by_module), array_map(fn($m) => ucfirst(str_replace('_', ' ', $m)), array_keys($routes_by_module))),
        '#attributes' => ['onchange' => 'Drupal.routesFilter.update()'],
      ],
      'routes' => [
        '#type' => 'container',
        '#attributes' => ['id' => 'routes-list'],
        'modules' => $routes_by_module,
      ],
    ];

    // Attach JavaScript for filtering.
    $form['#attached']['library'][] = 'core/drupal.ajax';
    $form['#attached']['drupalSettings']['routesFilter'] = [
      'modules' => array_keys($routes_by_module),
    ];
    $form['#attached']['html_head'][] = [
      [
        '#tag' => 'script',
        '#value' => <<<JS
        (function (Drupal, drupalSettings) {
          Drupal.routesFilter = {
            update: function () {
              var selectedModule = document.querySelector('[name="filter"]').value;
              document.querySelectorAll('#routes-list > details').forEach(detail => {
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
      'routes-filter-js',
    ];

    return $form;
  }

}
