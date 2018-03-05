# SEO Pager

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/seo-pager:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_SeoCore\
    Swissup_SeoPager

bin/magento setup:upgrade
```
