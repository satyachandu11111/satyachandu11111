<?php

namespace Dividebuy\RetailerConfig\Model\Config\Source\Product;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'before', 'label' => __('Before proceed to checkout button')],
            ['value' => 'after', 'label' => __('After proceed to checkout button')],
        ];
    }
}
