<?php

namespace Drupal\layout_builder_restrictions\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Layout builder restriction plugin plugin manager.
 */
class LayoutBuilderRestrictionManager extends DefaultPluginManager {

  /**
   * Constructs a new LayoutBuilderRestrictionManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/LayoutBuilderRestriction', $namespaces, $module_handler, 'Drupal\layout_builder_restrictions\Plugin\LayoutBuilderRestrictionInterface', 'Drupal\layout_builder_restrictions\Annotation\LayoutBuilderRestriction');

    $this->alterInfo('layout_builder_restrictions_layout_builder_restriction_info');
    $this->setCacheBackend($cache_backend, 'layout_builder_restrictions_layout_builder_restriction_plugins');
  }

}
