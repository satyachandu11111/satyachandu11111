<?php

namespace Homescapes\EmailVerificationApi\Api;

interface EmailVerificationRepositoryInterface
{
	/**
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface[]
	 */
	public function getList();

	/**
	 * @param int $customer_id
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function getEmailVerificationById($customer_id);

	/**
	 * @param \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface $emailvarifiation
	 * @return \Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface
	 */
	public function saveMarsTicket(\Homescapes\EmailVerificationApi\Api\Data\EmailVerificationInterface $emailvarifiation);
}