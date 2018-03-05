# Navigationpro

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/navigationpro:dev-master --prefer-source
bin/magento module:enable\
    Swissup_Core\
    Swissup_Navigationpro

bin/magento setup:upgrade
```

