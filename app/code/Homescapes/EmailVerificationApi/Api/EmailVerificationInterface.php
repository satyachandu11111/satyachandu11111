<?php 
namespace Homescapes\EmailVerificationApi\Api;
 
 
interface EmailVerificationInterface {

	/** 
	 * @api 
	 * @param string $email
	 * @param bool $valid 
	 * @return string 
	 */
	public function getEmail($email,$valid = true);

	/**
	 * @param string $code
	 * @return \Homescapes\EmailVerificationApi\Api\EmailVerificationInterface
	 */
	public function approveSubscription($code);

}