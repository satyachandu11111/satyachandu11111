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
namespace Aheadworks\Raf\Model\Advocate\Generator;

use Magento\Framework\Math\Random;

/**
 * Class ReferralLink
 *
 * @package Aheadworks\Raf\Model\Advocate\Generator
 */
class ReferralLink
{
    /**
     * Generate referral link
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate()
    {
        return strtoupper(uniqid(dechex(Random::getRandomNumber())));
    }
}
