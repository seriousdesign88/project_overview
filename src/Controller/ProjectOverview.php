<?php

namespace Drupal\project_overview\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides a controller to list ECK entity types and their entities.
 */
class ProjectOverview extends ControllerBase {

  /**
   * Displays the Project Overview page.
   */
  public function overview() {
    // Pass variables to the Twig template.
    $variables = [
      'title' => $this->t('Task List'),
      'description' => $this->t('This page provides an overview of the project tasks.'),
      'project_version' => '1.0.2',
    ];

    return [
      '#theme' => 'project-overview',
      '#variables' => $variables,
    ];
  }

}
