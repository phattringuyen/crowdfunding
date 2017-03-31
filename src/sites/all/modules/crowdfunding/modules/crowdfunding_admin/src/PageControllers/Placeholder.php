<?php

namespace Drupal\crowdfunding_admin\PageControllers;

class Placeholder implements \Drupal\cool\Controllers\PageController {

  static public function getPath() {
    return 'admin/crowdfunding/overview';
  }

  static public function getDefinition() {
    return array(
      'title' => t('Overview'),
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => -10,
    );
  }

  public static function pageCallback() {
    /* Because Drupal needs this to make the menu hierarchy with LOCAL_TASKS */
  }

  public static function accessCallback() {
    return user_access('Crowdfunding admin');
  }

}
