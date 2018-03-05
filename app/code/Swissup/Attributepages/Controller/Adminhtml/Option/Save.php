<?php
namespace Swissup\Attributepages\Controller\Adminhtml\Option;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\App\Filesystem\DirectoryList;
use Swissup\Attributepages\Model\Entity as AttributepagesEntity;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Swissup_Attributepages::option_save';
    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $validatorFactory;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    /**
     * upload model
     *
     * @var \Swissup\Attributepages\Model\Upload
     */
    protected $uploadModel;
    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $attrpageSession;
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Session\Generic $attrpageSession
     * @param \Swissup\Attributepages\Model\Upload $uploadModel
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Session\Generic $attrpageSession,
        \Swissup\Attributepages\Model\Upload $uploadModel
    ) {
        parent::__construct($context);
        $this->validatorFactory = $validatorFactory;
        $this->storeManager = $storeManager;
        $this->fileSystem = $fileSystem;
        $this->attrpageSession = $attrpageSession;
        $this->uploadModel = $uploadModel;
    }
    /**
     * Save action
     */
    public function execute()
    {
         if (!$data = $this->getRequest()->getPost('attributepage')) {
            $this->_redirect('*/*/');
            return;
        }
        $model = $this->_objectManager->create('Swissup\Attributepages\Model\Entity');
        if ($id = $this->getRequest()->getParam('entity_id')) {
            $model->load($id);
        }

        if (!$this->_validatePostData($data)) {
            $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
            return;
        }
        try {
            $mediaPath = $this->getBaseDir(AttributepagesEntity::IMAGE_PATH);
            foreach (['image', 'thumbnail'] as $key) {
                try {
                    $imageName = $this->uploadModel
                        ->uploadFileAndGetName(
                            $key,
                            $mediaPath,
                            $data,
                            ['jpg','jpeg','gif','png', 'bmp']
                        );
                    $data[$key] = $imageName;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                if (isset($data[$key]) && is_array($data[$key])) {
                    if (!empty($data[$key]['delete'])) {
                        @unlink($mediaPath . $data[$key]['value']);
                        $data[$key] = null;
                    } else {
                        $data[$key] = $data[$key]['value'];
                    }
                }
            }

            $model->addData($data);
            $model->save();

            $this->messageManager->addSuccess(__('The page has been saved.'));
            $this->attrpageSession->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['entity_id' => $model->getId(), '_current'=>true]);
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->attrpageSession->setFormData($data);
        $this->_redirect('*/*/edit', ['_current'=>true]);
    }
    /**
     * Validate post data
     *
     * @param array $data
     * @return bool Return FALSE if some item is invalid
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml'])) {
            /** @var $validatorCustomLayout \Magento\Framework\View\Model\Layout\Update\Validator */
            $validatorCustomLayout = $this->validatorFactory->create();
            if (!$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addError($message);
            }
        }
        return $errorNo;
    }
    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir($path, $directoryCode = DirectoryList::MEDIA)
    {
        return $this->fileSystem
            ->getDirectoryWrite($directoryCode)
            ->getAbsolutePath($path);
    }
}
