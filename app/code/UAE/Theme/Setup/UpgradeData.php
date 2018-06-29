<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

namespace UAE\Theme\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Cms\Model\BlockFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        BlockFactory $blockFactory
    ) {
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Add static block for footer links
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $footerLinksContent = <<<FOOTER_LINKS_CONTENT
<section>
    <h4>More Ways to Shop</h4>
    <ul>
        <li><a href="{{config path='web/unsecure/base_url'}}wishlist">My Favorites</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}quickorder">Quick Item Entry</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}">Store Locator</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}">Gift Cards</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}">Visit a Retail Store</a></li>
    </ul>
</section>
<section>
    <h4>My Account</h4>
    <ul>
        <li><a href="{{config path='web/unsecure/base_url'}}sales/order/history">My Order Status</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}giftcard/customer">My Gift Card Balance</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}customer/account/edit">Update My Information</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}newsletter/manage">Send Me Exclusive Deals!</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}">My Retail Rewards</a></li>
    </ul>
</section>
<section>
    <h4>Customer Service</h4>
    <ul>
        <li><a href="{{config path='web/unsecure/base_url'}}how-to-order">How to Order</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}volume-discount">Volume Discounts</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}return-policy">Return Policy</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}shipping-information">Shipping Information</a></li>
    </ul>
</section>
<section>
    <h4>About Us</h4>
    <ul>
        <li><a href="{{config path='web/unsecure/base_url'}}company-information">Company Information</a></li>
        <li><a href="https://www.google.com/shopping/ratings/account/metrics?q=unitednow.com&c=GLOBAL&v=1">Our Customers Say</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}faq">FAQ</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}contact">Contact Us</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}">YOUnited Blog</a></li>
    </ul>
</section>
<section>
    <h4>Safe &amp; Secure Shopping</h4>
    <ul>
        <li><a href="{{config path='web/unsecure/base_url'}}privacy-policy">Privacy Policy</a></li>
        <li><a href="{{config path='web/unsecure/base_url'}}health-safety">Health & Safety Info</a></li>
    </ul>
</section>
FOOTER_LINKS_CONTENT;

            $blockData = [
                'title' => 'Footer Links',
                'identifier' => 'custom_footer_links',
                'content' => $footerLinksContent,
                'stores' => [0],
                'is_active' => 1
            ];

            $this->blockFactory->create()->setData($blockData)->save();
        }

        $setup->endSetup();
    }
}
