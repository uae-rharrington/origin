<?php
namespace Swissup\EasySlide\Block\Adminhtml\Slider\Edit;

/**
 * Slider page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('slider_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Configuration'));
    }
}
