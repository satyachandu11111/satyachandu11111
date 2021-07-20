<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


declare(strict_types=1);

namespace Amasty\Xnotif\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class GdprXnotif implements OptionSourceInterface
{
    const GDPR_XNOTIF_PRICE_FORM = 'xnotif_price_form';
    const GDPR_XNOTIF_STOCK_FORM = 'xnotif_stock_form';

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => [
                    ['value' => self::GDPR_XNOTIF_PRICE_FORM, 'label' => __('Price Subscription Form')],
                    ['value' => self::GDPR_XNOTIF_STOCK_FORM, 'label' => __('Stock Subscription Form')],
                ],
                'label' => __('Out of Stock Notification')
            ]
        ];
    }
}
