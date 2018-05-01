<?php

namespace Jjcommerce\CollectPlus\Observer;

use Magento\Store\Model\StoresConfig;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class OrderExport
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    protected $io;

    protected $directorylist;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param StoresConfig $storesConfig
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     */
    public function __construct(
        StoresConfig $storesConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        DateTime $dateTime,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->storesConfig = $storesConfig;
        $this->logger = $logger;
        $this->orderCollectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        $this->directorylist = $directoryList;
        $this->io = $io;
    }

    /**
     * Export orders (cron process)
     *
     * @return void
     */
    public function execute()
    {

        $fileName = 'CollectPlus_Orders_' . $this->dateTime->date('Y-m-d_H-i-s') . '.csv';

        /** @var $orders \Magento\Sales\Model\ResourceModel\Order\Collection */
        $orders = $this->orderCollectionFactory->create();
        $orders->addFieldToFilter('shipping_method', array('like' => "collect_collect%"));
        $orders->addFieldToFilter('status', array('nin' => array('canceled', 'complete')));
        $orders->addFieldToFilter('agent_data', array('notnull' => true));
        $orders->addAttributeToFilter('export_file_name', array('null' => true));
//            $orders->getSelect()->where(
//                new \Zend_Db_Expr('TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) >= ' . $lifetime * 60)
//            );
        //$this->logger->debug($orders->getSelect());
        try {

            $titles = array('account number', 'customer reference', 'customer name', 'customer email', 'parcel weight', 'customer mobile number', 'SiteNumber', 'shipping address 1', 'shipping address 2', 'shipping address 3', 'shipping address 4', 'shipping address postcode', 'HDNCode');
            $products_row[] = $titles;
            $flag = false;
            foreach ($orders as $order) {
                $order = $order->load($order->getEntityId());
                $collectPlusAccountNumber = '';
                $shippingMethod = $order->getShippingMethod();
                if (strpos($shippingMethod, 'next')) {
                    $collectPlusAccountNumber = $this->storesConfig->getStoresConfigByPath('carriers/collect/next_day_account');
                } elseif(strpos($shippingMethod, '48')) {
                    $collectPlusAccountNumber = $this->storesConfig->getStoresConfigByPath('carriers/collect/two_day_account');
                } elseif(strpos($shippingMethod, '72')) {
                    $collectPlusAccountNumber = $this->storesConfig->getStoresConfigByPath('carriers/collect/three_day_account');
                }

                $collectPlusAccountNumber = is_array($collectPlusAccountNumber) ? end($collectPlusAccountNumber) : $collectPlusAccountNumber;

                $agentData = unserialize($order->getAgentData());
                $data = array();
                $data['account_code'] = $collectPlusAccountNumber;
                $data['order_id'] = $order->getIncrementId();
                //$data['customer_name'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                $data['customer_name'] = $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname();
                $data['customer_email'] = $order->getCustomerEmail();
                $data['weight'] = $order->getWeight() ? $order->getWeight() : 10;
                $data['sms_alert'] = $order->getSmsAlert();
                $data['site_number'] = $agentData['SiteNumber'];
                $data['address1'] = $agentData['SiteName'];
                $data['address2'] = $agentData['Address'];
                $data['address3'] = $agentData['City'];
                $data['address4'] = $agentData['County'];
                $data['postcode'] = $agentData['Postcode'];
                $data['HDN'] = $agentData['HDNCode'];
                $products_row[] = $data;

                $order->setData('export_file_name', $fileName);
                $flag = true;
                //$order->save();
            }
            //write to csv file
            if ($flag) {
                $content = '';
                foreach ($products_row as $row) {
                    $content .= implode(',', $row) . "\n";
                }
                /** @var \Magento\Framework\Filesystem\Io\File $io **/
                $collectDir = $this->directorylist->getPath('var').'/export/collectplus';
                $this->io->mkdir($collectDir, 0775);

                $updatefile = fopen($collectDir.'/'.$fileName, "a+") or die("Unable to open file!");
                fwrite($updatefile, $content);
                fclose($updatefile);

                $orders->walk('save');
            }

        } catch (\Exception $e) {
            $this->logger->error('Error exporting order: ' . $e->getMessage());
        }

    }
}
