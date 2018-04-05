<?php
namespace Bhs\DeliveryCountdown\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_resultPageFactory;
	protected $_delivery;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory		
		)
	{
		$this->_resultPageFactory = $resultPageFactory;		
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$resultPage = $this->_resultPageFactory->create();
		$block = $resultPage->getLayout()
                ->createBlock('Bhs\DeliveryCountdown\Block\DeliveryCountdown');
		echo $block->buildString();
		exit;		
	}
}
