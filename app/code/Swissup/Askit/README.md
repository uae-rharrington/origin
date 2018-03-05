# Askit
### Installation

```bash
cd <magento_root>
```

Download and install composer module 
```bash
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/askit
composer require swissup/askit:dev-master --prefer-source --ignore-platform-reqs
bin/magento module:enable Swissup_Askit Swissup_Core
bin/magento setup:upgrade
```
