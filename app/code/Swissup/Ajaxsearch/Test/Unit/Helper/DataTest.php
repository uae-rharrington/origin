<?php

namespace Swissup\Ajaxsearch\Test\Unit\Helper;

use Swissup\Ajaxsearch\Helper\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Unit test for \Swissup\Swissup\Block\Init
 */
class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Swissup\Ajaxsearch\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    public function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->getMockForAbstractClass();

        $this->dataHelper = $helper->getObject(
            Data::class,
            [
                'storeManager' => $this->storeManagerMock,
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * @param string $value
     * @param boolean $expected
     * @dataProvider isEnabledProvider
     */
    public function testIsFoldedDesignEnabled($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_FOLDED_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->isFoldedDesignEnabled()
        );
    }

    /**
     * @return array
     */
    public function isEnabledProvider()
    {
        return [['0', false], ['1', true]];
    }

    /**
     * @param string $value
     * @param string $expected
     * @dataProvider getFoldedEffectProvider
     */
    public function testGetFoldedEffect($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_FOLDED_EFFECT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->getFoldedEffect()
        );
    }

    /**
     * @return array
     */
    public function getFoldedEffectProvider()
    {
        return [['', ''], ['baz', 'baz']];
    }

    /**
     * @param string $value0
     * @param string $value1
     * @param string $expected
     * @dataProvider getAdditionalCssClassProvider
     */
    public function testGetAdditionalCssClass($value0, $value1, $expected)
    {
        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Data::CONFIG_PATH_FOLDED_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value0);
        if (1 == $value0) {
            $this->scopeConfigMock->expects($this->at(1))
                ->method('getValue')
                ->with(Data::CONFIG_PATH_FOLDED_EFFECT, ScopeInterface::SCOPE_STORE, null)
                ->willReturn($value1);
        }

        $this->assertEquals(
            $expected,
            $this->dataHelper->getAdditionalCssClass()
        );
    }

    /**
     * @return array
     */
    public function getAdditionalCssClassProvider()
    {
        return [
            [1, 'zoom-in', 'folded zoom-in'],
            [1, 'fade', 'folded fade'],
            [0, 'zoom-in', '']
        ];
    }

    /**
     * @param string $value
     * @param boolean $expected
     * @dataProvider isEnabledProvider
     */
    public function testIsAutocompleteEnabled($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_AUTOCOMPLETE_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->isAutocompleteEnabled()
        );
    }

    public function testGetAutocompleteLimit()
    {
        $value = '5';
        $expected = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_AUTOCOMPLETE_LIMIT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->dataHelper->getAutocompleteLimit());
    }

    /**
     * @param string $value
     * @param boolean $expected
     * @dataProvider isEnabledProvider
     */
    public function testIsProductEnabled($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_PRODUCT_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->isProductEnabled()
        );
    }

    public function testGetProductLimit()
    {
        $value = '5';
        $expected = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_PRODUCT_LIMIT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->dataHelper->getProductLimit());
    }

    /**
     * @param string $value
     * @param boolean $expected
     * @dataProvider isEnabledProvider
     */
    public function testIsCategoryEnabled($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_CATEGORY_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->isCategoryEnabled()
        );
    }

    public function testGetCategoryLimit()
    {
        $value = '5';
        $expected = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_CATEGORY_LIMIT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->dataHelper->getCategoryLimit());
    }

    /**
     * @param string $value
     * @param boolean $expected
     * @dataProvider isEnabledProvider
     */
    public function testIsPageEnabled($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_PAGE_ENABLE, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->dataHelper->isPageEnabled()
        );
    }

    public function testGetPageLimit()
    {
        $value = '5';
        $expected = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Data::CONFIG_PATH_PAGE_LIMIT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->dataHelper->getPageLimit());
    }
}
