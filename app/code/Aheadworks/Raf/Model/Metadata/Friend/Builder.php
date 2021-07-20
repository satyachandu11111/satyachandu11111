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
namespace Aheadworks\Raf\Model\Metadata\Friend;

use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Builder
 *
 * @package Aheadworks\Raf\Model\Metadata\Friend
 */
class Builder
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Build friend metadata
     *
     * @param Quote $quote
     * @return FriendMetadataInterface
     */
    public function build($quote)
    {
        $customerId = $quote->getCustomerId() ? : '';
        $customerEmail = $quote->getCustomerEmail() ? : '';
        $remoteIp = $quote->getRemoteIp() ? : '';

        $friendData = [
            FriendMetadataInterface::CUSTOMER_ID => $customerId,
            FriendMetadataInterface::CUSTOMER_EMAIL => $customerEmail,
            FriendMetadataInterface::CUSTOMER_IP => $remoteIp
        ];

        return $this->objectManager->create(FriendMetadataInterface::class, ['data' => $friendData]);
    }
}
