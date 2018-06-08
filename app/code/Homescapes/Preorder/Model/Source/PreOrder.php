<?php

namespace Homescapes\Preorder\Model\Source;

class PreOrder implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')]
        ];
    }
}