<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\EasySlide\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Slider extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'slider.phtml';

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Swissup\EasySlide\Model\Slider $sliderModel
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Swissup\EasySlide\Model\Slider $sliderModel,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_sliderModel = $sliderModel;
        $this->_objectManager = $_objectManager;
        $this->_jsonEncoder = $jsonEncoder;
    }

    public function getSlider()
    {
        $identifier = $this->getIdentifier();
        if (!$identifier) {
            return false;
        }

        $this->_sliderModel->loadByIdentifier('identifier', $identifier);
        if (!$this->_sliderModel->getId() || !$this->_sliderModel->getIsActive()) {
            return false;
        }

        return $this->_sliderModel;
    }

    public function getImage($image)
    {
        $urlTypeMedia = \Magento\Framework\UrlInterface::URL_TYPE_MEDIA;
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()->getBaseUrl($urlTypeMedia) . "easyslide/" . $image;
    }


    public function getSlideDescription($description)
    {
        $processor = $this->_objectManager->get('Magento\Framework\Filter\Template');
        return $processor->filter($description);
    }

    public function getSliderConfig()
    {
        $sliderConfig = unserialize($this->getSlider()->getSliderConfig());
        $config = [
            'direction' => $sliderConfig["direction"],
            'effect' => $sliderConfig["effect"],
            'speed' => (int)$sliderConfig["speed"],
            'autoplay' => (int)$sliderConfig["autoplay"],
        ];

        if ($sliderConfig["pagination"]) {
            $config['pagination'] = '.swiper-pagination';
            $config['paginationClickable'] = true;
            $config['paginationType'] = 'bullets';
        }

        if ($sliderConfig["navigation"]) {
            $config['nextButton'] = '.swiper-button-next';
            $config['prevButton'] = '.swiper-button-prev';
        }

        if ($sliderConfig["scrollbar"]) {
            $config['scrollbar'] = '.swiper-scrollbar';
            $config['scrollbarHide'] = $sliderConfig["scrollbarHide"];
        }

        return $this->_jsonEncoder->encode($config);
    }
}
