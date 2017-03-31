<?php

namespace Drupal\cool;

interface SchemaInterface {

  /**
   * Defines the database table name and can be used in other places as a
   * machine name
   * @return string
   */
  public function getTableName();

  /**
   * @return array a Schema API structure defining the data that will be used
   * with the snapshots
   * @return array
   */
  public function getSchemaFieldsDefinition();

  /**
   * Retrieve data needed to create the data warehouse and process it into
   * the expected format.
   * @return array
   */
  public function getSchemaExtraInfoDefinition();

}