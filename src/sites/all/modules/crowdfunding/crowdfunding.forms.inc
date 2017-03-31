<?php

function crowdfunding_form_crowdfunding_project_node_form_alter(&$form, &$form_state) {
  \Drupal\crowdfunding\FormAlter\CrowdfundingProjectNode::alter($form, $form_state);
}

function crowdfunding_project_node_alter_validate(&$form, &$form_state) {
  \Drupal\crowdfunding\FormAlter\CrowdfundingProjectNode::validate($form, $form_state);
}

function crowdfunding_project_node_alter_submit(&$form, &$form_state) {
  \Drupal\crowdfunding\FormAlter\CrowdfundingProjectNode::submit($form, $form_state);
}

// Customize rewards form
function crowdfunding_form_commerce_product_ui_product_form_alter($form, &$form_state) {
  switch ($form['#entity']->type) {
    case 'crowdfunding_reward':
      if (isset($_GET['field_crowdfunding_project_ref']) && $project = node_load($_GET['field_crowdfunding_project_ref'])) {
        drupal_set_title(t('[@title] Add reward option', array('@title' => $project->title)));
      }
      break;
    case 'crowdfunding_no_reward':
      if (isset($_GET['field_crowdfunding_project_ref']) && $project = node_load($_GET['field_crowdfunding_project_ref'])) {
        drupal_set_title(t('[@title] Add pledge without reward ', array('@title' => $project->title)));
      }
      break;
  }
}
