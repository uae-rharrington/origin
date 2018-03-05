<?php
namespace Swissup\EasySlide\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Framework\UrlInterface;

class Config extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    * @return \Swissup\EasySlide\Model\Slider
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

        $generalFieldset = $form->addFieldset(
            'general_fieldset',
            ['legend' => __('Parameters'), 'class' => 'fieldset-wide']
        );

        $generalFieldset->addField(
            'theme',
            'select',
            [
                'label' => __('Theme'),
                'title' => __('Theme'),
                'name' => 'theme',
                'options' => [
                    ''      => __('Default'),
                    'black' => __('Black'),
                    'white' => __('White')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'direction',
            'select',
            [
                'label' => __('Direction'),
                'title' => __('Direction'),
                'name' => 'direction',
                'required' => true,
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'effect',
            'select',
            [
                'label' => __('Effect'),
                'title' => __('Effect'),
                'name' => 'effect',
                'required' => true,
                'options' => [
                    'slide' => __('Slide'),
                    'fade' => __('Fade'),
                    'cube' => __('Cube'),
                    // 'coverflow' => __('Coverflow'),
                    'flip' => __('Flip')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'speed',
            'text',
            [
                'name' => 'speed',
                'label' => __('Speed'),
                'title' => __('Speed'),
                'required' => true,
                'note' => 'Duration of transition between slides (in ms)',
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'autoplay',
            'text',
            [
                'name' => 'autoplay',
                'label' => __('Autoplay'),
                'title' => __('Autoplay'),
                'required' => true,
                'note' => 'Delay between transitions (in ms). If this parameter is not specified, auto play will be disabled',
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'pagination',
            'select',
            [
                'label' => __('Pagination'),
                'title' => __('Pagination'),
                'name' => 'pagination',
                'required' => true,
                'options' => [
                    '1' => __('Enable'),
                    '0' => __('Disable')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'navigation',
            'select',
            [
                'label' => __('Navigation Buttons'),
                'title' => __('Navigation Buttons'),
                'name' => 'navigation',
                'required' => true,
                'options' => [
                    '1' => __('Enable'),
                    '0' => __('Disable')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'scrollbar',
            'select',
            [
                'label' => __('Scrollbar'),
                'title' => __('Scrollbar'),
                'name' => 'scrollbar',
                'required' => true,
                'options' => [
                    '1' => __('Enable'),
                    '0' => __('Disable')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $generalFieldset->addField(
            'scrollbarHide',
            'select',
            [
                'label' => __('Scrollbar Hide'),
                'title' => __('Scrollbar Hide'),
                'name' => 'scrollbarHide',
                'required' => true,
                'options' => [
                    '1' => __('Enable'),
                    '0' => __('Disable')
                ],
                'note' => 'Hide scrollbar automatically after user interaction',
                'disabled' => $isElementDisabled
            ]
        );

        $data = $model->getData();
        if (!$model->getId()) {
            $data = [
                'speed' => '1000',
                'autoplay' => '3000',
            ];
        }

        $form->setValues($data);
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
        return __('Parameters');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Parameters');
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
