<?php

namespace Swissup\SeoUrls\Plugin;

class RedirectCmsToHomepage
{
    /**
     * @var \Swissup\SeoUrls\Helper\Data
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @param \Swissup\SeoUrls\Helper\Data $helper
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Data $helper,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\ActionFactory $actionFactory
    ) {
        $this->helper = $helper;
        $this->response = $response;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Around pluging \Magento\Cms\Controller\Router::match
     *
     * @param  \Magento\Cms\Controller\Router $subject
     * @param  \Closure $proceed
     * @param  \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function aroundMatch(
        \Magento\Cms\Controller\Router $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $identifier = trim($request->getPathInfo(), '/');
        if ($identifier == $this->helper->getHomepageIdentifier()
            && $this->helper->isHomepageRedirect()
        ) {
            $this->response->setRedirect($this->helper->getHomepageUrl(), 301);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
        }

        return $proceed($request);
    }
}
