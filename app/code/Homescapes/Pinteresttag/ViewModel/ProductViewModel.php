<?php
namespace Homescapes\Pinteresttag\ViewModel;

class ProductViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
	/**
	* @param \Magento\Framework\Registry $registry
	*/

	protected $_registry;
	protected $customer;
	public function __construct(
	\Magento\Framework\Registry $registry,
	\Magento\Customer\Model\Session $customerSession
	) {
	$this->_registry = $registry;
	$this->customer = $customerSession;
	}

    public function getCategoryName()
    {

      $category = $this->_registry->registry('current_category');
      return $category->getName();
    }

    public function getCustomerEmail()
    {
    	$customer = $this->customer->getCustomer();
    	return $customerEmail =  $customer->getId();
    
   }
}
?>