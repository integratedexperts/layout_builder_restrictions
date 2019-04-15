<?php

namespace Drupal\layout_builder_restrictions\Plugin\LayoutBuilderRestriction;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\layout_builder_restrictions\Plugin\LayoutBuilderRestrictionBase;

/**
 * Example of a LayoutBuilderRestriction Plugin.
 *
 * @LayoutBuilderRestriction(
 *   id = "example_restriction",
 *   title = @Translation("Example Restriction")
 * )
 */
class ExampleRestriction extends LayoutBuilderRestrictionBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Alter the block definitions.
   *
   * In this example, only allow the `google_cse` block for
   * the 'layout_onecol' section, if we are on a node entity,
   * and the node bundle is 'utexas_flex_page'.
   */
  public function alterBlockDefinitions(array $definitions, array $context) {
    $section = $context['section_storage']->getSection($context['delta']);
    $section_contexts = $context['section_storage']->getContexts();
    $entity = $section_contexts[array_keys($section_contexts)[0]]->getContextValue();
    $entity_type = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    $layout_id = $section->getLayoutId();
    if ($layout_id == 'layout_utexas_fullwidth' && $entity_type == 'node' && $bundle == 'utexas_flex_page') {
      $altered_definitions = [];
      $definitions_to_allow = [
        'google_cse',
      ];
      foreach ($definitions_to_allow as $definition) {
        if (isset($definitions[$definition])) {
          $altered_definitions[$definition] = $definitions[$definition];
        }
      }
      return $altered_definitions;
    }
  }

  /**
   * Alter the section definitions.
   *
   * In this example, remove the `layout_onecol` and `layout_twocol_section`
   * sections if we are on a node entity, and the node bundle
   * is 'utexas_flex_page'.
   */
  public function alterSectionDefinitions(array $definitions, array $context) {
    $section_contexts = $context['section_storage']->getContexts();
    $entity = $section_contexts[array_keys($section_contexts)[0]]->getContextValue();
    $entity_type = $entity->getEntityTypeId();
    $bundle = $entity->bundle();
    if ($entity_type == 'node' && $bundle == 'utexas_flex_page') {
      $sections_to_remove = [
        'layout_onecol',
        'layout_twocol_section',
      ];
      foreach ($sections_to_remove as $section) {
        if (isset($definitions[$section])) {
          unset($definitions[$section]);
        }
      }
    }
    return $definitions;
  }

}
