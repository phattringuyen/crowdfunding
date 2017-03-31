<?php

namespace Drupal\crowdfunding\PageControllers;

class PledgeSelection implements \Drupal\cool\Controllers\PageController {

  /**
   * Path to be used by hook_menu().
   */
  static public function getPath() {
    return 'crowdfunding/pledge/%';
  }

  /**
   * Passed to hook_menu()
   */
  static public function getDefinition() {
    return array();
  }

  public static function pageCallback() {
    if (user_is_anonymous()) {
      drupal_goto('user', array('query' => array(drupal_get_destination())));
    }
    $project_id = arg(2);
    $form = drupal_get_form('crowdfunding_backing_selection_form', $project_id);
    return render($form);
  }

  public static function accessCallback() {
    return TRUE;
  }
}