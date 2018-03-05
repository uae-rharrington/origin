# Ajaxpro
Magento2 module Streamline add to cart function and remove interruptions from the shopping process

### Installation

Run the following commands:
```bash
cd <magento_root>
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/ajaxpro:dev-master --prefer-source
bin/magento module:enable Swissup_Core Swissup_Suggestpage Swissup_Ajaxpro
bin/magento setup:upgrade
bin/magento setup:static-content:deploy
```
