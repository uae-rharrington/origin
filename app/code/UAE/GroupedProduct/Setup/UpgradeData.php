<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\GroupedProduct\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Api\AttributeManagementInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeManagementInterface
     */
    private $attributeManagement;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeManagementInterface $attributeManagement
     */
    public function __construct(EavSetupFactory $eavSetupFactory, AttributeManagementInterface $attributeManagement)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeManagement = $attributeManagement;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        if (version_compare($context->getVersion(), '0.1.1') < 0) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'color_hex',
                [
                    'group' => 'Product Details',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Color Hex Code',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'note' => 'Color Hex Code'
                ]
            );
        }

        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($attributeSetIds as $attributeSetId) {
            $groupId = $eavSetup->getAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                'Product Details',
                'attribute_group_id'
            );

            $this->attributeManagement->assign(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeSetId,
                $groupId,
                'color_hex',
                20
            );
        }
        $setup->endSetup();
    }
}
