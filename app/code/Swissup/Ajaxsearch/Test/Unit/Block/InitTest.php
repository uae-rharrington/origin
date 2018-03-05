<?php

namespace Swissup\Ajaxsearch\Test\Unit\Block;

use Swissup\Ajaxsearch\Block\Init;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Json\EncoderInterface;

/**
 * Unit test for \Swissup\Swissup\Block\Init
 */
class InitTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jsonEncoderMock;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeFormatMock;

    /**
     * @var \Magento\Framework\Module\PackageInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packageInfoMock;

    /**
     * @var \Swissup\Swissup\Block\Init
     */
    protected $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    public function setUp()
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->contextMock = $this->createMock(\Magento\Catalog\Block\Product\Context::class);
        $this->contextMock->expects($this->once())
            ->method('getScopeConfig')
            ->will($this->returnValue($this->scopeConfigMock));

        $this->jsonEncoderMock = $this->createMock(\Magento\Framework\Json\EncoderInterface::class);
        $this->jsonEncoderMock->expects($this->any())
            ->method('encode')
            ->will(
                $this->returnCallback(
                    function ($value) {
                        return json_encode($value);
                    }
                )
            );
        $this->localeFormatMock = $this->createMock(\Magento\Framework\Locale\FormatInterface::class);
        $this->localeFormatMock->expects($this->any())
            ->method('getPriceFormat')
            ->will($this->returnValue([
                "pattern" => "$%s",
                // "precision" => 2,
                // "requiredPrecision" => 2,
                // "decimalSymbol" => ".",
                // "groupSymbol" => ",",
                // "groupLength" => 3,
                // "integerRequired" => 1
            ]));

        $this->packageInfoMock = $this->createMock(\Magento\Framework\Module\PackageInfo::class);
        $this->packageInfoMock->expects($this->any())
            ->method('getModuleName')
            ->will($this->returnValue('Swissup_Ajaxsearch'));
        $this->packageInfoMock->expects($this->any())
            ->method('getVersion')
            ->will($this->returnValue('1.2.7'));

        $this->model = new Init(
            $this->contextMock,
            $this->jsonEncoderMock,
            $this->localeFormatMock,
            $this->packageInfoMock
        );
    }

    /**
     * @param string $value
     * @param array $expected
     * @dataProvider isHintDataProvider
     */
    public function testIsHint($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Init::HINT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals(
            $expected,
            $this->model->isHint()
        );
    }

    /**
     * @return array
     */
    public function isHintDataProvider()
    {
        return [['0', false], ['1', true]];
    }

    /**
     * @param string $value
     * @param array $expected
     * @dataProvider isHighligthDataProvider
     */
    public function testIsHighligth($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Init::HIGHLIGHT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->model->isHighligth());
    }

    /**
     * @return array
     */
    public function isHighligthDataProvider()
    {
        return [['0', false], ['1', true]];
    }

    public function testGetLimit()
    {
        $value = '5';
        $expected = 5;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Init::LIMIT, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->model->getLimit());
    }

    /**
     * @param string $value
     * @param array $expected
     * @dataProvider getClassNamesDataProvider
     */
    public function testGetClassNames($value, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Init::CLASSNAMES, ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertEquals($expected, $this->model->getClassNames());
    }

    /**
     * @return array
     */
    public function getClassNamesDataProvider()
    {
        return [
            [
                [
                'input'=> 'tt-input',
                // 'hint'=> 'tt-hint',
                // 'menu'=> 'tt-menu block-swissup-ajaxsearch-results',
                // 'dataset'=> 'tt-dataset products wrapper list products-list',
                // 'suggestion'=> 'tt-suggestion',
                // 'empty'=> 'tt-empty',
                // 'open'=> 'tt-open',
                // 'cursor'=> 'tt-cursor',
                // 'highlight'=> 'tt-highlight'
                ],
                '{"input":"tt-input"}'
            ],
            [[], '[]']
        ];
    }

    public function testGetJsonConfig()
    {
        $expected = '{"priceFormat":{"pattern":"$%s"},"package":"swissup\/ajaxsearch","module":"Swissup_Ajaxsearch","version":"1.2.7"}';
        $this->assertEquals($expected, $this->model->getJsonConfig());
    }
}
