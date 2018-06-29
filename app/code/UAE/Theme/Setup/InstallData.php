<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

namespace UAE\Theme\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\Store;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    const THEME_NAME = 'UnitedArtsEducation/unitednow';

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Theme\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Theme\Model\Theme\Registration
     */
    private $themeRegistration;

    /**
     * InstallData constructor.
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Theme\Model\Config $config
     * @param \Magento\Theme\Model\Theme\Registration $themeRegistration
     */
    public function __construct(
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionFactory,
        \Magento\Theme\Model\Config $config,
        \Magento\Theme\Model\Theme\Registration $themeRegistration
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->themeRegistration = $themeRegistration;
    }

    /**
     * Install function
     *
     * Assign theme to default store
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->themeRegistration->register();

        $themes = $this->collectionFactory->create()->loadRegisteredThemes();

        /**
         * @var \Magento\Theme\Model\Theme $theme
         */
        foreach ($themes as $theme) {
            if ($theme->getCode() == self::THEME_NAME) {
                $this->config->assignToStore($theme,
                    [Store::DISTRO_STORE_ID]
                );
            }
        }

        $setup->endSetup();
    }
}
