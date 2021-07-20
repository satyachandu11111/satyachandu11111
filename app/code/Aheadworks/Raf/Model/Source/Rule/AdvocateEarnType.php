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
 * Class AdvocateEarnType
 * @package Aheadworks\Raf\Model\Source\Rule
 */
class AdvocateEarnType implements OptionSourceInterface
{
    /**
     * Advocate earn type
     */
    const CUMULATIVE = 'cumulative';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CUMULATIVE, 'label' => __('Cumulative')],
        ];
    }
}
