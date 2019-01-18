<?php
namespace Dividebuy\Payment\Plugins;

use Magento\Framework\App\ResourceConnection as ResourceConnection;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

/**
 * Class CollectionPool
 */
class DbSalesOrderGridCollection
{
    private $messageManager;
    private $collection;
    private $resource;

    /**
     * @param MessageManager           $messageManager
     * @param SalesOrderGridCollection $collection
     * @param ResourceConnection       $resource
     */
    public function __construct(MessageManager $messageManager,
        SalesOrderGridCollection $collection,
        ResourceConnection $resource
    ) {

        $this->messageManager = $messageManager;
        $this->collection     = $collection;
        $this->resource       = $resource;
    }

    /**
     * Used to hide dividebuy order
     *
     * @param  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param  \Closure                                                                   $proceed
     * @param  type                                                                     $requestName
     * @return array
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result          = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source') {
            if ($result instanceof $this->collection) {
                $this->collection->addFieldToFilter('hide_dividebuy', array('eq' => 0));
                return $this->collection;
            }
        }
        return $result;
    }
}
