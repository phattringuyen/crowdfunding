<?php

/**
 * @file
 * crowdfunding.rules_defaults.inc
 */

/**
 * Implements hook_default_rules_configuration().
 */
function crowdfunding_default_rules_configuration() {
  $items = array();
  $items['rules_update_line_item_amount_when_a_pledge_has_a_custom_amount'] = entity_import('rules_config', '{ "rules_update_line_item_amount_when_a_pledge_has_a_custom_amount" : {
      "LABEL" : "Update line item amount when a pledge has a custom amount",
      "PLUGIN" : "reaction rule",
      "OWNER" : "rules",
      "REQUIRES" : [ "rules", "entity" ],
      "TAGS" : [ "Crowdfunding" ],
      "ON" : { "commerce_order_presave" : [] },
      "IF" : [
        { "entity_has_field" : {
            "entity" : [ "commerce-order:commerce-line-items:0" ],
            "field" : "field_pledged_amount"
          }
        }
      ],
      "DO" : [
        { "data_set" : {
            "data" : [ "commerce-order:commerce-order-total:amount" ],
            "value" : [ "commerce-order:commerce-line-items:0:field-pledged-amount:amount" ]
          }
        }
      ]
    }
  }');
  return $items;
}