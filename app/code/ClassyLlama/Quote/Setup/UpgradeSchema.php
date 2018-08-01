<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface 
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = false;

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection = $setup->getConnection();

            $connection->addColumn(
                $setup->getTable('sales_order'),
                'originating_quote_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Corresponds to whether or not the order is a Quote Request.'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_order_grid'),
                'originating_quote_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Corresponds to whether or not the order is a Quote Request.'
                ]
            );

            $connection->addColumn(
                $setup->getTable('magento_sales_order_grid_archive'),
                'originating_quote_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Corresponds to whether or not the order is a Quote Request.'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if (!$connection) {
                $connection = $setup->getConnection();
            }

            $connection->addColumn(
                $setup->getTable('quote'),
                'originating_quote_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Increment Id of the Quote Request Order this was created from.'
                ]
            );
        }

        $setup->endSetup();
    }
}
