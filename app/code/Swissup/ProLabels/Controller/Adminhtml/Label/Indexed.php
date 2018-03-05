<?php

namespace Swissup\ProLabels\Controller\Adminhtml\Label;

class Indexed extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('label_id');
        $model = $this->_objectManager->create('Swissup\ProLabels\Model\Label');
        $model->load($id);
        $this->_coreRegistry->register('prolabel', $model);
        $resultLayout = $this->resultLayoutFactory->create();
        //var_dump($resultLayout->getFullActionName());die;
        $resultLayout->getLayout()->getBlock('label_index_listing');
            //->setProductsRelated($this->getRequest()->getPost('products_related', null));
        return $resultLayout;
    }
}
