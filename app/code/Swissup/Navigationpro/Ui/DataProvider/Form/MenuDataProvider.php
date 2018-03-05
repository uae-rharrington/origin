<?php

namespace Swissup\Navigationpro\Ui\DataProvider\Form;

class MenuDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Swissup\Navigationpro\Model\Menu\Locator\LocatorInterface
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
        \Swissup\Navigationpro\Model\ResourceModel\Menu\Collection $collection,
        \Swissup\Navigationpro\Model\Menu\Locator\LocatorInterface $locator,
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
        $menu = $this->locator->getMenu();

        if (!$menu->getId()) {
            switch ($this->request->getParam('type')) {
                case 'megamenu':
                    $data = $this->getMegamenuData();
                    break;
                default:
                    $data = $this->getDefaultData();
            }
            $menu->setData($data);
        }

        // Fill dropdown_settings with default data if needed
        $dropdownSettingKeys = [
            'default',
            'level1',
        ];
        $dropdownSettings = $menu->getData('dropdown_settings');
        foreach ($dropdownSettingKeys as $key) {
            if (empty($dropdownSettings[$key])) {
                $dropdownSettings[$key] = [
                    'width'  => 'small',
                    'layout' => $this->getDefaultDropdownLayout(),
                ];
            }
        }
        $menu->setData('dropdown_settings', $dropdownSettings);

        return [
            $menu->getId() => $menu->getData()
        ];
    }

    protected function getDefaultData()
    {
        return [
            'is_active' => '1',
            'direction' => 'horizontal',
            'max_depth' => '0',
            'dropdown_settings' => [
                'default' => [
                    'width'  => 'small',
                    'layout' => $this->getDefaultDropdownLayout(),
                ],
                'level1' => [
                    'width'  => 'small',
                    'layout' => $this->getDefaultDropdownLayout(),
                ],
            ],
        ];
    }

    protected function getMegamenuData()
    {
        return [
            'is_active' => '1',
            'direction' => 'horizontal',
            'max_depth' => '0',
            'dropdown_settings' => [
                'default' => [
                    'width'  => 'small',
                    'layout' => $this->getDefaultDropdownLayout(),
                ],
                'level1' => [
                    'width'  => 'boxed',
                    'layout' => $this->getMegamenuDropdownLayout(),
                ],
            ],
        ];
    }

    protected function getDefaultDropdownLayout()
    {
        return json_encode([
            "start" => [
                "size" => 0,
                "rows" => []
            ],
            "center" => [
                "size" => 12,
                "rows" => [[[
                    "id" => uniqid('navpro_'),
                    "size" => "12",
                    "type" => "children",
                    "is_active" => "1",
                    "columns_count" => "1",
                    "levels_per_dropdown" => "1",
                ]]]
            ],
            "end" => [
                "size" => 0,
                "rows" => []
            ]
        ]);
    }

    protected function getMegamenuDropdownLayout()
    {
        return json_encode([
            "start" => [
                "size" => 0,
                "rows" => []
            ],
            "center" => [
                "size" => 12,
                "rows" => [[[
                    "id" => uniqid('navpro_'),
                    "size" => "9",
                    "type" => "children",
                    "is_active" => "1",
                    "columns_count" => "5",
                    "levels_per_dropdown" => "2",
                ], [
                    "id" => uniqid('navpro_'),
                    "size" => "3",
                    "type" => "html",
                    "is_active" => "1",
                ]]]
            ],
            "end" => [
                "size" => 0,
                "rows" => []
            ]
        ]);
    }
}
