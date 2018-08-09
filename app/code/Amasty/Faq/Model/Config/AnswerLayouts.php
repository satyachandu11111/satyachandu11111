<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class AnswerLayouts implements ArrayInterface
{
    const LAYOUT_ONE_COLUMN = 'amastyfaq_column';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LAYOUT_ONE_COLUMN, 'label' => __('1 column with left bar')]
        ];
    }
}
