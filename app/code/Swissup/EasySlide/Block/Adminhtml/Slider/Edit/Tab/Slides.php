<?php
namespace Swissup\EasySlide\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Framework\UrlInterface;

class Slides extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Convert\DataObject $objectConverter,
        array $data = []
    ) {
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;

        parent::__construct($context, $registry, $formFactory, $data);
    }

   /**
    *
    * @return \Swissup\ProLabels\Model\Label
    */
    protected function _getModel()
    {
        return $this->_coreRegistry->registry('easyslide');
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Swissup\ProLabels\Model\Label */
        $model = $this->_getModel();

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Swissup_EasySlide::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('slider_');

        $fieldset = $form->addFieldset(
            'slides_fieldset',
            ['legend' => __('Slides'), 'class' => 'fieldset-wide']
        );

        // $fieldset->addType(
        //     'slide_gallery',
        //     'Swissup\EasySlide\Block\Adminhtml\Slider\Helper\Form\SlideGallery');

        // $fieldset->addField(
        //     'slides',
        //     'Swissup\EasySlide\Block\Adminhtml\Slider\Helper\Form\SlideGallery',
        //     [
        //         'name' => 'slides',
        //         'label' => __('Slides'),
        //         'title' => __('Slides'),
        //         'required' => false,
        //         'slider_id' => $model->getSliderId(),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Slides');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Slides');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
