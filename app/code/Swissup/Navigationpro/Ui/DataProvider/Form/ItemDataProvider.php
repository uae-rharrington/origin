<?php

namespace Swissup\Navigationpro\Ui\DataProvider\Form;

class ItemDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Swissup\Navigationpro\Model\Item\Locator\LocatorInterface
     */
    protected $locator;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Locator $locator
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Swissup\Navigationpro\Model\ResourceModel\Item\Collection $collection,
        \Swissup\Navigationpro\Model\Item\Locator\LocatorInterface $locator,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->locator = $locator;
        $this->request = $request;
    }

    public function getData()
    {
        $item = $this->locator->getItem();

        $useRemoteData = "0";
        if ($item->getRemoteEntityId()) {
            $useRemoteData = $item->getUseRemoteData();
            if (null === $useRemoteData) {
                $useRemoteData = "1";
            }
        }

        return [
            $item->getId() => array_merge($item->getData(), [
                'store_id' => $item->getStoreId(),
                'use_remote_data' => $useRemoteData
            ])
        ];
    }

    /**
     * Prepare meta data
     *
     * @param array $meta
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        if ($this->request->getParam('store')) {
            foreach ($this->getScopeSpecificFieldsMap() as $fieldsets => $fields) {
                $tmp = &$meta;
                foreach (explode('/', $fieldsets) as $fieldset) {
                    if (!isset($tmp[$fieldset]['children'])) {
                        $tmp[$fieldset]['children'] = [];
                    }
                    $tmp = &$tmp[$fieldset]['children'];
                }

                foreach ($fields as $field) {
                    $tmp[$field]['arguments']['data']['config']['service'] = [
                        'template' => 'ui/form/element/helper/service',
                    ];
                    $tmp[$field]['arguments']['data']['config']['disabled'] =
                        !$this->isScopeOverriddenValue($field, $fieldsets);
                }
            }
        }

        $item = $this->locator->getItem();
        if (!$item->getRemoteEntityId()) {
            $meta['main']['children']['general']['children']['use_remote_data']
                ['arguments']['data']['config']['visible'] = false;
        }

        return $meta;
    }

    protected function isScopeOverriddenValue($field, $group)
    {
        $data = $this->locator->getItem()->getData();

        if (empty($data['store_id'])) {
            return false; // all values are from default store view
        }

        if (strpos($group, 'dropdown_settings') !== false) {
            return isset($data['content']['scope']['dropdown_settings'][$field]);
        }

        // @see Swissup\Navigationpro\Model\ResourceModel\Item::_afterLoad
        return isset($data['content']['scope'][$field]);
    }

    /**
     * @return array
     */
    protected function getScopeSpecificFieldsMap()
    {
        return [
            'main/general' => [
                'name',
                'url_path',
            ],
            'main/advanced/html_wrapper' => [
                'html',
            ],
            'main/advanced' => [
                'css_class',
            ],
            'main/dropdown_settings' => [
                'use_menu_settings',
                'width',
                'layout',
                'dropdown_css_class',
            ],
        ];
    }
}
