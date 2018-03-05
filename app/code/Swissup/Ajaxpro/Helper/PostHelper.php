<?php

namespace Swissup\Ajaxpro\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class PostHelper extends \Magento\Framework\Data\Helper\PostHelper
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param Context $context
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        Context $context,
        UrlHelper $urlHelper
    ) {
        parent::__construct($context, $urlHelper);
        $this->urlHelper = $urlHelper;
    }

    /**
     * get data for post by javascript in format acceptable to $.mage.dataPost widget
     *
     * @param string $url
     * @param array $data
     * @return string
     */
    public function getPostData($url, array $data = [])
    {
        $param = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;
        if (!isset($data[$param])) {
            $data[$param] = $this->urlHelper->getEncodedUrl();
            $request = $this->_request;
            if ($request->isAjax()) {
                $uenc = $this->_request->getParam($param);
                if (!empty($uenc)) {
                    $data[$param] = $uenc;
                }
            }
        }
        return json_encode(['action' => $url, 'data' => $data]);
    }
}
