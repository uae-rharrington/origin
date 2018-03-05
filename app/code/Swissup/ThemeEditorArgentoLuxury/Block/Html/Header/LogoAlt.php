<?php
namespace Swissup\ThemeEditorArgentoLuxury\Block\Html\Header;

/**
 * Alt logo page header block
 */
class LogoAlt extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'logo-alt.phtml';

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getLogoUrl()
    {
        $altLogoPath = $this->_scopeConfig->getValue(
            'swissup_argento_luxury/homepage/logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $folderName = 'swissup/argento/luxury/images';
        $path = $folderName . '/' . $altLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($altLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } else {
            $url = parent::_getLogoUrl();
        }

        return $url;
    }
}
