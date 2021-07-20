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
namespace Aheadworks\Raf\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WhoCanInviteType
 * @package Aheadworks\Raf\Model\Source\Config\General
 */
class WhoCanInviteType implements OptionSourceInterface
{
    /**#@+
     * Constants defined for "who can invite" types
     */
    const ALL_CUSTOMERS = 'all_customers';
    const CUSTOMERS_WITH_PURCHASES = 'customer_with_purchases';
    /**#@-*/

    /**
     * Retrieve "who can invite" types as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ALL_CUSTOMERS,
                'label' => __('All Registered Customers')
            ],
            [
                'value' => self::CUSTOMERS_WITH_PURCHASES,
                'label' => __('Only Registered Customers with Previous Purchases')
            ],
        ];
    }
}
