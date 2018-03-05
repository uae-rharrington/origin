<?php
namespace Swissup\EasySlide\Model\ResourceModel;

/**
 * Easyslide Slider mysql resource
 */
class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_easyslide_slider', 'slider_id');
    }

    public function getSlides($sliderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_easyslide_slides'))
        ->where('slider_id = ?', $sliderId)
        ->where('is_active = ?', 1)
        ->order('sort_order');

        return $connection->fetchAll($select);
    }

    public function getOptionSliders()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable())
        ->where('is_active = ?', 1);

        return $connection->fetchAll($select);
    }
}
