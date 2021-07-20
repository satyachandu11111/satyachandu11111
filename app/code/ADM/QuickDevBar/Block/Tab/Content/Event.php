<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use \ADM\QuickDevBar\Block\Tab;

class Event extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     *
     * @var \ADM\QuickDevBar\Helper\Register
     */
    protected $_qdbHelperRegister;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
            \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
            array $data = []
    ) {

        $this->_qdbHelperRegister = $qdbHelperRegister;

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        $events = $this->getEvents();
        return count($events);
    }


    public function getEvents()
    {
        return $this->_qdbHelperRegister->getEvents();
    }
}