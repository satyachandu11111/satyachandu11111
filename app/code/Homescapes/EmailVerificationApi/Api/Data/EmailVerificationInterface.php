<?php

namespace Homescapes\EmailVerificationApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface EmailVerificationInterface extends ExtensibleDataInterface
{
	const ID = "customer_id";
	const STATUS = "status";
	const EMAIL = "email";
	const VARIFICATION_CODE = "verification_code";
	const CREATED_AT = "created_at";
	const UPDATED_AT = "updated_at";

	/**
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * @return string
	 */
	public function getEmail();

	/**
	 * @return string
	 */
	public function getVerificationCode();

	/**
	 * @return boolean
	 */
	public function getStatus();

	/**
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * @return string
	 */
	public function getUpdatedAt();


	/**
	 * @param int $customerId
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setCustomerId($customerId);

	/**
	 * @param string $email
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setEmail($email);

	/**
	 * @param boolean $status
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setStatus($status);

	/**
	 * @param string $verificationCode
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setVerificationCode($verificationCode);

	/**
	 * @param string $createdAt
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setCreatedAt($createdAt);

	/**
	 * @param string $updatedAt
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function setUpdatedAt($updatedAt);
}