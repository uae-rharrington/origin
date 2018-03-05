<?php

namespace Swissup\Ajaxsearch\Test\Unit\Model\Catalog\Category;

use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\CatalogSearch\Model\Autocomplete\DataProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

use Swissup\Ajaxsearch\Helper\Data as ConfigHelper;

class DataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataProvider
     */
    private $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Search\Model\Query |\PHPUnit_Framework_MockObject_MockObject
     */
    private $query;

    /**
     * @var \Swissup\Ajaxsearch\Model\QueryFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryFactoryMock;

    /**
     * @var \Magento\Search\Model\Autocomplete\ItemFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemFactoryMock;

    /**
     * @var \Magento\Search\Model\ResourceModel\Query\Collection |\PHPUnit_Framework_MockObject_MockObject
     */
    private $suggestCollection;

    /**
     *  @var \Swissup\Ajaxsearch\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configHelperMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection|MockObject
     */
    private $collectionMock;

    protected function setUp()
    {
        $this->collectionMock = $this->getMockBuilder(
            \Magento\Catalog\Model\ResourceModel\Category\Collection::class
        )
            ->setMethods([
                'getIterator',
                'setPageSize'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->query = $this->getMockBuilder(\Swissup\Ajaxsearch\Model\Query\Catalog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQueryText', 'getSuggestCollection'])
            ->getMock();

        $this->query->expects($this->any())
            ->method('getSuggestCollection')
            ->willReturn($this->collectionMock);

        $this->queryFactoryMock = $this->getMockBuilder(\Swissup\Ajaxsearch\Model\queryFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $this->queryFactoryMock->expects($this->any())
            ->method('get')
            ->willReturn($this->query);

        $this->itemFactoryMock = $this->getMockBuilder(\Magento\Search\Model\Autocomplete\ItemFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->configHelperMock = $this->getMockBuilder(\Swissup\Ajaxsearch\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            \Swissup\Ajaxsearch\Model\Catalog\Category\DataProvider::class,
            [
                'queryFactory' => $this->queryFactoryMock,
                'itemFactory' => $this->itemFactoryMock,
                'configHelper' => $this->configHelperMock,
            ]
        );
    }

    public function testGetItemsDisable()
    {
        $this->configHelperMock->expects($this->once())
            ->method('isCategoryEnabled')
            ->willReturn(false);

        $result = $this->model->getItems();

        $this->assertEquals([], $result);
    }

    public function testGetItems()
    {
        $queryString = 'cat';
        $expected = [
            '_type' => 'category',
            'title' => $queryString,
            'num_results' => '',
            'url' => '/' . $queryString
        ];
        $limit = 1;

        $this->configHelperMock->expects($this->once())
            ->method('isCategoryEnabled')
            ->willReturn(true);

        $collection = [
            ['name' => 'cat1', 'url' => '/cat1'],
            ['name' => 'cat2', 'url' => '/cat2'],
            ['name' => 'cat3', 'url' => '/cat3'],
            ['name' => $queryString, 'url' => '/' . $queryString]
        ];
        $collectionData = [];
        foreach ($collection as $collectionItem) {
            $collectionData[] = new \Magento\Framework\DataObject($collectionItem);
        }
        $this->collectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($collectionData)));

        $this->query->expects($this->any())
            ->method('getQueryText')
            ->willReturn($queryString);

        $itemMock = $this->getMockBuilder(\Magento\Search\Model\Autocomplete\Item::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTitle', 'toArray', 'getData'])
            ->getMock();

        $itemMock->expects($this->any())
            ->method('getTitle')
            ->will($this->onConsecutiveCalls(
                $queryString,
                'cat1',
                'cat2',
                'cat3'
            ));
        $itemMock->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($expected));

        $this->itemFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($itemMock);

        $result = $this->model->getItems();
        $this->assertEquals($expected, $result[0]->toArray());
    }
}
