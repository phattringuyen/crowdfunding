<?php

namespace Drupal\cool_examples\Controllers\BlockControllers;

class CurrentTimeDynamicBlock extends \Drupal\cool\BaseDynamicBlock {

  public static function getAdminTitle($delta = '') {
    return 'Cool Block - Admin title (' . $delta . ')';
  }

  public static function getTypeName($delta = '') {
    return 'Cool Dynamic Block';
  }

  static public function getDefinition($delta = '') {
    return array(
      'cache' => DRUPAL_CACHE_GLOBAL,
    );
  }

  public static function getConfiguration($delta = '') {
    if (!empty($delta)) {
      $cool_dynblocks = cool_get_dynamic_blocks();
    }
    $form = array();
    $form['container'] = array(
      '#type' => 'fieldset',
      '#title' => t('"@title" specific configurations', array('@title' => self::getTypeName()))
    );
    $form['container']['date_format'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => 'Date format',
      '#description' => 'The date format to use when showing the date',
    );
    if (empty($delta)) {
      $form['container']['date_format']['#default_value'] = 'Y-m-d';
    }
    elseif (isset($cool_dynblocks[$delta])) {
      $form['container']['date_format']['#default_value'] = $cool_dynblocks[$delta]['values']['date_format'];
    }
    return $form;
  }

  public static function getSubject($delta = '') {
    return 'Block title';
  }

  public static function getContent($delta = '') {
    $date_format = variable_get('cool_example_current_time_block_date_format', 'Y-m-d');
    return 'The current time is ' . date($date_format, time());
  }
}
