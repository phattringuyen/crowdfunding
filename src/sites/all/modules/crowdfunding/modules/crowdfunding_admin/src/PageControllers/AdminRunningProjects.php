<?php

namespace Drupal\crowdfunding_admin\PageControllers;

class AdminRunningProjects implements \Drupal\cool\Controllers\PageController {

  static public function getPath() {
    return 'admin/crowdfunding/running';
  }

  static public function getDefinition() {
    return array(
      'title' => t('Projects running'),
      'type' => MENU_LOCAL_TASK,
      'weight' => -9,
    );
  }

  public static function pageCallback() {
    $header = array(
      '#',
      t('Title'),
      t('Elapsed time'),
      t('Remaining time'),
      t('Goal'),
      t('Funded'),
      t('Backers amount'),
      t('Author'),
    );
    $projects = array();

    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'crowdfunding_project')
      ->addMetaData('account', user_load(1)); // Run the query as user 1.

    $result = $query->execute();

    $i = 1;
    foreach ($result['node'] as $row) {
      $Project = \Drupal\crowdfunding\Model\Project::create($row->nid);
      // Index projects by date, adding 1 in timestamp value to avoid conflicts
      $projects[$Project->getFundingEndDate() + $i++] = array(
        l($Project->title->value(), 'node/' . $Project->nid->value())
        . ' (' . l('overview', 'node/' . $Project->nid->value() . '/running') . ')',
        format_interval($Project->getElapsedTime()),
        format_interval($Project->getRemainingTime()),
        $Project->getGoal(),
        $Project->getFunded() . ' (' . ($Project->getFunded() / $Project->getGoal()) . '%)',
        $Project->getBackersAmount(),
        theme('username', array('account' => $Project->author->value())),
      );
    }

    ksort($projects);

    /*
     * Append a numeric index on the table (needs to be done here, because of 
     * the ksort above
     */
    $amount = 1;
    foreach ($projects as $key => $project) {
      array_unshift($projects[$key], $amount++);
    }
    return theme('table', array('header' => $header, 'rows' => $projects));
  }

  public static function accessCallback() {
    return user_access('Crowdfunding admin');
  }

}
