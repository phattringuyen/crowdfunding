<?php

namespace Drupal\crowdfunding_admin\PageControllers;

class AdminProjectOverview implements \Drupal\cool\Controllers\PageController {

  static public function getPath() {
    return 'node/%/running';
  }

  static public function getDefinition() {
    return array(
      'title' => t('Crowdfunding overview'),
      'type' => MENU_LOCAL_TASK,
      'weight' => 9,
    );
  }

  public static function pageCallback() {
    $nid = arg(1);
    $Project = \Drupal\crowdfunding\Model\Project::create($nid);
    
    drupal_set_title($Project->getTitle());

    $options = array('query' => drupal_get_destination());
    $reports = array();
    $reports[] = l('Rewards X Backers', 'admin/crowdfunding/projects/running/' . $Project->nid->value() . '/reports/pledges-backers/xls', $options);
    $reports[] = l('Backers personal data', 'admin/crowdfunding/projects/running/' . $Project->nid->value() . '/reports/backers/xls', $options);

    return theme('crowdfunding_admin_overview_page', array(
      'Project' => $Project,
      'reports' => $reports
    ));
  }

  public static function accessCallback() {
    // workaround with  node_load if not running page shows up in all content.
    $node = node_load(arg(1));
    if ($node->type == 'crowdfunding_project') {
      return user_access('Crowdfunding admin');
    }
  }

}
