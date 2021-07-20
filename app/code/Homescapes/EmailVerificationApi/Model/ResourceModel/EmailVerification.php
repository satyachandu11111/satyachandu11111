<?php

namespace Homescapes\EmailVerificationApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EmailVerification extends AbstractDb
{
	/**
     * Resource initialization
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('email_verification', 'customer_id');
	}
}