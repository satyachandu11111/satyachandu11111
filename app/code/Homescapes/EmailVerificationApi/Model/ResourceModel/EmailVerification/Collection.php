<?php

namespace Homescapes\EmailVerificationApi\Model\ResourceModel\EmailVerification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Homescapes\EmailVerificationApi\Model\Data\EmailVerification;
use Homescapes\EmailVerificationApi\Model\ResourceModel\EmailVerification as EmailVerificationResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'customer_id';
    
	/**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
    	parent::_construct();
    	$this->_init(EmailVerification::class, EmailVerificationResource::class);
    }
}