# Ajaxsearch
Magento2 module for advanced searching

### Installation

Run the following commands:
```bash
cd <magento_root>
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/ajaxsearch:dev-master --prefer-source
bin/magento module:enable Swissup_Core Swissup_Ajaxsearch
bin/magento setup:upgrade
bin/magento setup:static-content:deploy
```
