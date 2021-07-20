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
namespace Aheadworks\Raf\Model\Source\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Raf\Model\Source\Transaction
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Transaction status values
     */
    const PENDING = 'pending';
    const COMPLETE = 'complete';
    const CANCELED = 'canceled';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => self::COMPLETE,
                'label' => __('Complete')
            ],
            [
                'value' => self::CANCELED,
                'label' => __('Canceled')
            ]
        ];
    }
}
