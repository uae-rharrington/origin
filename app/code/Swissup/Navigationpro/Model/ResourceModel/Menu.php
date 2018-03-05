<?php

namespace Swissup\Navigationpro\Model\ResourceModel;

use Swissup\Navigationpro\Model\Item as MenuItem;
use Swissup\Navigationpro\Model\Menu\Source\CategoryImportMode;

class Menu extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Swissup\Navigationpro\Model\ItemFactory
     */
    protected $itemFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Swissup\Navigationpro\Model\ItemFactory $itemFactory,
        $connectionName = null
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->itemFactory = $itemFactory;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('swissup_navigationpro_menu', 'menu_id');
    }

    /**
     * Prepare dropdown_settings column
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $dropdownSettings = $object->getDropdownSettings();
        if (is_array($dropdownSettings)) {
            $object->setDropdownSettings($this->jsonHelper->jsonEncode($dropdownSettings));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Prepare dropdown_settings object
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $dropdownSettings = $object->getDropdownSettings();
        if ($dropdownSettings) {
            $object->setDropdownSettings($this->jsonHelper->jsonDecode($dropdownSettings));
        }

        return parent::_afterLoad($object);
    }

    /**
     * Import category into menu inside parent item
     *
     * @param  integer $categoryId
     * @param  integer $parentItemId
     * @param  string  $mode @see Swissup\Navigationpro\Model\Menu\Source\CategoryImportMode
     * @return void
     */
    public function importCategory($categoryId, $menuId, $parentItemId, $mode)
    {
        $category = $this->categoryFactory->create()->load($categoryId);
        if (!$category->getId()) {
            return;
        }

        $parentItem = $this->itemFactory->create();
        if ($parentItemId) {
            $parentItem->load($parentItemId);
            if (!$parentItem->getId()) {
                return;
            }
        } else {
            $parentItem->addData([
                'menu_id' => $menuId,
                'level' => 1,
                'path'  => 0,
            ]);
        }

        $filter = [];
        $filterChildren = [
            'attribute' => 'path',
            'like' => $category->getPath() . '/%'
        ];
        $filterSelected = [
            'attribute' => 'entity_id',
            'eq' => $category->getId()
        ];
        switch ($mode) {
            case CategoryImportMode::MODE_CHILDREN:
                $filter[] = $filterChildren;
                break;
            case CategoryImportMode::MODE_SELECTED:
                $filter[] = $filterSelected;
                break;
            default:
                $filter[] = $filterChildren;
                $filter[] = $filterSelected;
                break;
        }

        $collection = $this->categoryCollectionFactory->create()
            ->addNameToResult()
            ->addUrlRewriteToResult()
            ->addAttributeToFilter($filter)
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('include_in_menu')
            ->addAttributeToSort('level')
            ->addAttributeToSort('position')
            ->addAttributeToSort('parent_id')
            ->addAttributeToSort('entity_id');

        if ($collection->getSize()) {
            // create mapping to speedup items processing
            // @see \Swissup\Navigationpro\Model\Item@getParentItem
            $mapping = [
                $collection->getFirstItem()->getParentId() => $parentItem
            ];
            foreach ($collection as $category) {
                $item = $this->itemFactory->create();
                $item->setParentItem($mapping[$category->getParentId()]);
                $item->addData([
                    'remote_entity_type' => MenuItem::REMOTE_ENTITY_TYPE_CATEGORY,
                    'remote_entity_id'   => $category->getId(),
                    'store_id'  => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    'menu_id'   => $menuId,
                    'parent_id' => $item->getParentItem()->getId(),
                    'is_active' => ($category->getIsActive() && $category->getIncludeInMenu()),
                    'position'  => $category->getPosition(),
                    'name'      => $category->getName(),
                    'url_path'  => $category->getRequestPath(),
                ]);
                $item->save();

                $mapping[$category->getId()] = $item;
            }
        }
    }
}
