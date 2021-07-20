<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


declare(strict_types=1);

namespace Amasty\GroupedOptions\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ]
        ];
    }
}
