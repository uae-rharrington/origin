<?php
namespace Swissup\ProLabels\Block\Adminhtml\Label\Edit\Tab;

use Magento\Framework\UrlInterface;

class Category extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

   /**
    *
    * @return \Swissup\Askit\Model\Item
    */
    protected function _getModel()
    {
        return $this->_coreRegistry->registry('prolabel');
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Swissup\Askit\Model\Item */
        $model = $this->_getModel();
        // \Zend_Debug::dump($model->getData());

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Swissup_ProLabel::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('label_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Settings'), 'class' => 'fieldset-wide']
        );

        $fieldset->addType('category_image', 'Swissup\ProLabels\Block\Adminhtml\Label\Helper\CategoryImage');

        $fieldset->addField(
            'category_position',
            'select',
            [
                'label' => __('Position'),
                'title' => __('Position'),
                'name' => 'category_position',
                'required' => true,
                'options' => [ "top-left" => __('Top-Left'),
                                "top-center" => __('Top-Center'),
                                "top-right" => __('Top-Right'),
                                "middle-left" => __('Middle-Left'),
                                "middle-center" => __('Middle-Center'),
                                "middle-right" => __('Middle-Right'),
                                "bottom-left" => __('Bottom-Left'),
                                "bottom-center" => __('Bottom-Center'),
                                "bottom-right" => __('Bottom-Right'),
                                "content" => __('Content')],
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'category_image',
            'category_image',
            [
                'title' => __('Label Image'),
                'label' => __('Label Image'),
                'name' => 'category_image',
                'note' => 'Allow image type: jpg, jpeg, gif, png'
            ]
        );

        $fieldset->addField(
            'category_text',
            'text',
            [
                'name' => 'category_text',
                'label' => __('Label Text'),
                'title' => __('Label Text'),
                'required' => false,
                'note' => "#attr:attribute_code# or #discount_percent# or #discount_amount# or #special_price# or #final_price# or #price# or #stock_item#",
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'category_custom_style',
            'text',
            [
                'name' => 'category_custom_style',
                'label' => __('Custom Css Label'),
                'title' => __('Custom Css Label'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => __("Css Style Only. Example: color: #fff; text-shadow: 0 1px 0 rgba(0,0,0,0.3); width: 40px; height: 40px;background:#ff7800; border-radius:50%;")
            ]
        );

        $fieldset->addField(
            'category_custom_url',
            'text',
            [
                'name' => 'category_custom_url',
                'label' => __('Label Custom Url'),
                'title' => __('Label Custom Url'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'category_round_method',
            'select',
            [
                'label' => __('Round Method'),
                'title' => __('Round Method'),
                'name' => 'category_round_method',
                'required' => false,
                'options' => [
                    'round' => __('Math'),
                    'ceil' => __('Ceil'),
                    'floor' => __('Floor')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'category_round_value',
            'text',
            [
                'name' => 'category_round_value',
                'label' => __('Round Value'),
                'title' => __('Round Value'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => "Example: 0.001 or 0.01 or 0.1 or 1 or 10 or 100"
            ]
        );

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
        return __('Category Label');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Label');
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
