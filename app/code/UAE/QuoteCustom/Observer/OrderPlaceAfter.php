<?php
/**
 * Order Place After Observer
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\QuoteCustom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

/**
 * UAE\QuoteCustom\Observer\OrderPlaceAfter
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * OrderPlaceAfter constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check for customer email
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getIsQuoteRequest() && !$order->getCustomerId()) {
            $customerEmail = $order->getCustomerEmail();
            try {
                $customer = $this->customerRepository->get($customerEmail);
                $order->setCustomerId($customer->getId());
            } catch (\Exception $e) {
                // don't do anything
            }
        }
    }
}
