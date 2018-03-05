<?php
namespace Swissup\Ajaxsearch\Model\Catalog\Product;

use Swissup\Ajaxsearch\Model\DataProvider\AbstractDataProvider;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\ResourceModel\Query\Collection;
use Swissup\Ajaxsearch\Model\Query\Catalog\Product as Query;
use Swissup\Ajaxsearch\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Swissup\Ajaxsearch\Helper\Data as ConfigHelper;
use Magento\Catalog\Block\Product\ImageBuilder;

class DataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @param QueryFactory $queryFactory
     * @param ItemFactory $itemFactory
     * @param ConfigHelper $configHelper
     * @param ImageBuilder $imageBuilder
     */
    public function __construct(
        QueryFactory $queryFactory,
        ItemFactory $itemFactory,
        ConfigHelper $configHelper,
        ImageBuilder $imageBuilder
    ) {
        parent::__construct($queryFactory, $itemFactory, $configHelper);

        $this->imageBuilder = $imageBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $enable = $this->configHelper->isProductEnabled();
        if (!$enable) {
            return [];
        }
        $this->queryFactory->setInstanceName(Query::class);
        $collection = $this->getSuggestCollection();
        $query = $this->getQuery();

        $result = [];
        $isDebug = false;
        if ($isDebug) {
            // @see Magento\Framework\Search\Adapter\Mysql\Adapter::query for see temp table query
            $_select = (string) $collection->getSelect();
            $result[] = $this->itemFactory->create([
                '_type' => 'debug',
                '_query' => $query->getQueryText(),
                '_class' => get_class($collection),
                '_num_results' => $collection->getSize(),
                '_select' => md5($_select) .  '  ' . $_select,
            ]);
        }
        foreach ($collection as $item) {
            // $item = $item->load($item->getId());
            $resultItem = $this->itemFactory->create(
                array_merge($item->getData(), [
                    '_type' => 'product',
                    'title' => $item->getName(),
                    'num_results' => '',
                    'image' => $this->getImage($item)->toHtml(),
                    'product_url' => $item->getProductUrl(),
                    'final_price' => $this->getFinalPrice($item),
                ])
            );
            if ($resultItem->getTitle() == $query->getQueryText()) {
                array_unshift($result, $resultItem);
            } else {
                $result[] = $resultItem;
            }
        }
        return $result;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    private function getImage($product, $imageId = 'product_page_image_small', $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create()
            ->setTemplate('Swissup_Ajaxsearch::product/image.phtml');
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int|string
     */
    private function getFinalPrice($product)
    {
        return $product->getFinalPrice() > 0 ?
            $product->getFinalPrice() : $product->getPriceInfo()->getPrice('final_price')->getValue();
    }
}
