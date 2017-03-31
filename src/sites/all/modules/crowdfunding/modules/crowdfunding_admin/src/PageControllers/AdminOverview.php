<?php

namespace Drupal\crowdfunding_admin\PageControllers;

class AdminOverview implements \Drupal\cool\Controllers\PageController {

  static public function getPath() {
    return 'admin/crowdfunding';
  }

  static public function getDefinition() {
    return array(
      'title' => t('Crowdfunding'),
    );
  }

  public static function pageCallback() {
    $items['items'] = array(
      l('Crowdfunding Running Projects', 'admin/crowdfunding/running'),
      l('Crowdfunding Contributions', 'admin/crowdfunding/contributions'),
      l('Crowdfunding Projects', 'admin/crowdfunding/project'),
      l('Crowdfunding Rewards', 'admin/crowdfunding/rewards'),
      l('Crowdfunding Backers', 'admin/crowdfunding/backers'),
    );
    return theme('item_list', $items);
  }

  public static function accessCallback() {
    return user_access('Crowdfunding admin');
  }

}
