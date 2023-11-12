<?php
/**
 * @category    M2Commerce Enterprise
 * @package     M2Commerce_SaveForLater
 * @copyright   Copyright (c) 2023 M2Commerce Enterprise
 * @author      dawoodgondaldev@gmail.com
 */

namespace M2Commerce\SaveForLater\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('m2c_save_for_later'))
            ->addColumn(
                'entity_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id'
            )->addColumn(
                'customer_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false
                ],
                'Customer Id'
            )->addColumn(
                'group_product_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => true
                ],
                'Group Product Id'
            )->addColumn(
                'qoute_item_request_info',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Qoute Item Request Info'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->setComment('M2C Save For Later');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
