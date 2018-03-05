<?php

namespace Swissup\RichSnippets\Block;

class Breadcrumbs extends LdJson
{
    /**
     * @var \Swissup\RichSnippets\Plugin\Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * Constructor
     *
     * @param \Swissup\RichSnippets\Plugin\Breadcrumbs $breadcrumbs
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Swissup\RichSnippets\Plugin\Breadcrumbs $breadcrumbs,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->breadcrumbs = $breadcrumbs;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getLdJson()
    {
        if (!$this->getStoreConfig('richsnippets/breadcrumbs/enabled')) {
            return '';
        }

        $itemsList = [];
        $position = 1;
        foreach ($this->breadcrumbs->getCrumbs() as $crumbInfo) {
            if (isset($crumbInfo['link']) && !empty($crumbInfo['link'])) {
                $itemsList[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'item' => [
                        '@id' => $crumbInfo['link'],
                        'name' => $this->escapeHtml($crumbInfo['label'])
                    ]
                ];
                $position++;
            }
        }

        if (empty($itemsList)) {
            return '';
        }

        $ldArray = [
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemsList
        ];

        return $this->prepareJsonString($ldArray);
    }
}
