<?php
namespace Dividebuy\RetailerConfig\Block\Adminhtml;

class Notifications extends \Magento\Framework\View\Element\Template
{
    public function getMessage()
    {
        /*
          * Here you have check if there's a message to be displayed or not
          */
        $message = 'To "Activate/Deactivate" Dividebuy plugin goto Stores>Configuration>Dividebuy>Configuration. Select "Store View", goto General configuration then set "Activate/Deactivte DivideBuy" field  to "Activate/Deactivate".';
        return $message;
    }
}
