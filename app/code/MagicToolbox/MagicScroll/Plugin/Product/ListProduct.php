<?php

namespace MagicToolbox\MagicScroll\Plugin\Product;

/**
 * Plugin for \Magento\Catalog\Block\Product\ListProduct
 */
class ListProduct
{
    /**
     * Helper
     *
     * @var \MagicToolbox\MagicScroll\Helper\Data
     */
    protected $magicToolboxHelper = null;

    /**
     * MagicScroll module core class
     *
     * @var \MagicToolbox\MagicScroll\Classes\MagicScrollModuleCoreClass
     *
     */
    protected $toolObj = null;

    /**
     * Disable flag
     * @var bool
     */
    protected $isDisabled = true;

    /**
     * @param \MagicToolbox\MagicScroll\Helper\Data $magicToolboxHelper
     */
    public function __construct(
        \MagicToolbox\MagicScroll\Helper\Data $magicToolboxHelper
    ) {
        $this->magicToolboxHelper = $magicToolboxHelper;
        $this->toolObj = $this->magicToolboxHelper->getToolObj();
        $this->toolObj->params->setProfile('category');
        $this->isDisabled = !$this->toolObj->params->checkValue('enable-effect', 'Yes', 'category');
    }

    /**
     * Produce and return block's html output
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $listProductBlock
     * @param string $html
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\ListProduct $listProductBlock, $html)
    {
        if ($this->isDisabled) {
            return $html;
        }

        //NOTE: do not apply for product listing with not standard renderer (#148451)
        $isAnotherRendererAvailable = $this->magicToolboxHelper->getAnotherRenderer();
        if (!$isAnotherRendererAvailable) {
            return $html;
        }

        $this->magicToolboxHelper->setListProductBlock($listProductBlock);
        $productCollection = $listProductBlock->getLoadedProductCollection();
        $patternBegin = '<a(?=\s)[^>]+?(?<=\s)href="';
        $patternEnd = '"[^>]++>.*?</a>';

        foreach ($productCollection as $product) {

            $_html = $this->magicToolboxHelper->getHtmlData($product);

            if (empty($_html)) {
                continue;
            }

            $url = preg_quote($product->getProductUrl(), '#');
            $html = preg_replace("#{$patternBegin}{$url}{$patternEnd}#s", $_html, $html, 1);

        }

        return $html;
    }
}
