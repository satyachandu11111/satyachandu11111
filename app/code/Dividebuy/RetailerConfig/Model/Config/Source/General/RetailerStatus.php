<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dividebuy\RetailerConfig\Model\Config\Source\General;

/**
 * @api
 * @since 100.0.2
 */
class RetailerStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Activate')], ['value' => 0, 'label' => __('Deactivate')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Deactivate'), 0 => __('Activate')];
    }
}
