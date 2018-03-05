<?php

namespace Swissup\Navigationpro\Model\Template;

class Filter extends \Magento\Widget\Model\Template\Filter
{
    protected $item;

    /**
     * @param \Magento\Framework\Data\Tree\Node $item
     */
    public function setItem(\Magento\Framework\Data\Tree\Node $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return \Magento\Framework\Data\Tree\Node $item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param  array $construction
     * @return string
     */
    public function navproDirective($construction)
    {
        $item   = $this->getItem();
        $params = $this->getParameters($construction[2]);
        if (!isset($params['data']) || !$item) {
            return '';
        }

        $data = $item->getData();
        foreach (explode('.', $params['data']) as $segment) {
            if (is_object($data)) {
                if (strpos($segment, 'get') === 0) {
                    $data = $data->{$segment}();
                    continue;
                } else {
                    // short notation support: remote_entity.name
                    $data = $data->getData();
                }
            }

            if (is_array($data) && array_key_exists($segment, $data)) {
                $data = $data[$segment];
            } else {
                return '';
            }
        }

        return $data;
    }
}
