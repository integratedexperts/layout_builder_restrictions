<?php

namespace Drupal\layout_builder_restrictions\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Entity\LayoutEntityDisplayInterface;
use Drupal\layout_builder_restrictions\Traits\PluginHelperTrait;

/**
 * Supplement form UI to add setting for which blocks & layouts are available.
 */
class FormAlter {

  use PluginHelperTrait;

  /**
   * The actual form elements.
   */
  public function alterEntityViewDisplayForm(&$form, FormStateInterface $form_state, $form_id) {
    $display = $form_state->getFormObject()->getEntity();
    $is_enabled = $display->isLayoutBuilderEnabled();
    if ($is_enabled) {
      $form['#entity_builders'][] = [$this, 'entityFormEntityBuild'];
      // Block settings.
      $form['layout']['layout_builder_restrictions']['allowed_blocks'] = [
        '#type' => 'details',
        '#title' => t('Blocks available for placement'),
        '#states' => [
          'disabled' => [
            ':input[name="layout[enabled]"]' => ['checked' => FALSE],
          ],
          'invisible' => [
            ':input[name="layout[enabled]"]' => ['checked' => FALSE],
          ],
        ],
      ];
      $allowed_blocks = $display->getThirdPartySetting('layout_builder_restrictions', 'allowed_blocks', []);
      foreach ($this->getBlockDefinitions($display) as $category => $blocks) {
        $category_form = [
          '#type' => 'fieldset',
          '#title' => $category,
          '#parents' => ['layout_builder_restrictions', 'allowed_blocks'],
        ];
        $category_setting = in_array($category, array_keys($allowed_blocks)) ? "restricted" : "all";
        $category_form['restriction_behavior'] = [
          '#type' => 'radios',
          '#options' => [
            "all" => t('Allow all existing & new %category blocks.', ['%category' => $category]),
            "restricted" => t('Choose specific %category blocks:', ['%category' => $category]),
          ],
          '#default_value' => $category_setting,
          '#parents' => [
            'layout_builder_restrictions',
            'allowed_blocks',
            $category,
            'restriction',
          ],
        ];
        foreach ($blocks as $block_id => $block) {
          $enabled = FALSE;
          if ($category_setting == 'restricted' && in_array($block_id, $allowed_blocks[$category])) {
            $enabled = TRUE;
          }
          $category_form[$block_id] = [
            '#type' => 'checkbox',
            '#title' => $block['admin_label'],
            '#default_value' => $enabled,
            '#parents' => [
              'layout_builder_restrictions',
              'allowed_blocks',
              $category,
              $block_id,
            ],
            '#states' => [
              'invisible' => [
                ':input[name="layout_builder_restrictions[allowed_blocks][' . $category . '][restriction]"]' => ['value' => "all"],
              ],
            ],
          ];
        }
        $form['layout']['layout_builder_restrictions']['allowed_blocks'][$category] = $category_form;
      }
      // Layout settings.
      $allowed_layouts = $display->getThirdPartySetting('layout_builder_restrictions', 'allowed_layouts', []);
      $layout_form = [
        '#type' => 'details',
        '#title' => t('Layouts available for sections'),
        '#parents' => ['layout_builder_restrictions', 'allowed_layouts'],
        '#states' => [
          'disabled' => [
            ':input[name="layout[enabled]"]' => ['checked' => FALSE],
          ],
          'invisible' => [
            ':input[name="layout[enabled]"]' => ['checked' => FALSE],
          ],
        ],
      ];
      $layout_form['layout_restriction'] = [
        '#type' => 'radios',
        '#options' => [
          "all" => t('Allow all existing & new layouts.'),
          "restricted" => t('Allow only specific layouts:'),
        ],
        '#default_value' => !empty($allowed_layouts) ? "restricted" : "all",
      ];
      $definitions = $this->getLayoutDefinitions();
      foreach ($definitions as $plugin_id => $definition) {
        $enabled = FALSE;
        if (!empty($allowed_layouts) && in_array($plugin_id, $allowed_layouts)) {
          $enabled = TRUE;
        }
        $layout_form['layouts'][$plugin_id] = [
          '#type' => 'checkbox',
          '#default_value' => $enabled,
          '#description' => [
            $definition->getIcon(60, 80, 1, 3),
            [
              '#type' => 'container',
              '#children' => $definition->getLabel(),
            ],
          ],
          '#states' => [
            'invisible' => [
              ':input[name="layout_builder_restrictions[allowed_layouts][layout_restriction]"]' => ['value' => "all"],
            ],
          ],
        ];
      }
      $form['layout']['layout_builder_restrictions']['allowed_layouts'] = $layout_form;
    }
  }

  /**
   * Save allowed blocks & layouts for the given entity view mode.
   */
  public function entityFormEntityBuild($entity_type_id, LayoutEntityDisplayInterface $display, &$form, FormStateInterface &$form_state) {
    // Set allowed blocks.
    $allowed_blocks = [];
    $categories = $form_state->getValue([
      'layout_builder_restrictions',
      'allowed_blocks',
    ]);
    if (!empty($categories)) {
      foreach ($categories as $category => $category_setting) {
        if ($category_setting['restriction'] === 'restricted') {
          $allowed_blocks[$category] = [];
          unset($category_setting['restriction']);
          foreach ($category_setting as $block_id => $block_setting) {
            if ($block_setting == '1') {
              // Include only checked blocks.
              $allowed_blocks[$category][] = $block_id;
            }
          }
        }
      }
      $display->setThirdPartySetting('layout_builder_restrictions', 'allowed_blocks', $allowed_blocks);
    }

    // Set allowed layouts.
    $layout_restriction = $form_state->getValue([
      'layout_builder_restrictions',
      'allowed_layouts',
      'layout_restriction',
    ]);
    $allowed_layouts = [];
    if ($layout_restriction == 'restricted') {
      $allowed_layouts = array_keys(array_filter($form_state->getValue([
        'layout_builder_restrictions',
        'allowed_layouts',
        'layouts',
      ])));
    }
    $display->setThirdPartySetting('layout_builder_restrictions', 'allowed_layouts', $allowed_layouts);
  }

}
