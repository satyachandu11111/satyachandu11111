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
namespace Aheadworks\Raf\Model\Source\Customer\Advocate\Email;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ReminderStatus
 *
 * @package Aheadworks\Raf\Model\Source\Customer\Advocate\Email
 */
class ReminderStatus implements OptionSourceInterface
{
    /**#@+
     * Transaction action values
     */
    const READY_TO_BE_SENT = 'ready_to_be_sent';
    const SENT = 'sent';
    const FAILED = 'failed';
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::READY_TO_BE_SENT,
                'label' => __('Ready to be Sent')
            ],
            [
                'value' => self::SENT,
                'label' => __('Sent')
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed')
            ]
        ];
    }
}
