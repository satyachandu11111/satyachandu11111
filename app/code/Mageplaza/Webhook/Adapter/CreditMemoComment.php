<?php

namespace Mageplaza\Webhook\Adapter;

class CreditMemoComment
{
	/** @var int */
    public $entityId;

    /** @var int */
    public $creditmemoId;

    /** @var string */
    public $comment;

    /** @var string */
    public $createdAt;

    /** @var int */
    public $isCustomerNotified;

    /** @var int */
    public $isVisibleOnFront;

    /**
     * Gets the credit memo comment ID.
     *
     * @return int|null Credit memo comment ID.
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     */
    public function setEntityId(?int $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * Gets the credit memo ID.
     *
     * @return int|null Credit memo ID.
     */
    public function getCreditmemoId(): int
    {
        return $this->creditmemoId;
    }

    /**
     * @param int|null $creditmemoId
     */
    public function setCreditmemoId(?int $creditmemoId): void
    {
        $this->creditmemoId = $creditmemoId;
    }

	/**
     * Gets comment.
     *
     * @return string|null Sub Total.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }


    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string|null $comment
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns is_customer_notified
     *
     * @return int
     */
    public function getIsCustomerNotified()
    {
        return $this->isCustomerNotified;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCustomerNotified(?int $isCustomerNotified): void
    {
        $this->isCustomerNotified = $isCustomerNotified;
    }

    /**
     * Returns is_visible_on_front
     *
     * @return int
     */
    public function getIsVisibleOnFront()
    {
        return $this->isVisibleOnFront;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleOnFront(?int $isVisibleOnFront): void
    {
        $this->isVisibleOnFront = $isVisibleOnFront;
    }
}