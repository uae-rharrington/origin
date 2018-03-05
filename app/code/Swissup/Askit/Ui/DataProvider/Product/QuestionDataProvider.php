<?php
namespace Swissup\Askit\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Swissup\Askit\Ui\DataProvider\Product\MessageDataProvider;
use Swissup\Askit\Model\ResourceModel\Question\Grid\CollectionFactory;
use Swissup\Askit\Model\ResourceModel\Question\Grid\Collection;

/**
 * Class QuestionDataProvider
 *
 * @method Collection getCollection
 */
class QuestionDataProvider extends MessageDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {

        $this->getCollection()
            ->addQuestionFilter(0);

        return parent::getData();
    }
}
