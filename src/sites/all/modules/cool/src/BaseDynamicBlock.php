<?php

namespace Drupal\cool;

abstract class BaseDynamicBlock implements Controllers\BlockController {

  static public function getId() {
    return '';
  }

  static public function getAdminTitle($delta = '') {
    throw new \Exception('You need to implement the getAdminTitle() method');
  }

  static public function getTypeName($delta = '') {
    return self::getAdminTitle($delta);
  }

  static public function getDefinition($delta = '') {
    return array();
  }

  static public function getConfiguration($delta = '') {
    return array();
  }

  static public function saveConfiguration($edit, $delta = '') {
    $cool_dynblocks = cool_get_dynamic_blocks();
    if (isset($_GET['type'])) {
      $cool_dynamicblock_type = $_GET['type'];
    }
    else {
      $cool_dynamicblock_type = $cool_dynblocks[$delta]['class'];
    }

    $cool_dynblocks[$delta] = array(
      'class' => $cool_dynamicblock_type,
      'values' => $edit
    );
    variable_set('cool_dynblocks', serialize($cool_dynblocks));
  }

  static public function getSubject($delta = '') {
    return '';
  }

  static public function getContent($delta = '') {
    throw new \Exception('You need to implement the getContent() method');
  }
}
