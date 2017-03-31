<?php

namespace Drupal\crowdfunding\FormControllers;

class PledgeSelection extends \Drupal\cool\BaseForm {

  static private $order;
  static private $order_wrapper;

  /**
   * @var \Drupal\crowdfunding\Model\Project
   */
  static private $Project;

  static public function getId() {
    return 'crowdfunding_backing_selection_form';
  }

  static public function build() {
    $form = parent::build();

    $args = func_get_args();
    $project_id = $args[2];
    self::$Project = \Drupal\crowdfunding\Model\Project::create($project_id);

    drupal_set_title(self::$Project->getTitle());

    $form['#prefix'] = t('project by ') . theme('username', array('account' => self::$Project->getAuthor()))
      . ' / ' . t('back to ') . l(t('project page'), 'node/' . $project_id);

    $pre_selected_option = arg(3);
    $reward_options = array();

    foreach (self::$Project->getRewardOptions() as $Reward) {
      $price = $Reward->getPrice();
      $reward_options[$price['amount']] = array(
        'product_id' => $Reward->getIdentifier(),
        'price' => commerce_currency_amount_to_decimal($price['amount'], $price['currency_code']),
        'text' => $Reward->getText()
      );
      if (!empty($pre_selected_option) && $pre_selected_option == $Reward->getIdentifier()) {
        $default_reward_option = $Reward->getIdentifier();
        $pledge_default_value = commerce_currency_amount_to_decimal($price['amount'], $price['currency_code']);
      }
    }

    ksort($reward_options);
    $sorted_reward_options = array();

    $reward = self::$Project->getPledgeWithoutRewardProduct();
    $sorted_reward_options[$reward] = t('No thanks, I just want to help the project.');
    if (!empty($pre_selected_option) && $pre_selected_option == $reward) {
      $default_reward_option = $reward;
    }

    foreach ($reward_options as $reward) {
      $sorted_reward_options[$reward['product_id']] = $reward['price'] . '+';
    }

    $form['project_nid'] = array(
      '#type' => 'hidden',
      '#value' => arg(2)
    );
    $minimum_pledge = commerce_currency_format(1000, commerce_default_currency());
    $form['pledge'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter your pledge amount'),
      '#description' => t("It's up to you. Any amount of @minimum or more.", array('@minimum' => $minimum_pledge)),
      '#default_value' => !empty($pledge_default_value) ? $pledge_default_value : '',
    );
    $form['reward'] = array(
      '#type' => 'radios',
      '#title' => t('Select your reward'),
      '#options' => $sorted_reward_options,
      '#default_value' => !empty($default_reward_option) ? $default_reward_option : 0,
    );
    foreach ($reward_options as $reward) {
      $form['reward'][$reward['product_id']]['#attributes'] = array(
        'data-product-id' => $reward['product_id'],
        'data-text' => json_encode($reward['text'])
      );
    }
    return $form;
  }

  public static function validate($form, &$form_state) {
  }

  public static function submit($form, &$form_state) {
    try {

      $_SESSION['crowdfunding']['project_nid_current_contribution'] = $form_state['values']['project_nid'];

      global $user;
      self::createBackingOrder($user->uid, arg(2));

      $pledge_amount = self::formatPledgeAmount(
        $form_state['values']['pledge']
      );
      $Reward = \Drupal\crowdfunding\Model\Reward::create($form_state['values']['reward']);
      $price = $Reward->getPrice();
      $reward_price = $price['amount'];

      if ($pledge_amount > $reward_price) {
        self::addPledge($form_state['values']['reward'], $pledge_amount);
      }
      else {
        self::addPledge($form_state['values']['reward'], $reward_price);
      }

      self::save();
      self::redirectToCheckout();
    } catch (\Exception $e) {
      watchdog('fatal_error', 'Error on addPledge: ' . $e->getMessage());
      drupal_set_message(t(CROWDFUNDING_DEFAULT_ERROR_MESSAGE), 'error');
      return FALSE;
    }
  }

  private static function formatPledgeAmount($amount) {
    $pledge_amount = str_replace(',', '.', $amount);

    $dots = [];
    $lastPos = 0;
    while (($lastPos = strpos($pledge_amount, '.', $lastPos)) !== FALSE) {
      $dots[] = $lastPos;
      $lastPos = $lastPos + strlen('.');
    }

    if (count($dots) === 0) {
      $pledge_amount = number_format($pledge_amount, 2, '.', '');
    }
    else {
      for ($i = 0; $i < count($dots) - 1; $i++) {
        $pledge_amount = substr($pledge_amount, 0, ($dots[$i] - $i))
          . substr($pledge_amount, $dots[$i] - $i + 1);
      }
    };

    return $pledge_amount * 100;
  }

  private static function createBackingOrder($uid, $project_id, $status = 'checkout_checkout') {
    self::$order = commerce_order_new($uid, $status);
    self::$order_wrapper = entity_metadata_wrapper('commerce_order', self::$order);
    self::$order_wrapper->field_crowdfunding_project_ref = node_load($project_id);
  }

  public static function addPledge($product_id, $price = NULL) {
    $product = commerce_product_load($product_id);
    $line_item = commerce_product_line_item_new($product, 1, self::$order->order_id, array(), 'crowdfunding_pledge');
    $line_item_wrapper = entity_metadata_wrapper('commerce_line_item', $line_item);
    $line_item_wrapper->field_pledged_amount->amount = $price;
    $line_item_wrapper->commerce_unit_price->amount = $price;
    $line_item_wrapper->commerce_total->amount = $price;
    $line_item_wrapper->save();
    self::$order_wrapper->commerce_line_items[] = $line_item;
  }

  public static function save() {
    return self::$order_wrapper->save();
  }

  public static function redirectToCheckout() {
    drupal_goto('checkout/' . self::$order->order_id . '/checkout');
  }
}