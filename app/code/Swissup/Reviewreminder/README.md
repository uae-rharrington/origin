# Review Reminder

### Installation

```bash
cd <magento_root>
composer config repositories.swissup/reviewreminder vcs git@github.com:swissup/reviewreminder.git
composer require swissup/reviewreminder
bin/magento module:enable Swissup_Reviewreminder
bin/magento setup:upgrade
```
