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
namespace Aheadworks\Raf\Model;

use Aheadworks\Raf\Api\Data\TransactionEntityInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Transaction
 * @package Aheadworks\Raf\Model
 */
class TransactionEntity extends AbstractExtensibleObject implements TransactionEntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return  $this->_get(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        return  $this->_get(self::ENTITY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityLabel()
    {
        return $this->_get(self::ENTITY_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityLabel($entityLabel)
    {
        return $this->setData(self::ENTITY_LABEL, $entityLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\TransactionEntityExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
