<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SharedCatalog\Test\Constraint;

use Magento\SharedCatalog\Test\Page\Adminhtml\SharedCatalogConfigure;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\SharedCatalog\Test\Page\Adminhtml\SharedCatalogIndex;

/**
 * Assert new price values in shared catalog pricing grid.
 */
class AssertNewPrice extends AbstractConstraint
{
    /**
     * Assert new price values in shared catalog pricing grid.
     *
     * @param SharedCatalogConfigure $sharedCatalogConfigure
     * @param SharedCatalogIndex $sharedCatalogIndex
     * @param array $products
     * @param array $customPrices
     * @param string $websiteName
     * @param string|null $sharedCatalogName
     * @return void
     */
    public function processAssert(
        SharedCatalogConfigure $sharedCatalogConfigure,
        SharedCatalogIndex $sharedCatalogIndex,
        array $products,
        array $customPrices,
        $websiteName,
        $sharedCatalogName = null
    ) {
        if ($sharedCatalogName !== null) {
            $sharedCatalogIndex->open();
            $sharedCatalogIndex->getGrid()->search(['name' => $sharedCatalogName]);
            $sharedCatalogIndex->getGrid()->openConfigure($sharedCatalogIndex->getGrid()->getFirstItemId());
            $sharedCatalogConfigure->getContainer()->openConfigureWizard();
            $sharedCatalogConfigure->getNavigation()->nextStep();
        }
        $sharedCatalogConfigure->getPricingGrid()->filterProductsByWebsite($websiteName);
        foreach ($products as $key => $product) {
            $sharedCatalogConfigure->getPricingGrid()->search(['sku' => $product->getSku()]);
            $rowId = $sharedCatalogConfigure->getPricingGrid()->getFirstItemId();

            \PHPUnit_Framework_Assert::assertEquals(
                $customPrices[$key]['new_price'],
                $sharedCatalogConfigure->getPricingGrid()->getColumnValue($rowId, 'New Price'),
                'New price value for product \'' . $product->getName() . '\'' . ' is incorrect.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'New price values are correct.';
    }
}
