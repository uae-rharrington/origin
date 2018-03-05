<?php

namespace Swissup\SeoUrls\Model\Filter;

class Rating extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->helper->getPredefinedFilterLabel('rating_filter');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (!$this->hasData('options')) {
            $options = [
                '80-100' => $this->helper->getSeoFriendlyString(__('4 and up')),
                '60-100' => $this->helper->getSeoFriendlyString(__('3 and up')),
                '40-100' => $this->helper->getSeoFriendlyString(__('2 and up')),
                '20-100' => $this->helper->getSeoFriendlyString(__('1 and up')),
                '0-100'  => $this->helper->getSeoFriendlyString(__('any')),
            ];
            $this->setData('options', $options);
        }

        return $this->getData('options');
    }
}
