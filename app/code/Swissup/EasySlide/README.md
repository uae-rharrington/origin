# EasySlide

#### Installation

```bash
cd <magento_root>
composer config repositories.swissup/easy-slide vcs git@github.com:swissup/easyslide.git
composer require swissup/easy-slide
bin/magento module:enable Swissup_EasySlide
bin/magento setup:upgrade
```
###### Add slider in layout xml
```xml
<block class="Swissup\EasySlide\Block\Slider" name="easyslide.slider.name">
    <arguments>
        <argument name="identifier" xsi:type="string">slider-config-identifier</argument>
    </arguments>
</block>
```
