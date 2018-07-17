<?php
/**
 * OrderComment Repository
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use ClassyLlama\OrderComments\Api\Data\OrderCommentInterface;
use ClassyLlama\OrderComments\Api\Data\OrderCommentInterfaceFactory;
use ClassyLlama\OrderComments\Api\Data\OrderCommentSearchResultsInterfaceFactory;
use ClassyLlama\OrderComments\Api\Data\OrderCommentSearchResultsInterface;
use ClassyLlama\OrderComments\Api\OrderCommentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\State\InputMismatchException;
use ClassyLlama\OrderComments\Model\ResourceModel\OrderComment as ResourceOrderComment;
use ClassyLlama\OrderComments\Model\ResourceModel\OrderComment\CollectionFactory
    as OrderCommentCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * ClassyLlama\OrderComments\Model\OrderCommentRepository
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class OrderCommentRepository implements OrderCommentRepositoryInterface
{
    /**
     * OrderComment ResourceModel
     *
     * @var ResourceOrderComment
     */
    private $resourceOrderComment;

    /**
     * OrderComment Factory
     *
     * @var OrderCommentFactory
     */
    private $orderCommentFactory;

    /**
     * OrderComment Collection Factory
     *
     * @var OrderCommentCollectionFactory
     */
    private $orderCommentCollectionFactory;

    /**
     * OrderComment SearchResults Interface Factory
     *
     * @var OrderCommentSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * DataObject Helper
     *
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * DataObject Processor
     *
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * OrderComment Interface Factory
     *
     * @var OrderCommentInterfaceFactory
     */
    private $dataOrderCommentFactory;

    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * Initialize Repository
     *
     * @param ResourceOrderComment $resource
     * @param OrderCommentFactory $orderCommentFactory
     * @param OrderCommentInterfaceFactory $dataOrderCommentFactory
     * @param OrderCommentCollectionFactory $orderCommentCollectionFactory
     * @param OrderCommentSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceOrderComment $resource,
        OrderCommentFactory $orderCommentFactory,
        OrderCommentInterfaceFactory $dataOrderCommentFactory,
        OrderCommentCollectionFactory $orderCommentCollectionFactory,
        OrderCommentSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceOrderComment = $resource;
        $this->orderCommentFactory = $orderCommentFactory;
        $this->orderCommentCollectionFactory = $orderCommentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOrderCommentFactory = $dataOrderCommentFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Create OrderComment
     *
     * @api
     * @param OrderCommentInterface $orderComment
     * @return OrderCommentInterface
     * @throws InputException If bad input is provided
     * @throws InputMismatchException If the provided orderComment ID is already used
     * @throws LocalizedException
     */
    public function save(OrderCommentInterface $orderComment)
    {
        try {
            $this->resourceOrderComment->save($orderComment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Order Comment: %1',
                $exception->getMessage()
            ));
        }
        return $orderComment;
    }

    /**
     * Retrieve OrderComment
     *
     * @api
     * @param int $orderCommentId
     * @return OrderCommentInterface
     * @throws NoSuchEntityException If OrderComment with the specified ID does not exist
     * @throws LocalizedException
     */
    public function getById($orderCommentId)
    {
        $orderComment = $this->orderCommentFactory->create();
        $this->resourceOrderComment->load($orderComment, $orderCommentId);

        if (!$orderComment->getId()) {
            throw new NoSuchEntityException(__(
                'Order Comment with id "%1" does not exist.',
                $orderCommentId
            ));
        }
        return $orderComment;
    }

    /**
     * Retrieve Order Comment Which Match A Specified Criteria
     *
     * @api
     * @param SearchCriteriaInterface $searchCriteria
     * @return OrderCommentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->orderCommentCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $orderComments = [];

        /** @var OrderComment $orderCommentModel */
        foreach ($collection as $orderCommentModel) {
            $orderCommentData = $this->dataOrderCommentFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $orderCommentData,
                $orderCommentModel->getData(),
                'ClassyLlama\OrderComments\Api\Data\OrderCommentInterface'
            );

            $orderComments[] = $orderCommentData;
        }
        $searchResults->setItems($orderComments);
        return $searchResults;
    }

    /**
     * Delete Order Comment
     *
     * @api
     * @param OrderCommentInterface $orderComment
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(OrderCommentInterface $orderComment)
    {
        try {
            $this->resourceOrderComment->delete($orderComment);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Order Comment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Order Comment By Id
     *
     * @api
     * @param int $orderCommentId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($orderCommentId)
    {
        return $this->delete($this->getById($orderCommentId));
    }
}