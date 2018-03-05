# SEO Urls

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/seo-urls:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_SeoCore\
    Swissup_SeoUrls

bin/magento setup:upgrade
```
