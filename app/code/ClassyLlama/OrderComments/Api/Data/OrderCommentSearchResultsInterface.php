<?php
/**
 * Api Data OrderComment SearchResultsInterface
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * ClassyLlama\OrderComments\Api\Data\OrderJobSearchResultsInterface
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
interface OrderCommentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Retrieve OrderComment List
     *
     * @api
     * @return \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface[]
     */
    public function getItems();

    /**
     * Set OrderComment List
     *
     * @api
     * @param \ClassyLlama\OrderComments\Api\Data\OrderCommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}