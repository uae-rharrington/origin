# ProLabels

#### Installation

```bash
cd <magento_root>
composer config repositories.swissup/pro-labels vcs git@github.com:swissup/prolabels.git
composer require swissup/pro-labels --prefer-source
bin/magento module:enable Swissup_ProLabels
bin/magento setup:upgrade
```
#### Labels in Catalog and Search pages

###### Product Image Labels
replace list and grid image code
```php
<a href="<?php /* @escapeNotVerified */ echo $_product->getProductUrl() ?>" class="product photo product-item-photo" tabindex="-1">
    <?php echo $productImage->toHtml(); ?>
</a>
```
* to
```php
<div class="prolabels-wrapper">
    <?php
        $prolabelsCatalogHelper = $this->helper('Swissup\ProLabels\Helper\Catalog');
        echo $prolabelsCatalogHelper->getProductLabels($_product);
    ?>
    <?php // Product Image ?>
    <a href="<?php /* @escapeNotVerified */ echo $_product->getProductUrl() ?>" class="product photo product-item-photo" tabindex="-1">
        <?php echo $productImage->toHtml(); ?>
    </a>
</div>
```
###### Content Catalog Labels
add code to any place in catalog list.phtml
```php
<div class="prolabels-content-wrapper">
    <?php echo $prolabelsCatalogHelper->getContentLabels($_product); ?>
</div>
```

#### Reindex Labels From Command Line
```bash
cd <magento_root>
bin/magento prolabels:reindex:all
```