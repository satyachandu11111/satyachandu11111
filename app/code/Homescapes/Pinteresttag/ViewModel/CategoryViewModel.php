<?php
namespace Homescapes\Pinteresttag\ViewModel;

class CategoryViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
	/**
	* @param \Magento\Framework\Registry $registry
	*/

	protected $_registry;

	public function __construct(
	\Magento\Framework\Registry $registry
	) {
	$this->_registry = $registry;
	}

    public function getCategoryName()
    {

      $category = $this->_registry->registry('current_category');
      return $category->getName();
    }
}
?>