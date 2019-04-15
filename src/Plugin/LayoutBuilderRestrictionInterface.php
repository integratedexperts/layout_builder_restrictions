<?php

namespace Drupal\layout_builder_restrictions\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Layout builder restriction plugin plugins.
 */
interface LayoutBuilderRestrictionInterface extends PluginInspectionInterface {

  /**
   * Alter the block definitions.
   *
   * This will be called when the block list is being populated
   * for placing a block into a section.
   * A plugin can manipulate the definitions as needed, with
   * optional context about the section being utilized.
   *
   * @param array $definitions
   *   All the available block definitions.
   * @param array $context
   *   At a minimum, the entity, view_mode, layout, and region.
   *   Depending on the plugin, they may or may not ignore some of
   *   these contexts.
   */
  public function alterBlockDefinitions(array $definitions, array $context);

  /**
   * Alter the layout definitions.
   *
   * This will be called when the layout list is being populated.
   * A plugin can manipulate the definitions as needed.
   *
   * @param array $definitions
   *   All the available block definitions.
   * @param array $context
   *   At a minimum, the entity, view_mode, layout, and region.
   *   Depending on the plugin, they may or may not ignore some of
   *   these contexts.
   */
  public function alterSectionDefinitions(array $definitions, array $context);

  /**
   * Determine whether the block being moved is allowed to the destination.
   *
   * @param array $context
   *   At a minimum, the entity, view_mode, layout, and region.
   *   Depending on the plugin, they may or may not ignore some of
   *   these contexts.
   *
   * @return bool
   *   Is this block restricted from being placed in the current context?
   */
  public function blockMovementRestricted(array $context);

}
