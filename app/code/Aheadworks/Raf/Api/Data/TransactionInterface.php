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
namespace Aheadworks\Raf\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TransactionInterface
 * @api
 */
interface TransactionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const SUMMARY_ID = 'summary_id';
    const CREATED_AT = 'created_at';
    const ACTION = 'action';
    const ADMIN_COMMENT = 'admin_comment';
    const ADMIN_COMMENT_PLACEHOLDER = 'admin_comment_placeholder';
    const AMOUNT = 'amount';
    const AMOUNT_TYPE = 'amount_type';
    const BALANCE_AMOUNT = 'balance_amount';
    const PERCENT_BALANCE_AMOUNT = 'percent_balance_amount';
    const STATUS = 'status';
    const HOLDING_PERIOD_EXPIRATION = 'holding_period_expiration';
    const CREATED_BY = 'created_by';
    const ENTITIES = 'entities';
    /**#@-*/

    /**
     * Get transaction id
     *
     * @return int
     */
    public function getId();

    /**
     * Set transaction id
     *
     * @param  int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get summary id
     *
     * @return int
     */
    public function getSummaryId();

    /**
     * Set transaction id
     *
     * @param  int $summaryId
     * @return $this
     */
    public function setSummaryId($summaryId);

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get action
     *
     * @return string
     */
    public function getAction();

    /**
     * Set action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * Get admin comment
     *
     * @return string|null
     */
    public function getAdminComment();

    /**
     * Set admin comment
     *
     * @param string $adminComment
     * @return $this
     */
    public function setAdminComment($adminComment);

    /**
     * Get admin comment placeholder
     *
     * @return string|null
     */
    public function getAdminCommentPlaceholder();

    /**
     * Set admin comment placeholder
     *
     * @param string $adminCommentPlaceholder
     * @return $this
     */
    public function setAdminCommentPlaceholder($adminCommentPlaceholder);

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get amount type
     *
     * @return string
     */
    public function getAmountType();

    /**
     * Set amount type
     *
     * @param string $amountType
     * @return $this
     */
    public function setAmountType($amountType);

    /**
     * Get balance amount
     *
     * @return float
     */
    public function getBalanceAmount();

    /**
     * Set balance amount
     *
     * @param float $balanceAmount
     * @return $this
     */
    public function setBalanceAmount($balanceAmount);

    /**
     * Get percent balance amount
     *
     * @return float
     */
    public function getPercentBalanceAmount();

    /**
     * Set percent balance amount
     *
     * @param float $percentBalanceAmount
     * @return $this
     */
    public function setPercentBalanceAmount($percentBalanceAmount);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get holding period expiration
     *
     * @return string|null
     */
    public function getHoldingPeriodExpiration();

    /**
     * Set holding period expiration
     *
     * @param string $date
     * @return $this
     */
    public function setHoldingPeriodExpiration($date);

    /**
     * Get created by
     *
     * @return int|null
     */
    public function getCreatedBy();

    /**
     * Set created by
     *
     * @param  int $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Get transaction entities
     *
     * @return \Aheadworks\Raf\Api\Data\TransactionEntityInterface[]|null
     */
    public function getEntities();

    /**
     * Set transaction entities
     *
     * @param \Aheadworks\Raf\Api\Data\TransactionEntityInterface[] $entities
     * @return $this
     */
    public function setEntities($entities);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Raf\Api\Data\TransactionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Raf\Api\Data\TransactionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\TransactionExtensionInterface $extensionAttributes
    );
}
