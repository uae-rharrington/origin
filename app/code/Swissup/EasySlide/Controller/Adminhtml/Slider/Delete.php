<?php
namespace Swissup\EasySlide\Controller\Adminhtml\Slider;

use Magento\Framework\App\Filesystem\DirectoryList;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Swissup_EasySlide::delete');
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('slider_id');
        if ($id) {
            try {
                $sliderModel = $this->_objectManager->create('Swissup\EasySlide\Model\Slider');
                $slidesModel = $this->_objectManager->create('Swissup\EasySlide\Model\Slides');
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $slides = $slidesModel->getSlides($id);
                foreach ($slides as $slide) {
                    $image = $mediaDirectory->getAbsolutePath("easyslide") . "/" . $slide['image'];
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
                $sliderModel->load($id);
                $sliderModel->delete();
                $this->messageManager->addSuccess(__('Slider was deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['slider_id' => $id]);
            }
        }
        $this->messageManager->addError(__('Can\'t find a slider to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
