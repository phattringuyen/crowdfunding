<?php

namespace Drupal\crowdfunding\FormAlter;

class CrowdfundingProjectNode {

  static public function alter(&$form, &$form_state) {

    if (isset($form['#node']->funding_start)) {
      $funding_start = array(
        'month' => date('n', $form['#node']->funding_start),
        'day' => date('d', $form['#node']->funding_start),
        'year' => date('Y', $form['#node']->funding_start),
      );
    }
    else {
      $funding_start = array(
        'month' => date('n', time()),
        'day' => date('d', time()),
        'year' => date('Y', time()),
      );
    }
    if (isset($form['#node']->funding_end)) {
      $funding_end = array(
        'month' => date('n', $form['#node']->funding_end),
        'day' => date('d', $form['#node']->funding_end),
        'year' => date('Y', $form['#node']->funding_end),
      );
    }
    else {
      $funding_end = array(
        'month' => date('n', time()),
        'day' => date('d', time()),
        'year' => date('Y', time()),
      );
    }

    $form['crowdfunding'] = array(
      '#type' => 'fieldset',
      '#title' => t('Crowdfunding process information'),
    );
    $form['crowdfunding']['goal'] = array(
      '#type' => 'textfield',
      '#title' => t('Funding goal'),
      '#default_value' => isset($form['#node']->goal) ? $form['#node']->goal : '',
      '#required' => TRUE
    );
    $form['crowdfunding']['funding_start'] = array(
      '#type' => 'date',
      '#title' => t('Funding period start'),
      '#default_value' => array(
        'year' => $funding_start['year'],
        'month' => $funding_start['month'],
        'day' => $funding_start['day']
      ),
      '#required' => TRUE
    );
    $form['crowdfunding']['funding_end'] = array(
      '#type' => 'date',
      '#title' => t('Funding period end'),
      '#default_value' => array(
        'year' => $funding_end['year'],
        'month' => $funding_end['month'],
        'day' => $funding_end['day']
      ),
      '#required' => TRUE
    );
    $form['#validate'][] = 'crowdfunding_project_node_alter_validate';
    $form['#submit'][] = 'crowdfunding_project_node_alter_submit';
  }

  static public function validate(&$form, &$form_state) {

    $funding_start = mktime(0, 0, 0, $form_state['values']['funding_start']['month'], $form_state['values']['funding_start']['day'], $form_state['values']['funding_start']['year']);
    $funding_end = mktime(0, 0, 0, $form_state['values']['funding_end']['month'], $form_state['values']['funding_end']['day'], $form_state['values']['funding_end']['year']);
    if ($funding_start > $funding_end) {
      form_set_error('funding_start', t('The funding period start should happen before the funding period end.'));
      form_set_error('funding_end');
    }
    // we check if Goal is a positive number
    $goal = $form_state['values']['goal'];
    if (!preg_match('/^[1-9][0-9]*$/', $goal)) {
      form_set_error('goal', t('The goal must be a positive numeric value.'));
    }
  }

  static public function submit(&$form, &$form_state) {
    /*
     * If editing the node, Crowdfunding data will be updated now, otherwise it
     * will need to be processed on hook_node_insert(), so that the node had
     * already being created
     */
    if (isset($form['#node']->nid)) {
      $num_updated = db_update('crowdfunding_project')
        ->fields(array(
          'goal' => $form_state['values']['goal'],
          'funding_start' => mktime(0, 0, 0, $form_state['values']['funding_start']['month'], $form_state['values']['funding_start']['day'], $form_state['values']['funding_start']['year']),
          'funding_end' => mktime(0, 0, 0, $form_state['values']['funding_end']['month'], $form_state['values']['funding_end']['day'], $form_state['values']['funding_end']['year']),
        ))
        ->condition('nid', $form['#node']->nid)
        ->execute();
      // "db_update" returns -1 on failure, as affected rows can be zero if 
      // none of the colums was actually updated
      if ($num_updated < 0) {
        throw new \Exception('Node ' . $form['#node']->nid . ' gives error on updating crowdfunding project data. Please contact site administrators.');
      }
    }
  }

}
