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
namespace Aheadworks\Raf\Model\Transaction\Processor\Resolver;

use Aheadworks\Raf\Model\Config;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class HoldingPeriodExpiration
 *
 * @package Aheadworks\Raf\Model\Transaction\Processor\Resolver
 */
class HoldingPeriodExpiration
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StdlibDateTime
     */
    private $dateTime;

    /**
     * @param Config $config
     * @param StdlibDateTime $dateTime
     */
    public function __construct(
        Config $config,
        StdlibDateTime $dateTime
    ) {
        $this->config = $config;
        $this->dateTime = $dateTime;
    }

    /**
     * Resolve expiration date
     *
     * @param int $websiteId
     * @return string|null
     * @throws \Exception
     */
    public function resolveExpirationDate($websiteId)
    {
        $holdingPeriodInDays = $this->config->getNumberOfDaysForHoldingPeriod($websiteId);
        $expirationDate = null;
        if ($holdingPeriodInDays > 0) {
            $expirationDate = new \DateTime('now');
            $expirationDate->add(new \DateInterval('P' . $holdingPeriodInDays . 'D'));
            $expirationDate = $this->dateTime->formatDate($expirationDate);
        }

        return $expirationDate;
    }
}
