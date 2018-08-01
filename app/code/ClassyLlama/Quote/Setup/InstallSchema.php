<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable('sales_order'),
            'is_quote_request',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => false,
                'comment' => 'Corresponds to whether or not the order is a Quote Request.'
            ]
        );

        $connection->addColumn(
            $setup->getTable('sales_order_grid'),
            'is_quote_request',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => false,
                'comment' => 'Corresponds to whether or not the order is a Quote Request.'
            ]
        );

        $connection->addColumn(
            $setup->getTable('magento_sales_order_grid_archive'),
            'is_quote_request',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => false,
                'comment' => 'Corresponds to whether or not the order is a Quote Request.'
            ]
        );

        $setup->endSetup();
    }
}
