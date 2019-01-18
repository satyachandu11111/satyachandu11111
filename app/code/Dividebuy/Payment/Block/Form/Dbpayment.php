<?php
namespace Dividebuy\Payment\Block\Form;

/**
 * Block for Cash On Delivery payment method form
 */
class Dbpayment extends \Magento\OfflinePayments\Block\Form\AbstractInstruction
{
    /**
     * Cash on delivery template
     * 
     * @var string
     */
    protected $_template = 'Dividebuy_Payment::form/dbpament.phtml';
}
