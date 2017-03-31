<?php

namespace Drupal\cool;

interface ModelEntityInterface {

  /**
   * @return string
   */
  public function getEntityTypeName();

  /**
   * @return string
   */
  public function getEntityTypeLabel();

  /**
   * @return []
   */
  public function getEntityTypeProperties();
}