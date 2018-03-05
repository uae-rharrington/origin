<?php

namespace Swissup\SeoUrls\Model\Filter;

class Attribute extends AbstractFilter
{
    /**
     * Get filtrable attribute used for filter
     *
     * @return null|
     */
    public function getLayerFilter()
    {
        return $this->getData('layer_filter');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        if (!$this->hasData('label')) {
            $filter = $this->getLayerFilter();
            if (isset($filter)) {
                $this->setData(
                    'label',
                    $this->helper->getSeoFriendlyString(
                        $filter->getStoreLabel()
                    )
                );
            }
        }

        return $this->getData('label');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (!$this->hasData('options') && (null !== $this->getLayerFilter())) {
            $options = [];
            foreach ($this->getLayerFilter()->getOptions() as $option) {
                $label = $this->helper->getSeoFriendlyString(
                    $option->getLabel()
                );
                if (in_array($label, $options)) {
                    // this should not occur - poor options naming
                    // concatenate value to duplicated label
                    $label .= '-' . $option->getValue();
                }

                $options[$option->getValue()] = $label;
            }

            $this->setData('options', array_filter($options));
        }

        return $this->getData('options');
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        if (!$this->hasData('sort_order')) {
            $filter = $this->getLayerFilter();
            if (isset($filter)) {
                $this->setData(
                    'sort_order',
                    $filter->getAttributeId() + 10000 * $filter->getPosition()
                );
            }
        }

        return $this->getData('sort_order');
    }
}
