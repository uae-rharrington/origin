<?php

namespace Swissup\SoldTogether\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form\Fieldset;

class SoldTogether extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related   //AbstractModifier
{
    const DATA_SOLD_SCOPE = '';
    const DATA_SCOPE_ORDER = 'sold_order';
    const DATA_SCOPE_CUSTOMER = 'sold_customer';
    const GROUP_SOLDTOGETHER = 'soldtogether';

    protected $soldPrefix;
    protected $soldName;
    protected $url;

    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_SOLDTOGETHER => [
                    'children' => [
                        $this->soldPrefix . static::DATA_SCOPE_ORDER => $this->getOrderFieldset(),
                        $this->soldPrefix . static::DATA_SCOPE_CUSTOMER => $this->getCustomerFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('SoldTogether'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SOLD_SCOPE,
                                'sortOrder' => 300
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if (!$productId) {
            return $data;
        }

        $pricemod = ObjectManager::getInstance()->get(
            \Magento\Catalog\Ui\Component\Listing\Columns\Price::class
        );

        $pricemod->setData('name', 'price');

        $objectManager = ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
            $productCollection->addAttributeToSelect('*');

            if ('sold_order' == $dataScope) {
                $productCollection->getSelect()
                    ->joinInner(
                        ['sc' => $resource->getTableName('swissup_soldtogether_order')],
                        'sc.related_id=e.entity_id and sc.product_id=' . $productId,
                        ['soldtogether_weight' => 'sc.weight']
                    );
            } elseif ('sold_customer' == $dataScope) {
                $productCollection->getSelect()
                    ->joinInner(
                        ['sc' => $resource->getTableName('swissup_soldtogether_customer')],
                        'sc.related_id=e.entity_id and sc.product_id=' . $productId,
                        ['soldtogether_weight' => 'sc.weight']
                    );
            }

            $productCollection->getSelect()->order('soldtogether_weight DESC');

            foreach ($productCollection as $linkItem) {
                $data[$productId]['links'][$dataScope][] = $this->fillSoldTogetherData($linkItem);
            }
            if (!empty($data[$productId]['links'][$dataScope])) {
                $dataMap = $pricemod->prepareDataSource([
                    'data' => [
                        'items' => $data[$productId]['links'][$dataScope]
                    ]
                ]);
                $data[$productId]['links'][$dataScope] = $dataMap['data']['items'];
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }

    protected function fillSoldTogetherData($product)
    {
        return [
            'id' => $product->getId(),
            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $this->status->getOptionText($product->getStatus()),
            'attribute_set' => $this->attributeSetRepository
                ->get($product->getAttributeSetId())
                ->getAttributeSetName(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice(),
            'position' => $product->getSoldtogetherWeight(),
        ];
    }

    protected function getOrderFieldset()
    {
        $content = __(
            'Frequently Bought Together'
        );
        return [
            'children' => [
                'button_set' => $this->getSoldOrderButtonSet(
                    $content,
                    __('Add Frequently Bought Products'),
                    $this->soldPrefix . static::DATA_SCOPE_ORDER
                ),
                'modal' => $this->getGenericModal(
                    __('Add Frequently Bought Products'),
                    $this->soldPrefix . static::DATA_SCOPE_ORDER
                ),
                static::DATA_SCOPE_ORDER => $this->getGrid($this->soldPrefix . static::DATA_SCOPE_ORDER),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Frequently Bought Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    protected function getCustomerFieldset()
    {
        $content = __(
            'Customers Who Bought This Item Also Bought'
        );

        return [
            'children' => [
                'button_set' => $this->getSoldCustomerButtonSet(
                    $content,
                    __('Add Customers Bought Products'),
                    $this->soldPrefix . static::DATA_SCOPE_CUSTOMER
                ),
                'modal' => $this->getGenericModal(
                    __('Add Customers Bought Products'),
                    $this->soldPrefix . static::DATA_SCOPE_CUSTOMER
                ),
                static::DATA_SCOPE_CUSTOMER => $this->getGrid($this->soldPrefix . static::DATA_SCOPE_CUSTOMER),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Customers Who Bought This Item Also Bought Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 20,
                    ],
                ],
            ]
        ];
    }

    protected function getSoldOrderButtonSet(Phrase $content, Phrase $btnTitle, $scope)
    {
        $modal = 'product_form.product_form.soldtogether.sold_order.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modal,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modal . '.' . $scope . '_product_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $btnTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    protected function getSoldCustomerButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modal = 'product_form.product_form.soldtogether.sold_customer.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modal,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modal . '.' . $scope . '_product_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_ORDER,
            static::DATA_SCOPE_CUSTOMER,
        ];
    }
}
