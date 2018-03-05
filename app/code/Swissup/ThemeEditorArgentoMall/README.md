# Argento Mall theme editor

Installation

```bash
cd <magento_root>
composer config repositories.swissup/theme-editor-argento-mall vcs git@github.com:swissup/theme-editor-argento-mall.git
composer require swissup/theme-editor-argento-mall:dev-master --prefer-source
bin/magento module:enable Swissup_ThemeEditorArgentoMall
bin/magento setup:upgrade
```
