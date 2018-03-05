<?php

namespace Swissup\Ajaxsearch\Model\Config\Source\FoldedDesign;

class Effect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'zoom-in',    'label' => __('Zoom In')],
            ['value' => 'slide-down', 'label' => __('Slide Down')],
            ['value' => 'fade',       'label' => __('Fade')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }
}
