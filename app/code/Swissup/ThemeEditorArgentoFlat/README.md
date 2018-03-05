# Argento Flat theme editor

Installation

```bash
cd <magento_root>
composer config repositories.swissup/theme-editor-argento-flat vcs git@github.com:swissup/theme-editor-argento-flat.git
composer require swissup/theme-editor-argento-flat:dev-master --prefer-source
bin/magento module:enable Swissup_ThemeEditorArgentoFlat
bin/magento setup:upgrade
```
