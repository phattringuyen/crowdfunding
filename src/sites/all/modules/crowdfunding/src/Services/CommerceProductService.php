<?php

namespace Drupal\crowdfunding\Services;

/**
 * @file
 */
class CommerceProductService {

  /**
   * Create a "crowdfunding_no_reward" product for the specified project.
   * This type of product should only be created here(not via UI).
   *
   * @param int $nid
   * @param string $project_title
   * @return type
   */
  static public function createPledgeWithoutRewardForProject($nid, $project_title) {
    $form_state = array();
    $form = array();
    $form['#parents'] = array();
    $new_product = commerce_product_new('crowdfunding_no_reward');
    $new_product->status = 1;
    $new_product->uid = 1;
    $new_product->sku = $nid . '_no_reward';
    $new_product->title = t('Pledge without reward for project @title', array('@title' => $project_title));
    $new_product->created = $new_product->changed = time();
    $new_product->field_crowdfunding_project_ref['und'][0]['target_id'] = $nid;
    $price = array(
      LANGUAGE_NONE => array(
        0 => array(
          'amount' => 0,
          'currency_code' => commerce_default_currency()
        )
      )
    );
    $form_state['values']['commerce_price'] = $price;
    // Notify field widgets to save their field data
    field_attach_submit('commerce_product', $new_product, $form, $form_state);
    commerce_product_save($new_product);
    return $new_product;
  }
}