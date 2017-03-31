<?php

use \Drupal\cool\Loader;

/**
 * Implements hook_menu_local_tasks_alter().
 */
function cool_menu_local_tasks_alter(&$data, $router_item, $root_path) {
  if ($router_item['path'] == 'admin/structure/block') {
    $classes = \Drupal\cool\Loader::mapImplementationsAvailable('BlockControllers', '\Drupal\cool\Controllers\BlockController');
    unset($classes['Drupal\\cool\\BaseBlock']);
    unset($classes['Drupal\\cool\\BaseDynamicBlock']);
    foreach ($classes as $class_name) {
      $isDynamicBlock = Loader::classIsSubclassOf($class_name, '\Drupal\cool\BaseDynamicBlock');
      if ($isDynamicBlock) {
        $new_item = $data['actions']['output'][0];
        $new_item['#link']['title'] = 'Add "' . $class_name::getTypeName() . '" block';
        $new_item['#link']['href'] = 'admin/structure/block/add';
        $new_item['#link']['localized_options']['query'] = array(
          'type' => $class_name,
        );
        $data['actions']['output'][] = $new_item;
      }
    }
  }
}

/**
 * Implements hook_form_FORM_ID_alter()
 */
function cool_form_block_add_block_form_alter(&$form, &$form_state) {
  $cool_dynamicblock_type = $_GET['type'];
  $classes = \Drupal\cool\Loader::mapImplementationsAvailable('BlockControllers', '\Drupal\cool\Controllers\BlockController');
  unset($classes['Drupal\\cool\\BaseBlock']);
  unset($classes['Drupal\\cool\\BaseDynamicBlock']);
  foreach ($classes as $class_name) {
    $isDynamicBlock = Loader::classIsSubclassOf($class_name, '\Drupal\cool\BaseDynamicBlock');
    if ($isDynamicBlock && ($cool_dynamicblock_type == $class_name)) {
      drupal_set_title(t('Blocks - add new @type', array('@type' => $class_name::getTypeName())));
      $form['settings']['info']['#description'] = t('You can use whatever you wish here or keep "cool_dynblock_title", that will be converted dinamically to the format "@format"', array('@format' => $class_name::getAdminTitle('[block_delta]')))
        . '<br/>' . $form['settings']['info']['#description'];
      $form['settings']['info']['#default_value'] = 'cool_dynblock_title';
      $form['settings']['body_field']['body']['#required'] = FALSE;
      $configuration_form = $class_name::getConfiguration();
      $form['settings'][] = $configuration_form;
      //        $form['#validate'][] = 'cool_form_block_add_block_form_validate';
      $form['#submit'][] = 'cool_form_block_add_block_form_submit';
    }
  }
}

function cool_form_block_add_block_form_validate($form, &$form_state) {
  //  TODO: brings the configuration form validation here
}

function cool_form_block_add_block_form_submit($form, &$form_state) {
  $cool_dynamicblock_type = $_GET['type'];
  $delta = $form_state['values']['delta'];

  // Get the configuration items to automatically get their values for this block
  $configuration_form = $cool_dynamicblock_type::getConfiguration($delta);
  $cool_dynamicblock_values = array();
  $cool_dynamicblock_items = array_diff(
    array_keys($configuration_form['container']),
    array('#type', '#title')
  );

  foreach ($cool_dynamicblock_items as $item) {
    $cool_dynamicblock_values[$item] = $form_state['values'][$item];
  }

  $cool_dynamicblock_type::saveConfiguration($cool_dynamicblock_values, $delta);

  if ($form_state['values']['info'] == 'cool_dynblock_title') {
    $block_custom_data = block_custom_block_get($delta);
    $edit = array();
    $edit['info'] = $cool_dynamicblock_type::getAdminTitle($delta);
    $edit['body'] = array(
      'value' => $block_custom_data['body'],
      'format' => $block_custom_data['format']
    );
    block_custom_block_save($edit, $delta);
  }
}

/**
 * Implements hook_form_FORM_ID_alter()
 */
function cool_form_block_admin_configure_alter(&$form, &$form_state) {
  $cool_dynblocks = cool_get_dynamic_blocks();
  if (isset($cool_dynblocks[$form['delta']['#value']])) {
    $cool_dynamicblock_type = $cool_dynblocks[$form['delta']['#value']]['class'];
    $configuration_items = $cool_dynamicblock_type::getConfiguration($form['delta']['#value']);
    foreach ($configuration_items as $key => $item) {
      $form['settings'][$key] = $item;
    }
    $form['settings']['body_field']['body']['#required'] = FALSE;
    $form['#submit'][] = 'cool_form_block_admin_configure_submit';
  }
}

/**
 * Implements hook_form_FORM_ID_alter()
 */
function cool_form_block_admin_configure_submit(&$form, &$form_state) {
  $cool_dynblocks = cool_get_dynamic_blocks();
  $cool_dynamicblock_type = $cool_dynblocks[$form['delta']['#value']]['class'];

  // Get the configuration items to automatically get their values for this block
  $configuration_form = $cool_dynamicblock_type::getConfiguration($form['delta']['#value']);
  $cool_dynamicblock_values = array();
  $cool_dynamicblock_items = array_diff(
    array_keys($configuration_form['container']),
    array('#type', '#title')
  );

  foreach ($cool_dynamicblock_items as $item) {
    $cool_dynamicblock_values[$item] = $form_state['values'][$item];
  }

  $cool_dynamicblock_type::saveConfiguration($cool_dynamicblock_values, $form['delta']['#value']);
}

function cool_get_dynamic_blocks() {
  return unserialize(variable_get('cool_dynblocks', serialize(array())));
}