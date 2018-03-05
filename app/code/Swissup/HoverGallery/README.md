# Hover Gallery

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/hover-gallery:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_HoverGallery

bin/magento setup:upgrade
```

Insert code in template `/Magento_Catalog/templates/product/list.phtml`

```php
<?php
    if ($this->helper('Magento\Catalog\Helper\Data')->isModuleOutputEnabled('Swissup_HoverGallery')) {
        echo $this->helper('Swissup\HoverGallery\Helper\Data')->getHoverImage($_product, $productImage->getWidth(), $productImage->getHeight());
    }
?>
```
