# Project Overview

Project Overview is a Drupal module that provides a centralized dashboard for viewing key information about your Drupal site's entities, fields, services, routes, and providers. It is designed to help site builders and developers quickly audit and understand the structure and configuration of their Drupal installation.

## Features

- **Project Overview Dashboard:** View a summary page with project information.
- **Entities and Fields:** List all content entity types and their fields.
- **Services Report:** View all custom services registered in the service container.
- **Routes List:** Display all routes defined by custom modules.
- **Providers List:** See all entity types grouped by their provider.

## Installation

1. Place the `project_overview` directory in your Drupal site's `modules` folder.
2. Enable the module via the Drupal admin UI or with Drush:
   ```sh
   drush en project_overview
   ```
3. Assign the "Access Project Overview" permission to the appropriate user roles.

## Usage
- Access the dashboard at /project-overview or via the admin menu under "Project Overview".
- Use the navigation links to view entities, fields, services, routes, and providers.

## Requirements
Drupal core version ^10 or ^11

## Development
Controllers are located in src/Controller.
Custom theme hook is defined in project_overview.module.
Menu and routing configuration can be found in the .yml files.

## License
GPL-2.0-or-later

## Maintainers
Serious Design