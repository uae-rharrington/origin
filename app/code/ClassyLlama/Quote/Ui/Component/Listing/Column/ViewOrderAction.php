<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Ui\Component\Listing\Column;

class ViewOrderAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $orderUrlPath = $this->getData('config/orderUrlPath') ?: '#';
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'entity_id';
                    if (!isset($item[$this->getData('name')])) {
                        $item[$this->getData('name')] = [];
                    }
                    $item[$this->getData('name')]['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $viewUrlPath,
                            [
                                $urlEntityParamName => $item['entity_id']
                            ]
                        ),
                        'label' => __('View')
                    ];
                    $item[$this->getData('name')]['order'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $orderUrlPath,
                            [
                                $urlEntityParamName => $item['entity_id']
                            ]
                        ),
                        'label' => __('Order')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
