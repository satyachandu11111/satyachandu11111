<?php

namespace Homescapes\EmailVerificationApi\Block;

class Customer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Homescapes\EmailVerificationApi\Helper\Data
     */
    protected $_emailVerificationHelper;


    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Homescapes\EmailVerificationApi\Helper\Data $emailVerificationHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        array $data = []
    ) {
        $this->_emailVerificationHelper = $emailVerificationHelper;
        $this->_timezoneInterface = $timezoneInterface;
        parent::__construct($context, $data);
    }

    public function isUserVerified()
    {
        $model = $this->_emailVerificationHelper->getVerifiedCustomer();
        if ($model->getSize() == 0) {
            return false;
        }
        return true;
    }

    public function getConnectedAppTime()
    {
        $time = '';
        $model = $this->_emailVerificationHelper->getVerifiedCustomer();
        if ($model->getSize() > 0) {
            $firstItem = $model->getFirstItem();
            //$time = $this->_timezoneInterface->date($firstItem->getCreatedAt())->format('M d, Y H:i:s');
            //$time = date('M d, Y H:i:s', strtotime($firstItem->getCreatedAt()));
            //$time = $this->_timezoneInterface->date(new \DateTime($firstItem->getCreatedAt()))->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $time = $this->_timezoneInterface->formatDate(new \DateTime($firstItem->getCreatedAt()),\IntlDateFormatter::FULL,false);
        }
        return $time;
    }

}