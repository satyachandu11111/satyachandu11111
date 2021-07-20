<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Plugins\Catalog\Block\Product;

use Amasty\Xnotif\Block\Catalog\Category\StockSubscribe;
use Magento\Catalog\Block\Product\AbstractProduct as ProductBlock;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableModel;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;

class AbstractProduct
{
    const CATEGORY_BLOCK_NAME = 'category.subscribe.block';

    /**
     * @var string
     */
    private $loggedTemplate;

    /**
     * @var string
     */
    private $notLoggedTemplate;

    /**
     * @var \Amasty\Xnotif\Helper\Data
     */
    private $xnotifHelper;

    /**
     * @var ProductModel|null
     */
    private $product;

    /**
     * @var array
     */
    private $processedProducts = [];

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\Xnotif\Helper\Config
     */
    private $config;

    public function __construct(
        \Amasty\Xnotif\Helper\Data $xnotifHelper,
        \Amasty\Xnotif\Helper\Config $config,
        \Magento\Framework\Registry $registry
    ) {
        $this->loggedTemplate = 'Magento_ProductAlert::product/view.phtml';
        $this->notLoggedTemplate = 'Amasty_Xnotif::category/subscribe.phtml';
        $this->xnotifHelper = $xnotifHelper;
        $this->registry = $registry;
        $this->config = $config;
    }

    /**
     * @param ProductBlock $subject
     * @param ProductModel $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     *
     * @return array
     */
    public function beforeGetReviewsSummaryHtml(
        ProductBlock $subject,
        ProductModel $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        $this->setProduct($product);

        return [$product, $templateType, $displayIfNoReviews];
    }

    /**
     * @param ProductBlock $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetReviewsSummaryHtml(ProductBlock $subject, $result)
    {
        if ($this->enableSubscribe($subject)) {
            $result .= $this->getSubscribeHtml($subject);
        }

        return $result;
    }

    /**
     * @param ProductBlock $subject
     * @return string
     */
    public function getSubscribeHtml($subject)
    {
        $html = '';
        $layout = $subject->getLayout();
        $product = $this->getProduct();
        if (!$product->isSaleable() && !$product->getData('amxnotif_hide_alert')) {
            $html = $this->generateAlertHtml($layout, $product);
        }

        if ($product->getTypeId() == ConfigurableModel::TYPE_CODE) {
            $html .= $this->generateConfigurableHtml();
        }

        $this->processedProducts[$subject->getNameInLayout()][] = $product->getId();
        $this->registry->unregister('xnotifConfigurables');
        $this->registry->register('xnotifConfigurables', $this->processedProducts[$subject->getNameInLayout()]);

        return $html;
    }

    /**
     * @return string
     */
    private function generateConfigurableHtml()
    {
        $productId = $this->getProduct()->getId();

        return sprintf('<div class="amxnotif-category-container" data-amsubscribe="%s"></div>', $productId);
    }

    /**
     * @param LayoutInterface $layout
     * @param ProductModel $product
     * @param string $refererUrl
     * @return string
     */
    public function generateAlertHtml($layout, $product, $refererUrl = null)
    {
        $subscribeBlock = $this->getSubscribeBlock($layout);

        $currentProduct = $this->registry->registry('current_product');

        /*check if it is child product for replace product registered to child product.*/
        $isChildProduct = $currentProduct && ($currentProduct->getId() != $product->getId());
        if ($isChildProduct) {
            $subscribeBlock->setData('parent_product_id', $currentProduct->getId());
            $subscribeBlock->setOriginalProduct($product);
        }

        $this->prepareSubscribeBlock($subscribeBlock, $product, $refererUrl);

        return $subscribeBlock->toHtml();
    }

    /**
     * @param LayoutInterface $layout
     *
     * @return bool|BlockInterface
     */
    protected function getSubscribeBlock(LayoutInterface $layout)
    {
        $subscribeBlock = $layout->getBlock(self::CATEGORY_BLOCK_NAME);
        if (!$subscribeBlock) {
            $subscribeBlock = $layout->createBlock(StockSubscribe::class, self::CATEGORY_BLOCK_NAME);
        }

        return $subscribeBlock;
    }

    /**
     * @param BlockInterface $subscribeBlock
     * @param $product
     * @param string $refererUrl
     */
    protected function prepareSubscribeBlock(BlockInterface $subscribeBlock, $product, $refererUrl)
    {
        if ($this->xnotifHelper->isLoggedIn()) {
            $subscribeBlock->setTemplate($this->loggedTemplate);
            $subscribeBlock->setHtmlClass('alert stock link-stock-alert');
            $subscribeBlock->setSignupLabel(
                __('Notify me when this product is in stock')
            );
            $subscribeBlock->setSignupUrl(
                $this->xnotifHelper->getSignupUrl(
                    'stock',
                    $product->getId(),
                    $subscribeBlock->getData('parent_product_id'),
                    $refererUrl
                )
            );
        } else {
            $subscribeBlock->setTemplate($this->notLoggedTemplate);
            $subscribeBlock->setOriginalProduct($product);
        }
    }

    /**
     * Check if need render subscribe block for current product
     *
     * @param ProductBlock $subject
     * @return bool
     */
    protected function enableSubscribe($subject)
    {
        $result = $this->config->allowForCurrentCustomerGroup('stock')
            && $this->config->isCategorySubscribeEnabled()
            && (
                !isset($this->processedProducts[$subject->getNameInLayout()])
                || !in_array($this->getProduct()->getId(), $this->processedProducts[$subject->getNameInLayout()])
            )
            && !$this->registry->registry('current_product');

        return $result;
    }

    /**
     * @return ProductModel|null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductModel $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
}
