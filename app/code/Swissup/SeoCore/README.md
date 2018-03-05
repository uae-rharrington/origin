# Swissup SEO Core

Dummy SEO Core module. It's purpose is to add swissup menu and config sections.

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/seo-core --prefer-source
bin/magento module:enable Swissup_SeoCore
bin/magento setup:upgrade
```
