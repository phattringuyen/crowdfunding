<?php

namespace Drupal\crowdfunding\Model;

/**
 * @file
 */
class Contribution extends \EntityDrupalWrapper {

  private $total_amount = 0;

  public function __construct($data = NULL, $info = array()) {
    parent::__construct('commerce_order', $data, $info);
  }

  public function getProject() {
    return $this->field_crowdfunding_project_ref->value();
  }

  /**
   * Return the statuses considered as "valid" contributions
   * @return array
   */
  static public function getValidStatuses() {
    return array(
      CROWDFUNDING_CONTRIBUTION_PROCESSING,
      CROWDFUNDING_CONTRIBUTION_AUTHORIZED,
      CROWDFUNDING_CONTRIBUTION_COMPLETED,
    );
  }

  /**
   * Look a Commerce Order history to check which type of change was the last one,
   * to figure out if it came from complete to canceled, pending to complete, etc
   *
   * @param int $order_id
   * @param int $order_status
   * @return int 1 for new contribution to count or -1 for contribution to remove from count
   * @throws Exception
   */
  static public function identifyStatusChange($order_id, $order_status) {
    $last_status = Contribution::getLastStatus($order_id);
    if ($last_status) {
      switch ($last_status) {
        case CROWDFUNDING_CONTRIBUTION_PROCESSING:
        case CROWDFUNDING_CONTRIBUTION_AUTHORIZED:
        case CROWDFUNDING_CONTRIBUTION_COMPLETED:
          if (!in_array($order_status, Contribution::getValidStatuses())) {
            return -1;
          }
          break;

        default:
          if (in_array($order_status, Contribution::getValidStatuses())) {
            return 1;
          }
      }
    }
    else {
      if (in_array($order_status, Contribution::getValidStatuses())) {
        return 1;
      }
      else {
        return FALSE;
      }
    }
  }

  /**
   * Look at a Commerce Order history to get the penultimate status
   */
  static public function getLastStatus($order_id) {
    $result = db_query('SELECT c.status 
          FROM {commerce_order_revision} c
          WHERE c.order_id = :order_id
          ORDER BY c.revision_id DESC LIMIT 1 OFFSET 1', array(':order_id' => $order_id))->fetchCol();
    return count($result) > 0 ? $result[0] : FALSE;
  }
}