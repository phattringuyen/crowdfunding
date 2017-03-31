<?php

namespace Drupal\cool;

abstract class BaseBlock implements Controllers\BlockController {

  static public function getId() {
    throw new \Exception('You need to implement the getId() method');
  }

  static public function getAdminTitle($delta = '') {
    throw new \Exception('You need to implement the getAdminTitle() method');
  }

  static public function getDefinition($delta = '') {
    return array();
  }

  static public function getConfiguration($delta = '') {
    return array();
  }

  static public function saveConfiguration($edit, $delta = '') {
  }

  static public function getSubject($delta = '') {
    return '';
  }

  static public function getContent($delta = '') {
    throw new \Exception('You need to implement the getContent() method');
  }
}
