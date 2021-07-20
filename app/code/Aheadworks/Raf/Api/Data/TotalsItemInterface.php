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

use Magento\Quote\Api\Data\TotalsItemInterface as QuoteTotalsItemInterface;

/**
 * Interface TotalsItemInterface
 * @api
 */
interface TotalsItemInterface extends QuoteTotalsItemInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_RAF_RULE_IDS = 'aw_raf_rule_ids';
    const AW_RAF_PERCENT = 'aw_raf_percent';
    const AW_RAF_AMOUNT = 'aw_raf_amount';
    const BASE_AW_RAF_AMOUNT = 'base_aw_raf_amount';
    /**#@-*/
}
