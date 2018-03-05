<?php
namespace Swissup\EasySlide\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Swissup_EasySlide::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Swissup\ProLabels\Model\Label $model */
            $sliderModel = $this->_objectManager->create('Swissup\EasySlide\Model\Slider');

            $id = $this->getRequest()->getParam('slider_id');
            if ($id) {
                $sliderModel->load($id);
            }

            $sliderConfig = [
                'theme' => $data['theme'],
                'direction' => $data['direction'],
                'speed' => $data['speed'],
                'pagination' => $data['pagination'],
                'navigation' => $data['navigation'],
                'scrollbar' => $data['scrollbar'],
                'scrollbarHide' => $data['scrollbarHide'],
                'autoplay' => $data['autoplay'],
                'effect' => $data['effect']
            ];

            $sliderData = [
                'identifier' => $data['identifier'],
                'title' => $data['title'],
                'slider_config' => serialize($sliderConfig),
                'is_active' => $data['is_active']
            ];

            $sliderModel->addData($sliderData);
            try {
                // Save Slider
                $sliderModel->save();

                // Save Slides
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);

                if (array_key_exists('product', $data)) {
                    foreach ($data['product']['media_gallery']['images'] as $id => $item) {
                        $slideModel = $this->_objectManager->create('Swissup\EasySlide\Model\Slides');
                        $slideId = $item['slide_id'];
                        if ($slideId) {
                            $slideModel->load($item['slide_id']);
                        }

                        if (array_key_exists('removed', $item)) {
                            if (1 == (int)$item['removed']) {
                                $filePath = $mediaDirectory->getAbsolutePath("easyslide") . "/" . $item['file'];
                                if (is_readable($filePath)) {
                                    unlink($filePath);
                                }
                                if ($slideModel->getId()) {
                                    $slideModel->delete();
                                }
                                continue;
                            }
                        }

                        $slideData = [
                            'slider_id' => $sliderModel->getId(),
                            'title' => $item['title'],
                            'image' => $item['file'],
                            'description' => $item['description'],
                            'desc_position' => $item['desc_position'],
                            'desc_background' => $item['desc_background'],
                            'url' => $item['link'],
                            'target' => $item['target'],
                            'sort_order' => $item['position'],
                            'is_active' => $item['is_active']
                        ];
                        $slideModel->addData($slideData);
                        $slideModel->save();
                    }
                }

                $this->messageManager->addSuccess(__('Slider has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['slider_id' => $sliderModel->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
