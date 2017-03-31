<?php

namespace Drupal\crowdfunding\Model;

/**
 * @file
 */
class Backer extends \EntityDrupalWrapper {

  public function __construct($data = NULL, $info = array()) {
    parent::__construct('user', $data, $info);
  }
}