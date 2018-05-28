<?php

namespace Homescapes\Orderswatch\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Homescapes\Orderswatch\Model\OrderswatchFactory;
use Homescapes\Orderswatch\Model\ResourceModel\Orderswatch\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var OrderswatchFactory
     */
    protected $orderswatchFactory;
    
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderswatchFactory $orderswatchFactory
     */
    public function __construct(Context $context, Filter $filter,CollectionFactory $collectionFactory, OrderswatchFactory $orderswatchFactory)
    {
        $this->filter = $filter;
        $this->orderswatchFactory = $orderswatchFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deleteIds = $collection->getAllIds();        
        
        $i=0;
        foreach ($deleteIds as $deleteId) {
            $model = $this->orderswatchFactory->create();
            $model->load($deleteId);
            $model->delete();            
            $i++;
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been deleted.', $i));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
