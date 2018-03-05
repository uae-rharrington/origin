# Highlight

## Installation

Looking for a release installation instuctions? 
[See extension manual](http://documentation.swissuplabs.com/m2/highlight/).

#### Composer based installation

1. Open `<magento_root>/composer.json` and change `minimum-stability` setting to `dev`.
2. Run the following commands:

    ```bash
    cd <magento_root>
    composer config repositories.swissup composer http://swissup.github.io/packages/
    composer require swissup/highlight:dev-master --prefer-source
    bin/magento module:enable Swissup_Core Swissup_Highlight
    bin/magento setup:upgrade
    ```
