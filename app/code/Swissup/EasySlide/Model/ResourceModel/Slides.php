<?php
namespace Swissup\EasySlide\Model\ResourceModel;

/**
 * Easyslide Slides mysql resource
 */
class Slides extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_easyslide_slides', 'slide_id');
    }

    public function getSlides($sliderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())
            ->where('slider_id = ?', $sliderId)
            ->order('sort_order');

        return $connection->fetchAll($select);
    }
}
