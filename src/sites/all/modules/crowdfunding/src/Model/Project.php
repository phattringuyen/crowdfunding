<?php

namespace Drupal\crowdfunding\Model;

/**
 * @file
 */
class Project extends \EntityDrupalWrapper {

  static public function create($data = NULL) {
    if (is_object($data)) {
      return new Project($data->nid);
    }
    else {
      return new Project($data);
    }
  }

  public function __construct($nid = NULL, $info = array()) {
    parent::__construct('node', $nid, $info);
  }

  public function getTitle() {
    return $this->title->value();
  }

  public function getAuthor() {
    return $this->author->value();
  }

  public function getRewardOptions($status = NULL) {

    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'commerce_product')
      ->entityCondition('bundle', 'crowdfunding_reward')
      ->fieldCondition('field_crowdfunding_project_ref', 'target_id', $this->id)
      ->addMetaData('account', user_load(1));
    if (isset($status)) {
      $query->propertyCondition('status', $status);
    }
    $result = $query->execute();

    $rewards = array();
    if (isset($result['commerce_product'])) {
      foreach (array_keys($result['commerce_product']) as $product_id) {
        $rewards[] = Reward::create($product_id);
      }
    }
    return $rewards;
  }

  /**
   * Return product_id from the product associated with this project that
   * represents a pledge without reward
   * @return int
   */
  public function getPledgeWithoutRewardProduct() {
    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'commerce_product')
      ->entityCondition('bundle', 'crowdfunding_no_reward')
      ->fieldCondition('field_crowdfunding_project_ref', 'target_id', $this->getIdentifier())
      ->addMetaData('account', user_load(1));
    $result = $query->execute();

    if (empty($result['commerce_product'])) {
      return \Drupal\crowdfunding\Services\CommerceProductService::createPledgeWithoutRewardForProject(
        $this->getIdentifier(), $this->getTitle()
      )->product_id;
    }
    else {
      return current($result['commerce_product'])->product_id;
    }
  }

  public function getBackersAmountPerRewardOption() {
    $map = array(
      'pledges' => array(),
      'total' => 0
    );
    $noRewardProductNid = $this->getPledgeWithoutRewardProduct();
    $noRewardProduct = Reward::create($noRewardProductNid, $this);
    $amount = $noRewardProduct->getValidContributionsAmount();
    $map['pledges'][] = array(t('Contribution without reward'), $amount);
    $map['total'] += $amount;

    $rewards = $this->getRewardOptions();
    foreach ($rewards as $RewardProduct) {
      $amount = $RewardProduct->getValidContributionsAmount();
      $map['pledges'][] = array($RewardProduct->title->value(), $amount);
      $map['total'] += $amount;
    }
    return $map;
  }

  public function getBackersAmount() {
    return $this->data->backers_amount;
  }

  public function getFundingStartDate() {
    return $this->data->funding_start;
  }

  public function getFundingEndDate() {
    if (is_object($this->data)) {
      return $this->data->funding_end;
    }
  }

  public function getElapsedTime() {
    return REQUEST_TIME - $this->getFundingStartDate();
  }

  public function getRemainingTime() {
    return $this->getFundingEndDate() - REQUEST_TIME;
  }

  public function getGoal() {
    return $this->data->goal;
  }

  public function getFunded() {
    return $this->data->funded;
  }
}