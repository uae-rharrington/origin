<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
namespace ClassyLlama\Quote\Controller\Quote;

use Magento\Framework\App\Action\Action;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Registry;

class PrintAction extends Action
{
    /**
     * @var OrderLoaderInterface
     */
    private $orderLoader;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    private $forwardFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $forwardFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        ForwardFactory $forwardFactory,
        OrderRepositoryInterface $orderRepository,
        Registry $registry
    ) {
        $this->orderLoader = $orderLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->orderRepository = $orderRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Print Quote Action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $orderId = (int)$this->_request->getParam('order_id');

        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->forwardFactory->create();
        if (!$orderId) {
            return $resultForward->forward('noroute');
        }

        try {
            $order = $this->orderRepository->get($orderId);
            $this->registry->register('current_order', $order);
            /** @var Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->addHandle('print');
            $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle("Quote # {$order->getIncrementId()}");
            }
        } catch (\Exception $e) {
            return $resultForward->forward('noroute');
        }

        return $resultPage;
    }
}
