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

use Magento\Sales\Api\Data\CreditmemoInterface as SalesCreditmemoInterface;

/**
 * Interface CreditmemoInterface
 * @api
 */
interface CreditmemoInterface extends SalesCreditmemoInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_RAF_AMOUNT = 'aw_raf_amount';
    const BASE_AW_RAF_AMOUNT = 'base_aw_raf_amount';
    const AW_RAF_IS_RETURN_TO_ACCOUNT = 'aw_raf_is_return_to_account';
    /**#@-*/
}
