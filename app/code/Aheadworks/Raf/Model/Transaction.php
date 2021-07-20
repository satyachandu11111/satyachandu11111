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

use Aheadworks\Raf\Api\Data\TransactionInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Raf\Model\ResourceModel\Transaction as TransactionResourceModel;

/**
 * Class Transaction
 * @package Aheadworks\Raf\Model
 */
class Transaction extends AbstractModel implements TransactionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(TransactionResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryId()
    {
        return $this->getData(self::SUMMARY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSummaryId($summaryId)
    {
        return $this->setData(self::SUMMARY_ID, $summaryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminComment()
    {
        return $this->getData(self::ADMIN_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminComment($adminComment)
    {
        return $this->setData(self::ADMIN_COMMENT, $adminComment);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminCommentPlaceholder()
    {
        return $this->getData(self::ADMIN_COMMENT_PLACEHOLDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminCommentPlaceholder($adminCommentPlaceholder)
    {
        return $this->setData(self::ADMIN_COMMENT_PLACEHOLDER, $adminCommentPlaceholder);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountType()
    {
        return $this->getData(self::AMOUNT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmountType($amountType)
    {
        return $this->setData(self::AMOUNT_TYPE, $amountType);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceAmount()
    {
        return $this->getData(self::BALANCE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceAmount($balanceAmount)
    {
        return $this->setData(self::BALANCE_AMOUNT, $balanceAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentBalanceAmount()
    {
        return $this->getData(self::PERCENT_BALANCE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPercentBalanceAmount($percentBalanceAmount)
    {
        return $this->setData(self::PERCENT_BALANCE_AMOUNT, $percentBalanceAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getHoldingPeriodExpiration()
    {
        return $this->getData(self::HOLDING_PERIOD_EXPIRATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setHoldingPeriodExpiration($date)
    {
        return $this->setData(self::HOLDING_PERIOD_EXPIRATION, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        return $this->getData(self::ENTITIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntities($entities)
    {
        return $this->setData(self::ENTITIES, $entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\TransactionExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
