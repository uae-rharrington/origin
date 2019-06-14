<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\RequisitionList\Model\RequisitionListItem;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\RequisitionList\Api\RequisitionListRepositoryInterface;
use Magento\RequisitionList\Api\Data\RequisitionListInterface;
use Magento\RequisitionList\Model\RequisitionListRepository;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
 * @magentoDataFixture Magento/RequisitionList/_files/list.php
 * @magentoDbIsolation disabled
 */
class SaveHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RequisitionListRepository
     */
    private $requisitionListRepository;

    protected function setUp()
    {
        $this->saveHandler = Bootstrap::getObjectManager()->create(SaveHandler::class);
        $this->productRepository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);
        $this->requisitionListRepository = Bootstrap::getObjectManager()->create(
            RequisitionListRepositoryInterface::class
        );
    }

    public function testSaveItemConfigurableProduct()
    {
        $productSku = 'configurable';
        $product = $this->productRepository->get($productSku);
        $productOptions = $product->getTypeInstance()->getConfigurableOptions($product);
        $requisitionListProductData = new \Magento\Framework\DataObject(['sku' => $productSku]);
        $requisitionListOptions = ['product' => $product->getId(), 'options' => []];
        foreach ($productOptions as $attributeId => $optionItems) {
            $requisitionListOptions['super_attribute'] = [$attributeId => array_pop($optionItems)['value_index']];
        }
        $message = $this->saveHandler->saveItem(
            $requisitionListProductData,
            $requisitionListOptions,
            0,
            $this->getRequisitionListId()
        );
        $this->assertEquals(
            'Product Configurable Product has been added to the requisition list list name.',
            $message->render()
        );
    }

    public function testSaveItemConfigurableProductException()
    {
        $productSku = 'configurable';
        $product = $this->productRepository->get($productSku);
        $requisitionListProductData = new \Magento\Framework\DataObject(['sku' => $productSku]);
        $requisitionListOptions = ['product' => $product->getId(), 'options' => []];
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('You need to choose options for your item.');
        $this->saveHandler->saveItem(
            $requisitionListProductData,
            $requisitionListOptions,
            0,
            $this->getRequisitionListId()
        );
    }

    private function getRequisitionListId()
    {
        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        $filter = $filterBuilder->setField(RequisitionListInterface::CUSTOMER_ID)->setValue(1)->create();
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilters([$filter]);
        $list = $this->requisitionListRepository->getList($searchCriteriaBuilder->create())->getItems();
        return array_pop($list)->getId();
    }
}
