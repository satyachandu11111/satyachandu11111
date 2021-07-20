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
namespace Aheadworks\Raf\Model\Metadata;

use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Magento\Framework\DataObject;

/**
 * Class Friend
 *
 * @package Aheadworks\Raf\Model\Metadata
 */
class Friend extends DataObject implements FriendMetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerIp()
    {
        return $this->getData(self::CUSTOMER_IP);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerIp($ip)
    {
        return $this->setData(self::CUSTOMER_IP, $ip);
    }
}
