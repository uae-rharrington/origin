<?php
namespace Swissup\ProLabels\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
     /**
     * product upload model
     *
     * @var \Swissup\ProLabels\Model\Label\ProductUpload
     */
    protected $productUploadModel;
    /**
     * product image model
     *
     * @var \Swissup\ProLabels\Model\Label\ProductImage
     */
    protected $productImageModel;

    /**
     * category upload model
     *
     * @var \Swissup\ProLabels\Model\Label\CategoryUpload
     */
    protected $categoryUploadModel;
    /**
     * category image model
     *
     * @var \Swissup\ProLabels\Model\Label\CategoryImage
     */
    protected $categoryImageModel;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Swissup\ProLabels\Model\Label\ProductImage $productImageModel,
        \Swissup\ProLabels\Model\Label\ProductUpload $productUploadModel,
        \Swissup\ProLabels\Model\Label\CategoryImage $categoryImageModel,
        \Swissup\ProLabels\Model\Label\CategoryUpload $categoryUploadModel
    ) {
        $this->productUploadModel = $productUploadModel;
        $this->productImageModel = $productImageModel;
        $this->categoryUploadModel = $categoryUploadModel;
        $this->categoryImageModel = $categoryImageModel;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Swissup_ProLabels::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Swissup\ProLabels\Model\Label $model */
            $model = $this->_objectManager->create('Swissup\ProLabels\Model\Label');

            $id = $this->getRequest()->getParam('label_id');
            if ($id) {
                $model->load($id);
            }

            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);

            $model->loadPost($data);
            /*
             ** Product Label Image Upload
             */
            $productImage = $this->productUploadModel->uploadFileAndGetName(
                'product_image',
                $this->productImageModel->getBaseDir(),
                $data
            );
            $model->setProductImage($productImage);
            $model->setProductImageWidth(0);
            $model->setProductImageHeight(0);
            /*
            ** Category Label Image Upload
             */
            $categoryImage = $this->categoryUploadModel->uploadFileAndGetName(
                'category_image',
                $this->categoryImageModel->getBaseDir(),
                $data
            );
            $model->setCategoryImage($categoryImage);
            $model->setCategoryImageWidth(0);
            $model->setCategoryImageHeight(0);

            $model->setCustomerGroups(serialize($data['customer_groups']));
            $model->setStoreId(serialize($data['stores']));
            $model->setStatus($data['label_status']);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('Label has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['label_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the label.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['label_id' => $this->getRequest()->getParam('label_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
