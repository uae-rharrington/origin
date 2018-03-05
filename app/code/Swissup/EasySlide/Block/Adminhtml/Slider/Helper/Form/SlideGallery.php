<?php
/**
 * Slide gallery attribute
 */
namespace Swissup\EasySlide\Block\Adminhtml\Slider\Helper\Form;

use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery;

class SlideGallery extends Gallery implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    public function getContentHtml()
    {
        /* @var $content \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content */
        $slideContent = $this->_layout->createBlock('Swissup\EasySlide\Block\Adminhtml\Slider\Helper\Form\Gallery\SlideContent')
            ->setElement($this);
        $slideContent->setId($this->getHtmlId() . '_content')->setElement($this);
        $slideContent->setFormName('edit_form');
        $gallery = $slideContent->getJsObjectName();
        $slideContent->getUploader()->getConfig()->setMegiaGallery($gallery);
        return $slideContent->toHtml();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('');
    }


    public function getTabLabel()
    {
        return __('Slides');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Slides');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }



}
