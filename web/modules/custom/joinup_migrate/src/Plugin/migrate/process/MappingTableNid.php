<?php

namespace Drupal\joinup_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a processor for mapping_table migration.
 *
 * @MigrateProcessPlugin(
 *   id = "mapping_table_nid"
 * )
 */
class MappingTableNid extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_numeric($value)) {
      $row_number = $row->getSourceProperty('row_index');
      throw new MigrateSkipRowException("Row #$row_number: Invalid Nid '$value'.", FALSE, MigrationInterface::MESSAGE_WARNING);
    }
    return $value;
  }

}
