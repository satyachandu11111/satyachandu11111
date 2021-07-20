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
namespace Aheadworks\Raf\Plugin\Model\Service;

use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class CreditmemoServicePlugin
 *
 * @package Aheadworks\Raf\Plugin\Model\Service
 */
class CreditmemoServicePlugin
{
    /**
     * @var AdvocateManagementInterface
     */
    private $advocateManagement;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param AdvocateManagementInterface $advocateManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        AdvocateManagementInterface $advocateManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->advocateManagement = $advocateManagement;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Refund RAF discount by credit memo
     *
     * @param CreditmemoService $subject
     * @param CreditmemoInterface|Order\Creditmemo $result
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRefund(CreditmemoService $subject, CreditmemoInterface $result)
    {
        $order = $this->orderRepository->get($result->getOrderId());
        $customerId = $result->getCustomerId();
        $websiteId = $result->getStore()->getWebsiteId();
        try {
            $this->advocateManagement->refundReferralDiscountForCreditmemo($customerId, $websiteId, $result, $order);
        } catch (LocalizedException $e) {
            $this->logger->error($e);
        }

        return $result;
    }
}
