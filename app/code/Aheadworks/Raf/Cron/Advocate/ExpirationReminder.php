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
namespace Aheadworks\Raf\Cron\Advocate;

use Aheadworks\Raf\Api\AdvocateExpirationManagementInterface;
use Aheadworks\Raf\Cron\Management;
use Aheadworks\Raf\Model\Flag;
use Psr\Log\LoggerInterface;

/**
 * Class ExpirationReminder
 *
 * @package Aheadworks\Raf\Cron\Advocate
 */
class ExpirationReminder
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var AdvocateExpirationManagementInterface
     */
    private $advocateExpirationManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param AdvocateExpirationManagementInterface $advocateExpirationManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        AdvocateExpirationManagementInterface $advocateExpirationManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->advocateExpirationManagement = $advocateExpirationManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_RAF_ADVOCATE_EXPIRATION_REMINDER_LAST_EXEC_TIME)) {
            try {
                $this->advocateExpirationManagement->sendExpirationReminder();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_RAF_ADVOCATE_EXPIRATION_REMINDER_LAST_EXEC_TIME);
        }
    }
}
