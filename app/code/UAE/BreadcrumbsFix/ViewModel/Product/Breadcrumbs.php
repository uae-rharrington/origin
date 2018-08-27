<?php
/**
 * ViewModel Breadcrumbs
 *
 * @category UAE
 * @package UAE_BreadcrumbsFix
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\BreadcrumbsFix\ViewModel\Product;

use Magento\Catalog\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Product breadcrumbs view model.
 */
class Breadcrumbs extends DataObject implements ArgumentInterface
{
    /**
     * Catalog data.
     *
     * @var Data
     */
    private $catalogData;

    /**
     * @param Data $catalogData
     */
    public function __construct(Data $catalogData)
    {
        parent::__construct();

        $this->catalogData = $catalogData;
    }

    /**
     * Returns breadcrumb path.
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        return $this->catalogData->getBreadcrumbPath() !== null
            ? json_encode($this->catalogData->getBreadcrumbPath(), JSON_HEX_TAG)
            : '';
    }
}
