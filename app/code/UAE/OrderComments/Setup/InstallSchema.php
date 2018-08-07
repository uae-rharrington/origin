<?php
/**
 * Install Schema
 *
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\OrderComments\Setup;

use	Magento\Framework\Setup\InstallSchemaInterface;
use	Magento\Framework\Setup\ModuleContextInterface;
use	Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * UAE\OrderComments\Setup\InstallSchema
 *
 * @category UAE
 * @package UAE_OrderComments
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        $columns = [
            'order_comment' => [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => false,
                'default' => '',
                'comment' => 'Order Comment'
            ],
        ];

        /** Add 'order_comment' field to 'quote' table */
        $quoteTable = $installer->getTable('quote');
        foreach ($columns as $name => $definition) {
            $connection->addColumn($quoteTable, $name, $definition);
        }

        /** Add 'order_comment' field to 'sales_order' table */
        $saleOrderTable = $installer->getTable('sales_order');
        foreach ($columns as $name => $definition) {
            $connection->addColumn($saleOrderTable, $name, $definition);
        }

        $installer->endSetup();
    }
}
