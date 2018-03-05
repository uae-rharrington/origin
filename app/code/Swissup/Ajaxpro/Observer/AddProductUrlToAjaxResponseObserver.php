<?php
namespace Swissup\Ajaxpro\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as jsonDataHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Http\Header\HeaderInterface as HttpHeaderInterface;

class AddProductUrlToAjaxResponseObserver implements ObserverInterface
{
    const ENABLE = 'ajaxpro/main/product';

    /**
     * json helper
     *
     * @var jsonDataHelper
     */
    protected $jsonDataHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param jsonDataHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        jsonDataHelper $jsonDataHelper,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->jsonDataHelper = $jsonDataHelper;
        $this->productRepository = $productRepository;
        $this->storeManager =  $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $isEnabled = (bool) $this->getConfig(self::ENABLE);
        if (!$isEnabled) {
            return $this;
        }

        $event = $observer->getEvent();
        /** @var $request \Magento\Framework\App\RequestInterface */
        $request = $event->getRequest();
        if (!$request->isAjax() || $request->getParam('return_url')) {
            return $this;
        }

        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $event->getControllerAction();
        /** @var $response \Magento\Framework\App\ResponseInterface */
        $response = $controller->getResponse();

        $resultJsonData = $response->getContent();
        if (empty($resultJsonData)) {
            return $this;
        }
        json_decode($resultJsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this;
        }
        $resultJsonData = $this->jsonDataHelper->jsonDecode($resultJsonData);

        if (!isset($resultJsonData['backUrl'])) {
            return $this;
        }
        $product = $this->initProduct($request);
        if (!$product || !$this->isProductHasOptions($product)) {
            return $this;
        }
        $resultJsonData['action'] = $request->getFullActionName();

        $additional = [];
        $additional['_escape'] = true;
        $additional['_query'] = [];
        $additional['_query']['options'] = 'cart';
        $resultJsonData['ajaxpro']['product'] = [
            'id' => $product->getId(),
            'product_url' => $product->getUrlModel()->getUrl($product, $additional),
            'has_options' => true
        ];

        $cacheControlHeader = $response->getHeader('Cache-Control');

        $resultJsonData = $this->jsonDataHelper->jsonEncode($resultJsonData);
        $response
            ->clearHeaders()
            ->setHttpResponseCode(200)
            ->representJson($resultJsonData)
            ->send();
        if ($cacheControlHeader instanceof HttpHeaderInterface) {
            $response->setHeader('Cache-Control', $cacheControlHeader->getFieldValue());
        }
        // exit;
        return $this;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function initProduct(\Magento\Framework\App\RequestInterface $request)
    {
        $productId = (int) $request->getParam('product');
        if ($productId) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     *
     * @param  \Magento\Catalog\Model\Product  $product
     * @return boolean
     */
    protected function isProductHasOptions(\Magento\Catalog\Model\Product $product)
    {
        if (null === $product) {
            $product = $this->initProduct();
        }
        if ($product) {
            $typeInstance = $product->getTypeInstance();
            return $typeInstance && ($typeInstance->hasRequiredOptions($product)
                || $typeInstance->hasOptions($product));

        }
        return false;
    }

    /**
     *
     * @param  string $key
     * @param  string $scope
     * @return string
     */
    protected function getConfig($key, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($key, $scope);
    }
}
