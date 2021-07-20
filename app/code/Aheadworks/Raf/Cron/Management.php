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
namespace Aheadworks\Raf\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aheadworks\Raf\Model\Flag;
use Aheadworks\Raf\Model\FlagFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Management
 *
 * @package Aheadworks\Raf\Cron
 */
class Management
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 50;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     * @param FlagFactory $flagFactory
     */
    public function __construct(
        LoggerInterface $logger,
        DateTime $dateTime,
        FlagFactory $flagFactory
    ) {
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        $this->flag = $flagFactory->create();
    }

    /**
     * Is cron job locked
     *
     * @param string $flag
     * @param int $interval
     * @return bool
     */
    public function isLocked($flag, $interval = self::RUN_INTERVAL)
    {
        $now = $this->getCurrentTime();
        $lastExecTime = (int)$this->getFlagData($flag);
        return $now < $lastExecTime + $interval;
    }

    /**
     * Set flag data
     *
     * @param string $param
     * @return $this
     */
    public function setFlagData($param)
    {
        try {
            $this->flag
                ->unsetData()
                ->setEtFlagCode($param)
                ->loadSelf()
                ->setFlagData($this->getCurrentTime())
                ->save();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }

    /**
     * Get current time
     *
     * @return int
     */
    private function getCurrentTime()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * Get flag data
     *
     * @param string $param
     * @return mixed
     */
    private function getFlagData($param)
    {
        try {
            $this->flag
                ->unsetData()
                ->setEtFlagCode($param)
                ->loadSelf();
        } catch (LocalizedException $e) {
            return 0;
        }

        return $this->flag->getFlagData();
    }
}
