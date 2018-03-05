<?php

namespace Swissup\Navigationpro\Model\ResourceModel;

use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;

class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        $connectionName = null
    ) {
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('swissup_navigationpro_item', 'item_id');
    }

    /**
     * 1. Prepare dropdown_settings object
     * 2. Load scope-specific value to use together with "Use Default Value" checkboxes
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['content' => $this->getTable('swissup_navigationpro_item_content')])
            ->where('content.item_id = ?', $object->getId())
            ->where('content.store_id IN (?)', [$object->getStoreId(), Store::DEFAULT_STORE_ID]);

        $result = $connection->fetchAll($select);
        if (!$result) {
            return;
        }

        $assocData = [
            'content' => [
                'default' => [],
                'scope' => [],
            ]
        ];
        foreach ($result as $data) {
            if ($data['dropdown_settings']) {
                $data['dropdown_settings'] = $this->jsonHelper->jsonDecode(
                    $data['dropdown_settings']
                );
            } else {
                $data['dropdown_settings'] = [
                    'use_menu_settings' => '1'
                ];
            }

            if ($data['store_id'] == Store::DEFAULT_STORE_ID) {
                $assocData['content']['default'] = $data;
            } else {
                $assocData['content']['scope'] = $data;
            }
        }

        $object->addData($assocData);
        $object->addContentData(
            $assocData['content']['default'],
            $assocData['content']['scope']
        );

        return parent::_afterLoad($object);
    }

    /**
     * 1. Prepare dropdown_settings column
     * 2. Prepare parent item dependent properties
     * 3. Update item position and place it into the right place
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()
            || $object->getParentId() !== $object->getOrigData('parent_id')
            || $object->getForceParentUpdate()) {

            // Prevent recursion:
            //
            // Child path:
            // 0/3
            //
            // Possible parents paths and expected results:
            // 0        allow
            // 0/33     allow
            // 0/3/4    disallow
            // 0/3      disallow
            //
            $parentPath = $object->getParentItem()->getPath() . '/';
            $itemPath   = $object->getPath() . '/';
            if (strpos($parentPath, $itemPath) === 0) {
                throw new LocalizedException(
                    __('An item cannot be parent for itself')
                );
            }

            $object->setLevel($object->getParentItem()->getLevel() + 1);
            $object->setPath($object->getParentItem()->getPath() . '/' . (string)$object->getId());
        }

        // Update item position and place it before next_sibling_id
        // or after all siblings
        if (null !== $object->getInsertBefore()) {
            if (!$object->getInsertBefore()) {
                // move item to the bottom of new parent
                $maxPosition = $object->getParentItem()
                    ->getLastChildItem()
                    ->getPosition();
                $position = $maxPosition + 1;
            } else {
                // move item above next_sibling_id
                // and increment position of all next items
                $position = $object->getInsertBefore()->getPosition();

                // @todo: move to single query method
                $nextSiblings = $object->getInsertBefore()->getNextSiblingItems();
                foreach ($nextSiblings as $sibling) {
                    if ($sibling->getId() === $object->getId()) {
                        continue;
                    }

                    $sibling
                        ->setSkipContentUpdate(true)
                        ->setPosition($sibling->getPosition() + 1)
                        ->save();
                }
                $object->getInsertBefore()
                    ->setPosition($position + 1)
                    ->save();
            }
            $object->setPosition($position);
        }

        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (substr($object->getPath(), -1) === '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->savePath($object);
        }

        if (!$object->getSkipContentUpdate()) {
            $this->saveContent($object);
        }

        if (!$object->isObjectNew()
            && ($object->getParentId() !== $object->getOrigData('parent_id')
                || $object->getForceParentUpdate())) {

            foreach ($object->getChildrenItems() as $item) {
                $item
                    ->setForceParentUpdate(true)
                    ->setSkipContentUpdate(true)
                    ->save();
            }
        }

        return $this;
    }

    /**
     * Save content fields
     *
     * @param  \Swissup\Navigationpro\Model\Item $object
     * @return $this
     */
    protected function saveContent($object)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('swissup_navigationpro_item_content');

        $where = [
            'item_id = ?' => (int)$object->getId(),
            'store_id = ?' => (int)$object->getStoreId(),
        ];
        $connection->delete($table, $where);

        $data = array_fill_keys([
            'item_id',
            'store_id',
            'name',
            'url_path',
            'html',
            'css_class',
            'dropdown_settings',
        ], null);

        foreach ($data as $key => $value) {
            $data[$key] = $object->getData($key);

            if (in_array($key, ['dropdown_settings']) && is_array($data[$key])) {
                $data[$key] = $this->jsonHelper->jsonEncode($data[$key]);
            }
        }

        $connection->insert($table, $data);

        return $this;
    }

    /**
     * Update path and level fields
     *
     * @param \Swissup\Navigationpro\Model\Item $object
     * @return $this
     */
    protected function savePath($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['path' => $object->getPath()],
                ['item_id = ?' => $object->getId()]
            );
        }
        return $this;
    }
}
