<?php

namespace Drupal\crowdfunding\ViewModes;

class CrowdfundingProject {

  static $node;

  /**
   * @var \Drupal\crowdfunding\Model\Project
   */
  static $Project;

  static public function viewModeFull($node, $view_mode, $langcode) {

    self::$node = $node;
    self::$Project = \Drupal\crowdfunding\Model\Project::create($node->nid);

    drupal_add_css(drupal_get_path('module', 'crowdfunding') . '/assets/stylesheets/crowdfunding.css');

    self::addResultsBlock();

    self::addRewardOptionsBlock();

    return self::$node;
  }

  static public function addResultsBlock() {
    $time_remaining = \Drupal\crowdfunding\DateUtils::formatTimeRemainingString(
      self::$node->funding_end - REQUEST_TIME
    );
    self::$node->content['crowdfunding_results_node_view'] = array(
      '#markup' => theme('crowdfunding_results_node_view', array(
        'backers_amount' => self::$node->backers_amount,
        'goal' => commerce_currency_format(self::$node->goal, commerce_default_currency()),
        'funded' => commerce_currency_format(self::$node->funded, commerce_default_currency()),
        'time_remaining' => $time_remaining['amount'],
        'time_remaining_text' => $time_remaining['text'],
      )),
      '#weight' => 12,
    );
  }

  static public function addRewardOptionsBlock() {
    $rewards = self::$Project->getRewardOptions();
    $reward_options = array();
    foreach ($rewards as $Reward) {
      $price = $Reward->getPrice();

      // Needs to improve
      if (TRUE) {
        $pledge_url = 'crowdfunding/pledge/' . self::$Project->getIdentifier() . '/' . $Reward->getIdentifier();
        $text = l($Reward->getText(), $pledge_url);
      }
      else {
        $text = $Reward->getText();
      }
      $reward_options[$price['amount']] = array(
        'product_id' => $Reward->getIdentifier(),
        'text' => $text,
        'price' => commerce_currency_format($price['amount'], $price['currency_code']),
      );
    }

    ksort($reward_options);

    self::$node->content['crowdfunding_reward_options_node_view'] = array(
      '#markup' => theme('crowdfunding_reward_options_node_view', array(
        'rewards' => $reward_options
      )),
      '#weight' => 13,
    );
  }
}