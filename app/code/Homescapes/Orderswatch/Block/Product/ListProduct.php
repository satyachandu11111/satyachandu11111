<?php

namespace Homescapes\Orderswatch\Block\Product;

class ListProduct
{
    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);

        $html = $subject->getLayout()->createBlock('Homescapes\Orderswatch\Block\Swatchlist')->setProduct($product)->setTemplate('Homescapes_Orderswatch::sample/swatchlist.phtml')->toHtml();
    
        return $html.$result;
           
    }
}
