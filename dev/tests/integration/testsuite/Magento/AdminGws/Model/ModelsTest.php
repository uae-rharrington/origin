<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Model;

use Magento\Authorization\Model\Role;
use Magento\AdminGws\Model\Role as AdminGwsRole;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 */
class ModelsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Tests case when admin has User Role with access only to one website
     * and tries to update product which is assigned to two websites.
     * Attributes from global scope level has to be locked for changes and
     * attributes from website/store view level should be available for updates.
     *
     * @covers \Magento\AdminGws\Model\Models::catalogProductLoadAfter
     * @magentoDataFixture Magento/Catalog/_files/product_with_two_websites.php
     * @magentoDataFixture Magento/AdminGws/_files/role_websites_login.php
     */
    public function testCatalogProductLoadAfter()
    {
        /** @var Role $adminRole */
        $adminRole = $this->objectManager->get(Role::class);
        $adminRole->load('admingws_role', 'role_name');

        /** @var \Magento\AdminGws\Model\Role $adminGwsRole */
        $adminGwsRole = $this->objectManager->get(AdminGwsRole::class);
        $adminGwsRole->setAdminRole($adminRole);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $product = $productRepository->get('unique-simple-azaza', true);

        $origProductName = $product->getName();
        $newProductName = $origProductName . ' new';

        $origPrice = $product->getPrice();
        $newPrice = $origPrice + 1;

        $origStatus = $product->getStatus();
        $newStatus = $origStatus ? 0 : 1;

        $product->setName($newProductName);
        $product->setPrice($newPrice);
        $product->setStatus($newStatus);

        $this->assertEquals(
            $newProductName,
            $product->getName(),
            'Attribute from store view scope should be available for update'
        );

        $this->assertEquals(
            $newStatus,
            $product->getStatus(),
            'Attribute from website scope should be available for update'
        );

        $this->assertEquals(
            $origPrice,
            $product->getPrice(),
            'Attribute from global scope should be locked for update'
        );
    }
}
