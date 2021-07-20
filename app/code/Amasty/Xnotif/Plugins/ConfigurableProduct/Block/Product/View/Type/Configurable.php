<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Plugins\ConfigurableProduct\Block\Product\View\Type;

use Amasty\Xnotif\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Configurable
{
    const STOCK_STATUS = 'quantity_and_stock_status';
    const IS_IN_STOCK = 'is_in_stock';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $allProducts = [];

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        Config $config
    ) {
        $this->moduleManager = $moduleManager;
        $this->jsonEncoder = $jsonEncoder;
        $this->registry = $registry;
        $this->config = $config;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function beforeGetAllowProducts($subject)
    {
        if (!$subject->hasAllowProducts()) {
            $subject->setAllowProducts($this->getAllProducts($subject));
        }

        return $subject->getData('allow_products');
    }

    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterFetchView($subject, $html)
    {
        $configurableLayout = ['product.info.options.configurable', 'product.info.options.swatches'];
        if (in_array($subject->getNameInLayout(), $configurableLayout)
            && !$this->moduleManager->isEnabled('Amasty_Stockstatus')
            && !$this->registry->registry('amasty_xnotif_initialization')
        ) {
            $this->registry->register('amasty_xnotif_initialization', 1);

            /*move creating code to Amasty\Xnotif\Plugins\ConfigurableProduct\Data */
            $aStockStatus = $this->registry->registry('amasty_xnotif_data');
            $aStockStatus['changeConfigurableStatus'] = true;
            $data = $this->jsonEncoder->encode($aStockStatus);

            $html
                = '<script type="text/x-magento-init">
                    {
                        ".product-options-wrapper": {
                                    "amnotification": {
                                        "xnotif": ' . $data . '
                                    }
                         }
                    }
                   </script>' . $html;
        }

        return $html;
    }

    /**
     * @param $subject
     * @return mixed
     */
    private function getAllProducts($subject)
    {
        $mainProduct = $subject->getProduct();
        $productId = $mainProduct->getId();

        if (!isset($this->allProducts[$productId])) {
            $products = [];
            $allProducts = $mainProduct->getTypeInstance(true)
                ->getUsedProducts($mainProduct);
            if (isset($mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK])) {
                $mainProductStatus = (bool) $mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK];
            } else {
                $mainProductStatus = true;
            }

            foreach ($allProducts as $product) {
                if ($this->isProductAllowed($product, $mainProductStatus)) {
                    $products[] = $product;
                }
            }
            $this->allProducts[$productId] = $products;
        }

        return $this->allProducts[$productId];
    }

    private function isProductAllowed(Product $product, bool $mainProductStatus): bool
    {
        if ($product->getStatus() != Status::STATUS_ENABLED) {
            return false;
        }

        return $mainProductStatus || !$this->config->isShowOutOfStockOnly()
            ? true
            : !$product->getIsSalable();
    }
}
