<?php

namespace Drupal\layout_builder_restrictions\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for Layout builder restriction plugin plugins.
 */
abstract class LayoutBuilderRestrictionBase extends PluginBase implements LayoutBuilderRestrictionInterface {

  /**
   * Alter the block definitions.
   */
  public function alterBlockDefinitions(array $definitions, array $context) {
    return $definitions;
  }

  /**
   * Alter the section definitions.
   */
  public function alterSectionDefinitions(array $definitions, array $context) {
    return $definitions;
  }

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
  public function blockMovementRestricted(array $context) {
    return FALSE;
  }

}
