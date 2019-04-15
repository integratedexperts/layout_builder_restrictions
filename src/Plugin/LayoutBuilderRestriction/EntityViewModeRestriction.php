<?php

namespace Drupal\layout_builder_restrictions\Plugin\LayoutBuilderRestriction;

use Drupal\Core\Config\Entity\ThirdPartySettingsInterface;
use Drupal\layout_builder_restrictions\Plugin\LayoutBuilderRestrictionBase;
use Drupal\layout_builder\OverridesSectionStorageInterface;

/**
 * EntityViewModeRestriction Plugin.
 *
 * @LayoutBuilderRestriction(
 *   id = "entity_view_mode_restriction",
 *   title = @Translation("Restrict blocks/layouts per entity view mode")
 * )
 */
class EntityViewModeRestriction extends LayoutBuilderRestrictionBase {

  /**
   * Restrict placeable blocks based on entity type & view mode.
   */
  public function alterBlockDefinitions(array $definitions, array $context) {
    // Respect restrictions on allowed blocks specified by the section storage.
    if (isset($context['section_storage'])) {
      $default = $context['section_storage'] instanceof OverridesSectionStorageInterface ? $context['section_storage']->getDefaultSectionStorage() : $context['section_storage'];
      $allowed_blocks = $default instanceof ThirdPartySettingsInterface
        ? $default->getThirdPartySetting('layout_builder_restrictions', 'allowed_blocks', [])
        : [];
      // Filter blocks from entity-specific SectionStorage (i.e., UI).
      if (!empty($allowed_blocks)) {
        foreach ($definitions as $delta => $definition) {
          $category = (string) $definition['category'];
          if (in_array($category, array_keys($allowed_blocks))) {
            // This category has restrictions.
            if (!in_array($delta, $allowed_blocks[$category])) {
              // The current block is not in the allowed list for this category.
              unset($definitions[$delta]);
            }
          }
        }
      }
    }
    return $definitions;
  }

  /**
   * Alter the section definitions.
   *
   * In this example, remove the `layout_onecol` and `layout_twocol_section`
   * sections if we are on a node entity, and the node bundle
   * is 'utexas_flex_page'.
   */
  public function alterSectionDefinitions(array $definitions, array $context) {
    // Respect restrictions on allowed layouts specified by section storage.
    if (isset($context['section_storage'])) {
      $default = $context['section_storage'] instanceof OverridesSectionStorageInterface ? $context['section_storage']->getDefaultSectionStorage() : $context['section_storage'];
      if ($default instanceof ThirdPartySettingsInterface) {
        $allowed_layouts = $default->getThirdPartySetting('layout_builder_restrictions', 'allowed_layouts', []);
        // Filter blocks from entity-specific SectionStorage (i.e., UI).
        if (!empty($allowed_layouts)) {
          $definitions = array_intersect_key($definitions, array_flip($allowed_layouts));
        }
      }
    }
    return $definitions;
  }

}
