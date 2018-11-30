<?php
namespace Mirasvit\Feed\Export\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Tax\Model\Calculation as TaxCalculation;

class PriceResolver extends AbstractResolver
{
    /**
     * @var TaxCalculation
     */
    private $taxCalculation;

    public function __construct(
        TaxCalculation $taxCalculation
    ) {
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * Price
     *
     * @param Product $product
     * @return float
     */
    public function getPrice($product)
    {
        return $product->getPrice();
    }

    /**
     * Final Price
     *
     * @param Product $product
     * @return float
     */
    public function getRegularPrice($product)
    {
        return $product->getPriceInfo()->getPrice('regular_price')->getValue();
    }

    /**
     * Final Price
     *
     * @param Product $product
     * @return float
     */
    public function getSpecialPrice($product)
    {
        return $product->getPriceInfo()->getPrice('special_price')->getValue();
    }

    /**
     * Final Price
     *
     * @param Product $product
     * @return float
     */
    public function getFinalPrice($product)
    {
        return $product->getPriceInfo()->getPrice('final_price')->getValue();
    }

    /**
     * Tax Rate
     *
     * @param Product $product
     * @return float
     */
    public function getTaxRate($product)
    {
        if ($this->getFeed()) {
            $storeId = $this->getFeed()->getStoreId();
        } else {
            $storeId = 0;
        }

        $request = $this->taxCalculation->getRateRequest(null, null, null, $storeId);
        $request->setData('product_class_id', $product->getTaxClassId());

        return $this->taxCalculation->getRate($request);
    }
}