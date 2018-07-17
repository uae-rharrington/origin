<?php
/**
 * Install Schema
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Setup;

use	Magento\Framework\Setup\InstallSchemaInterface;
use	Magento\Framework\Setup\ModuleContextInterface;
use	Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * ClassyLlama\OrderComments\Setup\InstallSchema
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
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

        $orderCommentTable = $installer->getTable('uae_order_comment');

        /**
         * Create uae_order_comment table
         */
        if (!$installer->tableExists($orderCommentTable)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($orderCommentTable)
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order Id'
            )->addColumn(
                'order_type',
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Order Type'
            )->addColumn(
                'order_comment',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Order Comment'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Comment Create Date'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Comment Create Date'
            )->setComment('UAE Order Comment Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
