<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BaseOffType
 *
 * @package Aheadworks\Raf\Model\Source\Rule
 */
class BaseOffType implements OptionSourceInterface
{
    /**
     * Fixed off type
     */
    const FIXED = 'fixed';

    /**
     * Percent off type
     */
    const PERCENT = 'percent';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FIXED, 'label' => __('Fixed')],
            ['value' => self::PERCENT, 'label' => __('Percent')]
        ];
    }
}
