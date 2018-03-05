<?php
namespace Swissup\ProLabels\Block\Adminhtml\Label\Edit\Tab;

class Index extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'tab/index.phtml';
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
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
    // protected function _prepareForm()
    public function initForm()
    {
        /* @var $model \Swissup\ProLabels\Model\Label */
        $model = $this->_getModel();//$this->_coreRegistry->registry('askit_question');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Swissup_ProLabels::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        // \Zend_Debug::dump($this->getData('action'));
        // \Zend_Debug::dump($this->getUrl('*/answer/save'));
        /** @var \Magento\Framework\Data\Form $form */
        // $form = $this->_formFactory->create(
        //     ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/answer/save'), 'method' => 'post']]
        // );

        // $form->setHtmlIdPrefix('index_');

        // $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Indexed Products');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Indexed Products');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $model = $this->_getModel();
        return (bool) $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->canShowTab();
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

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Swissup\ProLabels\Block\Adminhtml\Label\Edit\Tab\Index\Grid',
                'index.grid'
            )
        );
        parent::_prepareLayout();
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
