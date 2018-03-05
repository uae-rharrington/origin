# Lightbox Pro

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/lightboxpro:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Lightboxpro

bin/magento setup:upgrade
```
