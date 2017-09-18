<?php
/**
 * Filename: install-1.0.0.php.
 * Author: Muhammad Shahab Hameed
 * Date: 10/14/2016
 */
$installer=$this;
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('folio3_restoreorder_status')};
CREATE TABLE {$this->getTable('folio3_restorecancelledorder_status')} (
  `folio3_restoreorder_id` int(11) unsigned NOT NULL auto_increment COMMENT 'Restore Order ID',
  `folio3_restoreorder_order_id` int(50) unsigned NULL COMMENT 'Order ID',
  `folio3_restoreorder_status_code` varchar(255) NOT NULL  COMMENT 'Status',
  `folio3_restoreorder_created_time` datetime NULL COMMENT 'Entry created time',
  `folio3_restoreorder_modified_time` datetime NULL COMMENT 'Entry modified time',
  PRIMARY KEY (`folio3_restoreorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is used to store order statuses';

    ");
$installer->endSetup();
