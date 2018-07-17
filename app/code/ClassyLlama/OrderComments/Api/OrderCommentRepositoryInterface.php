<?php
/**
 * Interface OrderCommentRepositoryInterface
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Api;

/**
 * Interface OrderCommentRepositoryInterface
 * @api
 * @since 100.0.2
 */
interface OrderCommentRepositoryInterface
{
    /**
     * Create Order Comment
     *
     * @api
     * @param \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface $orderComment
     * @return \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided order comment ID is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\ClassyLlama\OrderComments\Api\Data\OrderCommentInterface $orderComment);

    /**
     * Retrieve Order Comment By Id
     *
     * @api
     * @param int $orderCommentId
     * @return \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If order comment with the specified ID does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($orderCommentId);

    /**
     * Retrieve OrderComment Which Match A Specified Criteria
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \ClassyLlama\OrderComments\Api\Data\OrderCommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete OrderComment
     *
     * @api
     * @param \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface $orderComment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\ClassyLlama\OrderComments\Api\Data\OrderCommentInterface $orderComment);

    /**
     * Delete OrderComment By Id
     *
     * @api
     * @param int $orderCommentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($orderCommentId);
}