<?php

namespace Mirasvit\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ImportEntities implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Templates'),
                'value' => 'template',
            ],
            [
                'label' => __('Filters'),
                'value' => 'rule',
            ],
            [
                'label' => __('Dynamic Attributes'),
                'value' => 'dynamic_attribute',
            ],
            [
                'label' => __('Dynamic Categories'),
                'value' => 'dynamic_category',
            ],
            [           
                'label' => __('Dynamic Variables'),
                'value' => 'dynamic_variable',
            ],
        ];
    }
}
