<?php

namespace Drupal\cool_examples\Controllers\BlockControllers;

class CurrentTimeBlock implements \Drupal\cool\Controllers\BlockController {

  public static function getId() {
    return 'cool_example_current_time_block';
  }

  public static function getAdminTitle($delta = '') {
    return 'Cool Block - Admin title';
  }

  public static function getTypeName($delta = '') {
    return self::getAdminTitle();
  }

  static public function getDefinition($delta = '') {
    return array(
      'cache' => DRUPAL_CACHE_GLOBAL,
    );
  }

  public static function getConfiguration($delta = '') {
    $form = array();
    $form['container'] = array(
      '#type' => 'fieldset',
      '#title' => t('@title specific configurations', array('@title' => self::getAdminTitle()))
    );
    $form['container']['date_format'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => variable_get('cool_example_current_time_block_date_format', 'Y-m-d'),
      '#title' => 'Date format',
      '#description' => 'The date format to use when showing the date',
    );
    return $form;
  }

  public static function saveConfiguration($edit, $delta = '') {
    variable_set('cool_example_current_time_block_date_format', $edit['date_format']);
  }

  public static function getSubject($delta = '') {
    return 'Block title';
  }

  public static function getContent($delta = '') {
    $date_format = variable_get('cool_example_current_time_block_date_format', 'Y-m-d');
    return 'The current time is ' . date($date_format, time());
  }
}
