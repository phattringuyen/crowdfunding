<?php

namespace Drupal\crowdfunding\Model;

class Reward extends \EntityDrupalWrapper {

  /**
   * @var \Drupal\crowdfunding\Model\Project
   */
  private $project;

  static public function create($data = NULL) {
    if (is_object($data)) {
      return new Reward($data->product_id);
    }
    else {
      return new Reward($data);
    }
  }

  public function __construct($data = NULL) {
    parent::__construct('commerce_product', $data, array());
  }

  public function getTitle() {
    return $this->title->value();
  }

  public function getText() {
    return $this->field_crowdfunding_text->value();
  }

  public function getPrice() {
    return $this->commerce_price->value();
  }

  /**
   * @return Project
   */
  public function getProject() {
    if (!isset($this->project)) {
      $this->project = $this->field_crowdfunding_project_ref->value();
    }
    return Project::create($this->project->nid);
  }

  public function getValidContributionsAmount() {
    return count($this->getValidContributions());
  }

  /**
   * List contributions(line items) related to this Reward Product
   * @return array
   */
  public function getValidContributions() {
    $query = db_select('field_data_commerce_product', 'lip')
      ->fields('li', array('line_item_id'))
      ->condition('o.status', array('completed', 'payment_authorized'))
      ->condition('lip.commerce_product_product_id', $this->product_id->value());
    $query->innerJoin('commerce_line_item', 'li', 'li.line_item_id = lip.entity_id');
    $query->innerJoin('field_data_commerce_line_items', 'fli', 'fli.commerce_line_items_line_item_id = li.line_item_id');
    $query->innerJoin('commerce_order', 'o', 'o.order_id = fli.entity_id');

    $itens = $query->execute()->fetchAll();
    $contributions = array();
    foreach ($itens as $item) {
      $contributions[] = $item;
    }
    return $contributions;
  }
}