<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$companyCollection = Bootstrap::getObjectManager()
    ->create(\Magento\Company\Model\ResourceModel\Company\Collection::class);
$company = $companyCollection->getLastItem();
$company->delete();

try {
    $customerRepository = Bootstrap::getObjectManager()->get(\Magento\Customer\Api\CustomerRepositoryInterface::class);
    $customer = $customerRepository->get('email1@companyquote.com');
    $quoteRepository = Bootstrap::getObjectManager()->get(\Magento\Quote\Api\CartRepositoryInterface::class);
    $quote = $quoteRepository->getForCustomer($customer->getId());
    $quoteRepository->delete($quote);
    $customerRepository->delete($customer);
} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
    //Nothing to delete
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
