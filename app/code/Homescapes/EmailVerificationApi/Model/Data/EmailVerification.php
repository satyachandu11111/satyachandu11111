<?php

namespace Homescapes\EmailVerificationApi\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Homescapes\EmailVerificationApi\Model\ResourceModel\EmailVerification as EmailVerificationResource;
use Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface;

class EmailVerification extends AbstractExtensibleModel implements EmailVerificationInterface
{
    protected function _construct() 
    {
    	$this->_init(EmailVerificationResource::class);
    }

    /**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->getData(EmailVerificationInterface::ID);
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->getData(EmailVerificationInterface::EMAIL);
	}

	/**
	 * @return boolean
	 */
	public function getStatus()
	{
		return $this->getData(EmailVerificationInterface::STATUS);
	}

	/**
	 * @return string
	 */
	public function getVerificationCode()
	{
		return $this->getData(EmailVerificationInterface::VARIFICATION_CODE);
	}
	
	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->getData(EmailVerificationInterface::CREATED_AT);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->getData(EmailVerificationInterface::UPDATED_AT);
	}

	/**
	 * @param int $customerId
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setCustomerId($customerId)
	{
		$this->setData(EmailVerificationInterface::ID, $customerId);
	}

	/**
	 * @param string $email
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setEmail($email)
	{
		$this->setData(EmailVerificationInterface::EMAIL, $email);
	}

	/**
	 * @param boolean $status
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setStatus($status)
	{
		$this->setData(EmailVerificationInterface::STATUS, $status);
	}

	/**
	 * @param string $verificationCode
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setVerificationCode($verificationCode)
	{
		$this->setData(EmailVerificationInterface::VARIFICATION_CODE, $verificationCode);
	}

	/**
	 * @param string $createdAt
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setCreatedAt($createdAt)
	{
		$this->setData(EmailVerificationInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @param string $updatedAt
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->setData(EmailVerificationInterface::UPDATED_AT, $updatedAt);
	}
}