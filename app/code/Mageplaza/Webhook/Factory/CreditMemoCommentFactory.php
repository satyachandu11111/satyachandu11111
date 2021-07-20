<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\CreditmemoCommentInterface;
use Mageplaza\Webhook\Adapter\CreditMemoComment as SpbCreditMemoComment;

class CreditMemoCommentFactory
{
	public function createFromCreditMemoComment(CreditmemoCommentInterface $creditmemoComment): SpbCreditMemoComment
    {
        $spbCreditMemoComment = new SpbCreditMemoComment();
        $spbCreditMemoComment->setEntityId($creditmemoComment->getEntityId());
        $spbCreditMemoComment->setCreditmemoId($creditmemoComment->getCreditmemoId());
        $spbCreditMemoComment->setComment($creditmemoComment->getComment());
        $spbCreditMemoComment->setCreatedAt($creditmemoComment->getCreatedAt());
        $spbCreditMemoComment->setIsCustomerNotified($creditmemoComment->getIsCustomerNotified());
        $spbCreditMemoComment->setIsVisibleOnFront($creditmemoComment->getIsVisibleOnFront());
        return $spbCreditMemoComment;
    }
}