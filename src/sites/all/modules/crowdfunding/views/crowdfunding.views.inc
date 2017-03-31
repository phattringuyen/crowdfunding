<?php

/**
 * Implements hook_views_data().
 */
function crowdfunding_views_data() {

  // ----------------------------------------------------------------
  // crowdfunding_project table -- basic table information.
  // Define the base group of this table. Fields that don't
  // have a group defined will go into this field by default.

  $data['crowdfunding_project']['table']['group'] = t('Crowdfunding Project');

  // Advertise this table as a possible base table
  $data['crowdfunding_project']['table']['base'] = array(
    'field' => 'nid',
    'title' => t('Crowdfunding Project'),
    'weight' => -10,
    'access query tag' => 'node_access',
    'defaults' => array(
      'field' => 'nid',
    ),
  );

  // For other base tables, explain how we join
  $data['crowdfunding_project']['table']['join'] = array(
    // this explains how the 'node' table (named in the line above)
    // links toward the node_revision table.
    'node_revision' => array(
      'left_field' => 'nid',
      'field' => 'nid',
      // also supported:
      // 'type' => 'INNER',
      // 'extra' => array(array('field' => 'fieldname', 'value' => 'value', 'operator' => '='))
      //   Unfortunately, you can't specify other tables here, but you can construct
      //   alternative joins in the handlers that can do that.
      // 'table' => 'the actual name of this table in the database',
    ),
  );

  // ----------------------------------------------------------------
  // crowdfunding_project table -- fields
  // nid
  $data['crowdfunding_project']['nid'] = array(
    'title' => t('Crowdfunding Nid'),
    'help' => t('The node ID of the crowdfunding project.'),
    // The help that appears on the UI,
    // Information for displaying the nid
    'field' => array(
      'handler' => 'views_handler_field_node',
      'click sortable' => TRUE,
    ),
    // Information for accepting a nid as an argument
    'argument' => array(
      'handler' => 'views_handler_argument_node_nid',
      'name field' => 'title', // the field to display in the summary.
      'numeric' => TRUE,
      'validate type' => 'nid',
    ),
    // Information for accepting a nid as a filter
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    // Information for sorting on a nid.
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  // Goal field
  $data['crowdfunding_project']['goal'] = array(
    'title' => t('Project Goal'),
    // The item it appears as on the UI,
    'help' => t('The goal of the crowdfunding project.'),
    // The help that appears on the UI,
    'field' => array(
      'handler' => 'views_handler_field_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
  );

  // Funded field
  $data['crowdfunding_project']['funded'] = array(
    'title' => t('Project funded value'),
    // The item it appears as on the UI,
    'help' => t('The funded value of the crowdfunding project.'),
    // The help that appears on the UI,
    'field' => array(
      'handler' => 'views_handler_field_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
  );

  // The Funding start date
  $data['crowdfunding_project']['funding_start'] = array(
    'title' => t('Funding start date'),
    'help' => t('The Project funding start date.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
    ),
  );

  // The Funding end date
  $data['crowdfunding_project']['funding_end'] = array(
    'title' => t('Funding end date'),
    'help' => t('The Project funding end date.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date',
    ),
  );

  return $data;
}