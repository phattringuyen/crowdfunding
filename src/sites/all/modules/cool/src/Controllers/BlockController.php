<?php

namespace Drupal\cool\Controllers;

interface BlockController {

  /**
   * Path to be used by hook_info().
   */
  static public function getId();

  /**
   * Passed to hook_block_info().
   */
  static public function getAdminTitle($delta);

  /**
   * Passed to hook_block_info().
   */
  static public function getDefinition($delta);

  /**
   * Passed to hook_block_info().
   */
  static public function getConfiguration($delta);

  /**
   * Passed to hook_block_save().
   */
  static public function saveConfiguration($edit, $delta);

  /**
   * Passed to hook_block_view().
   */
  static public function getSubject($delta);

  /**
   * Passed to hook_block_view().
   */
  static public function getContent($delta);
}
