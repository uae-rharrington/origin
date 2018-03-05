# SoldTogether

#### Installation

```bash
cd <magento_root>
composer config repositories.swissup/sold-together vcs git@github.com:swissup/soldtogether.git
composer require swissup/sold-together:dev-master --prefer-source
bin/magento module:enable Swissup_SoldTogether Swissup_SlickCarousel
bin/magento setup:upgrade
```

You can add soldtogether blocks to the sales email template using the following code:

```
{{block class='Swissup\\SoldTogether\\Block\\Email\\Customer' area='frontend' template='Swissup_SoldTogether::email/customer.phtml' order=$order}}
{{block class='Swissup\\SoldTogether\\Block\\Email\\Order' area='frontend' template='Swissup_SoldTogether::email/order.phtml' order=$order}}
```
