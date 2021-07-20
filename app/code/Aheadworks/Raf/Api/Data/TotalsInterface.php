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
namespace Aheadworks\Raf\Api\Data;

use Magento\Quote\Api\Data\TotalsInterface as QuoteTotalsInterface;

/**
 * Interface TotalsInterface
 * @api
 */
interface TotalsInterface extends QuoteTotalsInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_RAF_IS_FRIEND_DISCOUNT = 'aw_raf_is_friend_discount';
    const AW_RAF_REFERRAL_LINK = 'aw_raf_referral_link';
    const AW_RAF_PERCENT_AMOUNT = 'aw_raf_percent_amount';
    const AW_RAF_AMOUNT_TYPE = 'aw_raf_amount_type';
    const AW_RAF_AMOUNT = 'aw_raf_amount';
    const BASE_AW_RAF_AMOUNT = 'base_aw_raf_amount';
    const AW_RAF_SHIPPING_PERCENT = 'aw_raf_shipping_percent';
    const AW_RAF_SHIPPING_AMOUNT = 'aw_raf_shipping_amount';
    const BASE_AW_RAF_SHIPPING_AMOUNT = 'base_aw_raf_shipping_amount';
    /**#@-*/
}
