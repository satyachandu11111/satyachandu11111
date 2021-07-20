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
namespace Aheadworks\Raf\Api;

/**
 * Interface RuleManagementInterface
 * @api
 */
interface RuleManagementInterface
{
    /**
     * Retrieve active rule on website
     *
     * @param int $websiteId
     * @return bool|\Aheadworks\Raf\Api\Data\RuleInterface
     */
    public function getActiveRule($websiteId);

    /**
     * Retrieve rule on website
     *
     * @param int $websiteId
     * @return bool|\Aheadworks\Raf\Api\Data\RuleInterface
     */
    public function getRule($websiteId);
}
