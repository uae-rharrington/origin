# SeoHtmlSitemap

### Installation

```bash
cd <magento_root>
```

Download and install composer module
```bash
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/seo-html-sitemap:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_SeoCore\
    Swissup_SeoHtmlSitemap

bin/magento setup:upgrade
```
