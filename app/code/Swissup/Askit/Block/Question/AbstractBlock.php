<?php

namespace Swissup\Askit\Block\Question;

use Swissup\Askit\Api\Data\MessageInterface;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Swissup\Askit\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Swissup\Askit\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     *
     * @var int
     */
    protected $_itemTypeId = 0;

    /**
     *
     * @var \Swissup\Askit\Model\Vote\Factory
     */
    protected $_voteFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swissup\Askit\Helper\Config $configHelper
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Swissup\Askit\Model\Vote\Factory $voteFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Swissup\Askit\Helper\Config $configHelper,
        \Swissup\Askit\Helper\Url $urlHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Swissup\Askit\Model\Vote\Factory $voteFactory,
        array $data = []
    ) {

        $this->_customerSession = $customerSession;
        $this->_configHelper = $configHelper;
        $this->_urlHelper = $urlHelper;
        $this->_postDataHelper = $postDataHelper;
        $this->_voteFactory = $voteFactory;
        parent::__construct($context, $data);

        $this->_itemTypeId = (int) $this->getRequest()->getParam(
            'item_type_id',
            $this->_itemTypeId
        );
    }

    /**
     *
     * @return \Magento\Framework\Data\Helper\PostHelper
     */
    public function getPostDataHelper()
    {
        return $this->_postDataHelper;
    }

    /**
     * Get review product post action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('askit/question/save');
    }

    /**
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        $request = $this->getRequest();
        return $request->getParam('product_id', $request->getParam('id', false));
    }

    /**
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getRequest()->getParam('id', false);
    }

    /**
     *
     * @return int
     */
    public function getPageId()
    {
        $request = $this->getRequest();
        return $request->getParam('page_id', $request->getParam('id', false));
    }

    public function setItemTypeId($itemTypeId)
    {
        $this->_itemTypeId = (int) $itemTypeId;
        return $this;
    }

    public function getItemTypeId()
    {
        //page type auto detecting
        if (0 == $this->_itemTypeId) {
            $request = $this->getRequest();
            $fullActionName = $request->getFullActionName();
            switch ($fullActionName) {
                case 'catalog_category_view':
                    $this->_itemTypeId = MessageInterface::TYPE_CATALOG_CATEGORY;
                    break;
                case 'catalog_product_view':
                    $this->_itemTypeId = MessageInterface::TYPE_CATALOG_PRODUCT;
                    break;
                case 'cms_page_view':
                    $this->_itemTypeId = MessageInterface::TYPE_CMS_PAGE;
                    break;
                case 'askit_customer_index':
                default:
                    $this->_itemTypeId = MessageInterface::TYPE_UNKNOWN;
                    break;
            }
        }

        return $this->_itemTypeId;
    }

    public function getItemId()
    {
        $type = $this->getItemTypeId();
        switch ($type) {
            case MessageInterface::TYPE_CATALOG_CATEGORY:
                $itemId = $this->getCategoryId();
                break;
            case MessageInterface::TYPE_CATALOG_PRODUCT:
                $itemId = $this->getProductId();
                break;
            case MessageInterface::TYPE_CMS_PAGE:
                $itemId = $this->getPageId();
                break;
            default:
                $itemId = -1;
                break;
        }
        return $itemId;
    }

    public function getConfigHelper()
    {
        return $this->_configHelper;
    }

    public function getUrlHelper()
    {
        return $this->_urlHelper;
    }

    /**
     *
     * @param  int $id
     * @return boolean
     */
    public function canVoted($id)
    {
        if (!$this->isCustomerLoggedIn()) {
            return false;
        }
        $customerId = (int) $this->_customerSession->getId();

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $model = $objectManager->get('Swissup\Askit\Model\Vote');
        $model = $this->_voteFactory->create();
        if ($model->isVoted($id, $customerId)) {
            return false;
        }
        return true;
    }

    /**
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        if (method_exists($this->_escaper, 'escapeHtmlAttr')) {
            return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
        }
        if ($escapeSingleQuote) {
            $escaper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\ZendEscaper::class);
            return $escaper->escapeHtmlAttr((string) $string);
        }

        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
    }
}
