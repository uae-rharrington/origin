<?php
namespace Swissup\ProLabels\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\ObjectManagerInterface;

class CategoryImage extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'category_image';

    const ALT_FIELD = 'name';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        ObjectManagerInterface $om,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_objectManager = $om;
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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (!$item['product_image']) { continue; }
                $productImgUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "prolabels/category/" . $item['category_image'];

                $item[$fieldName . '_src'] = $productImgUrl;
                $item[$fieldName . '_alt'] = '';
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'prolabels/label/edit',
                    ['label_id' => $item['label_id']]
                );
                $item[$fieldName . '_orig_src'] = $productImgUrl;
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
