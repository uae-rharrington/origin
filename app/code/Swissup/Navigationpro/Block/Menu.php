<?php

namespace Swissup\Navigationpro\Block;

use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Menu extends Template implements IdentityInterface
{
    /**
     * Cache identities
     *
     * @var array
     */
    protected $identities = [];

    /**
     * Menu data tree
     *
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $menuTree;

    /**
     * @var \Swissup\Navigationpro\Model\Menu
     */
    protected $menu;

    /**
     * @var \Swissup\Navigationpro\Model\MenuFactory
     */
    protected $menuFactory;

    /**
     * @var \Swissup\Navigationpro\Model\Template\Filter
     */
    protected $filter;

    /**
     * Data tree node.
     *
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Data tree.
     *
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Array of hasDropdownContent check results
     * @var array
     */
    protected $hasDropdownContentCache = [];

    /**
     * @var string
     */
    private $output;

    /**
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param \Swissup\Navigationpro\Model\MenuFactory $menuFactory,
     * @param \Swissup\Navigationpro\Model\Template\Filter $filter,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param CustomerSession $customerSession,
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Swissup\Navigationpro\Model\MenuFactory $menuFactory,
        \Swissup\Navigationpro\Model\Template\Filter $filter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->menuFactory = $menuFactory;
        $this->filter = $filter;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [];

        $settings = $this->getMenu()->getDropdownSettings();
        $validPositions = ['left', 'right', 'center'];
        $level0HorizontalAlignment = 'center';
        if (isset($settings['level1']['position']) &&
            in_array($settings['level1']['position'], $validPositions)) {

            $level0HorizontalAlignment = $settings['level1']['position'];
        }

        // Set level0 position
        if ($this->getOrientation() === 'vertical') {
            if ($this->getDropdownSide() === 'left') {
                $config['level0']['position'] = [
                    'my' => 'right top',
                    'at' => 'left top',
                ];
            } else {
                $config['level0']['position'] = [
                    'my' => 'left top',
                    'at' => 'right top',
                ];
            }
        } else {
            if ($this->getDropdownSide() === 'top') {
                $config['level0']['position'] = [
                    'my' => $level0HorizontalAlignment . ' bottom',
                    'at' => $level0HorizontalAlignment . ' top',
                ];
            } else {
                $config['level0']['position'] = [
                    'my' => $level0HorizontalAlignment . ' top',
                    'at' => $level0HorizontalAlignment . ' bottom',
                ];
            }
        }

        // set other levels position
        if ($this->getDropdownSide() === 'left') {
            $config['position'] = [
                'my' => 'right top',
                'at' => 'left top',
            ];
        } elseif ($this->getDropdownSide() === 'top') {
            $config['position'] = [
                'my' => 'left bottom',
                'at' => 'right bottom',
            ];
        }

        return $this->jsonHelper->jsonEncode($config);
    }

    /**
     * Get CSS classes for the NAV element
     *
     * @return string
     */
    public function getNavCssClass()
    {
        $classes = [
            $this->getData('nav_css_class'),
            'orientation-' . ($this->getOrientation() ?: 'horizontal'),
        ];

        if ($this->getDropdownSide()) {
            $classes[] = 'dropdown-' . $this->getDropdownSide();
        }

        if ($this->getTheme()) {
            $classes[] = 'navpro-theme-' . $this->getTheme();
        }

        if ($this->getShowActiveBranch()) {
            $classes[] = 'navpro-active-branch';
        }

        return implode(' ', $classes);
    }

    /**
     * Get css classes for the UL element
     *
     * @return string
     */
    public function getCssClass()
    {
        $classes = [
            $this->getData('css_class')
        ];

        if ($this->getMenu()) {
            $classes[] = $this->getMenu()->getCssClass();
        }

        return implode(' ', $classes);
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = parent::getTemplate();

        if ($template) {
            return $template;
        }

        return 'Swissup_Navigationpro::menu.phtml';
    }

    /**
     * Get menu html
     *
     * @return string
     */
    public function getHtml($outermostClass = '')
    {
        if (!$this->getMenu()) {
            return '';
        }

        if ($this->output === null) {
            $this->_eventManager->dispatch(
                'swissup_navigationpro_menu_gethtml_before',
                [
                    'menu' => $this->getMenuTree(),
                    'block' => $this,
                    'request' => $this->getRequest()
                ]
            );

            $this->getMenuTree()->setOutermostClass($outermostClass);
            // $this->getMenuTree()->setChildrenWrapClass($childrenWrapClass);

            $html = $this->_getHtml($this->getMenuTree());

            $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
            $this->_eventManager->dispatch(
                'swissup_navigationpro_menu_gethtml_after',
                [
                    'menu' => $this->getMenuTree(),
                    'transportObject' => $transportObject
                ]
            );
            $this->output = $transportObject->getHtml();
        }

        return $this->output;
    }

    /**
     * Recursively generates menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @return string
     */
    protected function _getHtml(\Magento\Framework\Data\Tree\Node $menuTree)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        $lastLevelsPerDropdown = $menuTree->getNextLevelsPerDropdown() ?: 1;

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $currentLevelsPerDropdown = max(
                $this->getItemLevelsPerDropdown($child),
                $lastLevelsPerDropdown
            );
            $child->setLevelsPerDropdown($currentLevelsPerDropdown);
            $child->setNextLevelsPerDropdown($currentLevelsPerDropdown - 1);

            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $child->setClass($outermostClass);
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= $this->getRenderedItemName($child)
                . $this->getRenderedDropdownContent($child)
                . '</li>';
            $itemPosition++;
            $counter++;
        }

        return $html;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string HTML code
     */
    protected function getRenderedDropdownContent(\Magento\Framework\Data\Tree\Node $item)
    {
        $html = '';
        if (!$this->hasDropdownContent($item)) {
            return $html;
        }

        $settings = $this->getItemDropdownSettings($item);

        $dropdownClass = 'navpro-dropdown';
        if ($item->getLevelsPerDropdown() > 1) {
            $dropdownClass = 'navpro-dropdown-expanded';
        }
        if (!empty($settings['dropdown_css_class'])) {
            $dropdownClass .= ' ' . $settings['dropdown_css_class'];
        }
        $html .= '<div class="' . $dropdownClass . ' navpro-dropdown-level' . ($item->getLevel() + 1) . ' size-' . $settings['width'] . '" data-level="' . $item->getLevel() . '">';
        $html .= '<div class="navpro-dropdown-inner">';

        $layout = $this->jsonHelper->jsonDecode($settings['layout']);
        foreach ($layout as $regionCode => $region) {
            if (!$region['size']) {
                continue;
            }

            foreach ($region['rows'] as $row) {
                $rowHtml = '';
                foreach ($row as $column) {
                    if (!$column['is_active'] || !$column['size']) {
                        continue;
                    }

                    $columnHtml = '';
                    switch ($column['type']) {
                        case 'html':
                            if (empty($column['content'])) {
                                continue;
                            }
                            $columnHtml .= $this->filter
                                ->setItem($item)
                                ->filter($column['content']);
                            break;
                        case 'children':
                            if (!$item->hasChildren()) {
                                continue;
                            }
                            $classes = ['children'];
                            $columnsCount = 1;
                            if (!empty($column['columns_count']) && $column['columns_count'] > 1) {
                                $columnsCount = $column['columns_count'];
                                $classes[] = 'multicolumn';
                                $classes[] = 'multicolumn-' . $columnsCount;
                            }
                            if (!empty($column['direction']) && $column['direction'] === 'vertical') {
                                $classes[] = 'vertical';
                            }

                            $columnHtml .= '<ul class="' . implode($classes, ' ') . '" data-columns="' . $columnsCount . '">';
                            $columnHtml .= $this->_getHtml($item);
                            $columnHtml .= '</ul>';
                            break;
                    }

                    if ($columnHtml) {
                        $rowHtml .= '<div class="navpro-col navpro-col-' . $column['size'] . '">';
                        $rowHtml .= $columnHtml;
                        $rowHtml .= '</div>';
                    }
                }
                if ($rowHtml) {
                    $html .= '<div class="navpro-row gutters">';
                    $html .= $rowHtml;
                    $html .= '</div>';
                }
            }
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Generates a string to use as item name
     *
     * @param  \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function getRenderedItemName(\Magento\Framework\Data\Tree\Node $item)
    {
        if ($item->getHtml()) {
            return $this->filter->setItem($item)->filter($item->getHtml());
        } else {
            return '<a href="' . $item->getUrl() . '" class="' . $item->getClass() . '"><span>'
                . $this->escapeHtml($item->getName())
                . '</span></a>';
        }
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(\Magento\Framework\Data\Tree\Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);
        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }

        return $html;
    }

    /**
     * Returns array of menu item's attributes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(\Magento\Framework\Data\Tree\Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);

        return ['class' => implode(' ', $menuItemClasses)];
    }

    /**
     * Returns array of menu item's classes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(\Magento\Framework\Data\Tree\Node $item)
    {
        $classes = ['li-item'];

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        $settings = $this->getItemDropdownSettings($item);
        $classes[] = 'size-' . $settings['width'];

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        } elseif ($item->getHasActive()) {
            $classes[] = 'has-active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->getCssClass()) {
            $classes[] = $item->getCssClass();
        }

        if ($this->hasDropdownContent($item)) {
            if ($item->getLevelsPerDropdown() == 1) {
                $classes[] = 'parent';
            }
        }
        if ($item->getLevelsPerDropdown() > 1) {
            $classes[] = 'parent-expanded';
        }

        return $classes;
    }

    /**
     * Check if item has dropdown content to show
     * @param  \Magento\Framework\Data\Tree\Node $item
     * @return boolean
     */
    protected function hasDropdownContent(\Magento\Framework\Data\Tree\Node $item)
    {
        if (isset($this->hasDropdownContentCache[$item->getId()])) {
            return $this->hasDropdownContentCache[$item->getId()];
        }

        $settings = $this->getItemDropdownSettings($item);

        if (empty($settings['layout'])) {
            return false;
        }

        $result = false;
        $layout = $this->jsonHelper->jsonDecode($settings['layout']);
        foreach ($layout as $region) {
            if (!$region['size']) {
                continue;
            }
            foreach ($region['rows'] as $row) {
                foreach ($row as $content) {
                    if (!$content['is_active'] || !$content['size']) {
                        continue;
                    }

                    switch ($content['type']) {
                        case 'html':
                            if (!empty($content['content'])) {
                                $result = true;
                                break 3;
                            }
                            break;
                        case 'children':
                            if ($item->hasChildren()) {
                                $result = true;
                                break 3;
                            }
                            break;
                    }
                }
            }
        }

        $this->hasDropdownContentCache[$item->getId()] = $result;

        return $result;
    }

    /**
     * @param  \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function getItemDropdownSettings(\Magento\Framework\Data\Tree\Node $item)
    {
        $settings = $item->getDropdownSettings();
        if (!$settings || !empty($settings['use_menu_settings'])) {
            $settings = $this->getMenu()->getDropdownSettings();
            if ($item->getLevel() !== null) {
                $dropdownLevel = $item->getLevel() + 1;
            } else {
                $dropdownLevel = 0;
            }

            if (isset($settings['level' . $dropdownLevel])) {
                $settings = $settings['level' . $dropdownLevel];
            } else {
                $settings = $settings['default'];
            }
        }
        return $settings;
    }

    /**
     * @param  \Magento\Framework\Data\Tree\Node $item
     * @return integer
     */
    protected function getItemLevelsPerDropdown(\Magento\Framework\Data\Tree\Node $item)
    {
        if ($item->getParent()->getId()) {
            $settings = $this->getItemDropdownSettings($item->getParent());
        } else {
            return $this->getVisibleLevels() ?: 1;
        }

        if (empty($settings['layout'])) {
            return 1;
        }

        $layout = $this->jsonHelper->jsonDecode($settings['layout']);
        foreach ($layout as $region) {
            if (!$region['size']) {
                continue;
            }
            foreach ($region['rows'] as $row) {
                foreach ($row as $content) {
                    if (!$content['is_active'] || !$content['size']) {
                        continue;
                    }

                    if ($content['type'] == 'children') {
                        if (!isset($content['levels_per_dropdown'])) {
                            return 1;
                        }

                        return empty($content['levels_per_dropdown']) ?
                            100 : $content['levels_per_dropdown'];
                    }
                }
            }
        }

        return 1;
    }

    /**
     * Get menu object.
     *
     * Creates \Magento\Framework\Data\Tree\Node root node object.
     * The creation logic was moved from class constructor into separate method.
     *
     * @return Node
     */
    public function getMenuTree()
    {
        if (!$this->menuTree) {
            $this->menuTree = $this->nodeFactory->create(
                [
                    'data' => [],
                    'idField' => 'root',
                    'tree' => $this->treeFactory->create()
                ]
            );
            $this->_eventManager->dispatch(
                'swissup_navigationpro_menu_prepare',
                [
                    'menu' => $this->menuTree,
                    'block' => $this,
                    'request' => $this->getRequest()
                ]
            );
        }

        return $this->menuTree;
    }

    /**
     * @return \Swissup\Navigationpro\Model\Menu
     */
    public function getMenu()
    {
        if (!$this->menu && $this->getIdentifier()) {
            $this->menu = $this->menuFactory->create()->load(
                $this->getIdentifier(), 'identifier'
            );
        }
        return $this->menu;
    }

    /**
     * Add identity
     *
     * @param string|array $identity
     * @return void
     */
    public function addIdentity($identity)
    {
        if (!in_array($identity, $this->identities)) {
            $this->identities[] = $identity;
        }
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * Get block cache life time
     *
     * @return int
     */
    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 3600;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $keyInfo = parent::getCacheKeyInfo();
        $keyInfo[] = $this->getUrl('*/*/*', ['_current' => true, '_query' => '']);

        // customer group and currency
        $keyInfo[] = $this->customerSession->getCustomerGroupId();
        $keyInfo[] = $this->_storeManager->getStore()->getCurrentCurrencyCode();

        // widget data
        $keys = [
            'identifier',
            'show_active_branch',
            'visible_levels',
            'theme',
            'orientation',
            'dropdown_side',
            'css_class',
            'nav_css_class',
        ];
        foreach ($keys as $key) {
            if (!$this->hasData($key)) {
                continue;
            }
            $keyInfo[$key] = $key . ':' . $this->getData($key);
        }

        return $keyInfo;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    protected function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getIdentities());
    }
}
