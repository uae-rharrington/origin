<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\NegotiableQuote\Helper;

use PHPUnit\Framework\TestCase;
use Magento\NegotiableQuote\Helper\QuoteFactory as QuoteHelperFactory;
use Magento\NegotiableQuote\Helper\Quote as QuoteHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\NegotiableQuote\Api\NegotiableQuoteRepositoryInterface;

class QuoteTest extends TestCase
{
    /**
     * @var QuoteHelperFactory
     */
    private $helperFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var NegotiableQuoteRepositoryInterface
     */
    private $quoteRepo;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerSession = $objectManager->get(CustomerSession::class);
        $this->helperFactory = $objectManager->get(QuoteHelperFactory::class);
        $this->customerRepo = $objectManager->get(
            CustomerRepositoryInterface::class
        );
        $this->request = $objectManager->get(RequestInterface::class);
        $this->quoteRepo = $objectManager->get(
            NegotiableQuoteRepositoryInterface::class
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/NegotiableQuote/_files/company_customer.php
     * @magentoDataFixture Magento/NegotiableQuote/_files/negotiable_quote.php
     */
    public function testResolveCurrentQuote()
    {
        $quoteCreator = $this->customerRepo->get('email@companyquote.com');
        $quotes = $this->quoteRepo->getListByCustomerId($quoteCreator->getId());
        $quote = array_shift($quotes);
        $this->request->setParams(['quote_id' => $quote->getId()]);

        $this->customerSession->setCustomerData(
            $this->customerRepo->get('customercompany22@example.com')
        );
        /** @var QuoteHelper $helper */
        $helper = $this->helperFactory->create();
        $this->assertEmpty($helper->resolveCurrentQuote());

        $this->customerSession->setCustomerData($quoteCreator);
        /** @var QuoteHelper $helper */
        $helper = $this->helperFactory->create();
        $this->assertNotEmpty($resolved = $helper->resolveCurrentQuote());
        $this->assertEquals($quote->getId(), $resolved->getId());

        $this->customerSession->setCustomerId(null);
        /** @var QuoteHelper $helper */
        $helper = $this->helperFactory->create();
        $this->assertEmpty($helper->resolveCurrentQuote());
    }
}
