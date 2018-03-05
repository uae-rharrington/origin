<?php

namespace Swissup\Navigationpro\Model;

use Magento\Framework\Data\Collection;
use Swissup\Navigationpro\Api\Data\MenuInterface;

class Menu extends AbstractEntity implements MenuInterface
{
    const DIRECTION_HORIZONTAL = 'horizontal';
    const DIRECTION_VERTICAL   = 'vertical';

    const ORIENTATION_HORIZONTAL = 'horizontal';
    const ORIENTATION_VERTICAL   = 'vertical';
    const ORIENTATION_ACCORDION  = 'accordion';

    const THEME_BLANK          = '';
    const THEME_DARK           = 'dark';
    const THEME_DARK_BAR       = 'dark-bar';
    const THEME_DARK_DROPDOWN  = 'dark-dropdown';
    const THEME_FLAT           = 'flat';
    const THEME_COMPACT        = 'compact';

    /**
     * @var \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context                                  $context
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null      $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                $resourceCollection
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;

        return parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Navigationpro\Model\ResourceModel\Menu');
    }

    /**
     * Import category into menu, inside parent item
     *
     * @param  integer $categoryId
     * @param  integer $parentItemId
     * @param  string  $mode @see Swissup\Navigationpro\Model\Menu\Source\CategoryImportMode
     * @return void
     */
    public function importCategory($categoryId, $parentItemId, $mode)
    {
        return $this->getResource()->importCategory(
            $categoryId,
            $this->getId(),
            $parentItemId,
            $mode
        );
    }

    /**
     * Get visible items only
     *
     * @param  int $storeId
     * @return \Swissup\Navigationpro\Model\ResourceModel\Item\Collection
     */
    public function getVisibleItems($storeId)
    {
        $collection = $this->getItems($storeId)
            ->addFieldToFilter('is_active', 1);

        if ($this->getMaxDepth()) {
            $collection->addFieldToFilter(
                'level',
                [
                    'lt' => 2 + $this->getMaxDepth()
                ]
            );
        }

        return $collection;
    }

    /**
     * Get all menu items (including disabled)
     *
     * @param  int      $storeId
     * @return \Swissup\Navigationpro\Model\ResourceModel\Item\Collection
     */
    public function getItems($storeId)
    {
        $collection = $this->itemCollectionFactory->create();
        $collection->canAddRemoteEntities(true);
        $collection->setStoreId($storeId);
        $collection->addFieldToFilter('menu_id', $this->getId());
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('item_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::MENU_ID);
    }

    /**
     * Get is_active
     *
     * @return int
     */
    public function getIsActive()
    {
        return (int) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param  int $id
     * @return MenuInterface
     */
    public function setId($id)
    {
        return $this->setData(self::MENU_ID, $id);
    }

    /**
     * Set menu identifier
     *
     * @param  int $identifier
     * @return MenuInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Set is_active
     *
     * @param  int $isActive
     * @return MenuInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Prepare menu orientations
     *
     * @return array
     */
    public function getAvailableOrientations()
    {
        return [
            self::ORIENTATION_HORIZONTAL  => __('Horizontal'),
            self::ORIENTATION_VERTICAL    => __('Vertical'),
            // self::ORIENTATION_ACCORDION   => __('Accordion'),
        ];
    }

    /**
     * Prepare menu directions
     *
     * @return array
     */
    public function getAvailableDirections()
    {
        return [
            self::DIRECTION_HORIZONTAL  => __('Horizontal'),
            self::DIRECTION_VERTICAL    => __('Vertical')
        ];
    }

    /**
     * Prepare menu themes
     *
     * @return array
     */
    public function getAvailableThemes()
    {
        return [
            self::THEME_BLANK           => __('Blank'),
            self::THEME_DARK            => __('Dark'),
            self::THEME_DARK_BAR        => __('Dark Bar'),
            self::THEME_DARK_DROPDOWN   => __('Dark Dropdown'),
            self::THEME_FLAT            => __('Flat'),
            self::THEME_COMPACT         => __('Compact'),
        ];
    }
}
