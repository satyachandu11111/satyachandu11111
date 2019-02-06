<?php

namespace JustShout\Gfs\Ui\Component\Listing\Column;

use JustShout\Gfs\Helper\Data;
use JustShout\Gfs\Model\Carrier\Gfs;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Gfs Shipping Details Grid Column
 *
 * @package   JustShout\Gfs
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2018
 */
class ShippingDetails extends Column
{
    /**
     * Order Repository
     *
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * GFS Helper
     *
     * @var Data
     */
    protected $_gfsHelper;

    /**
     * Shipping Details constructor
     *
     * @param ContextInterface         $context
     * @param UiComponentFactory       $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Data                     $gfsHelper
     * @param array                    $components
     * @param array                    $data
     */
    public function __construct(
        ContextInterface         $context,
        UiComponentFactory       $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        Data                     $gfsHelper,
        array                    $components = [],
        array                    $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->_orderRepository = $orderRepository;
        $this->_gfsHelper = $gfsHelper;
    }

    /**
     * {@inheritdoc}
     *
     * Adding in gfs delivery details
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $order = $this->_getOrder((int) $item['entity_id']);
                if ($order->getShippingMethod() === Gfs::METHOD_CODE) {
                    $item[$this->getData('name')] = $this->_getGfsDetails($order);
                } else {
                    $item[$this->getData('name')] = '-';
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get the order entity
     *
     * @param $orderId
     *
     * @return Order|null
     */
    protected function _getOrder($orderId)
    {
        try {
            $order = $this->_orderRepository->get($orderId);
        } catch (\Exception $e) {
            $order = null;
        }

        return $order;
    }

    /**
     * This method will get the details for the fs delivery based on type of method
     *
     * @param Order $order
     *
     * @return string
     */
    protected function _getGfsDetails($order)
    {
        $html = '-';
        $gfsShippingData = $this->_gfsHelper->getGfsShippingData($order);
        if (empty($gfsShippingData)) {
            return $html;
        }
        switch ($gfsShippingData['shippingMethodType']) {
            case 'standard':
            case 'calendar':
                $html = $this->_getGfsDetailsStandard($order);
                break;
            case 'droppoint':
                $html = $this->_getGfsDetailsDropPoint($order);
                break;
        }

        return $html;
    }

    /**
     * This method will get details for the gfs delivery
     *
     * @param Order $order
     *
     * @return string
     */
    protected function _getGfsDetailsStandard($order)
    {
        $gfsData = $this->_getGfsCloseCheckoutData($order);
        if (!$gfsData) {
            return '-';
        }

        $lines = [];
        if (isset($gfsData['selectedService']['methodTitle']) && isset($gfsData['selectedService']['deliveryTimeFrom'])) {
            $deliveryTimeFrom = $this->_gfsHelper->getGfsDate($gfsData['selectedService']['deliveryTimeFrom']);
            $lines[] = __('<strong>Service:</strong> %1 - %2',
                $gfsData['selectedService']['methodTitle'],
                $deliveryTimeFrom->format('d/m/Y')
            );
        }

        if (isset($gfsData['selectedService']['carrierName'])) {
            $lines[] = __('<strong>Carrier:</strong> %1', $gfsData['selectedService']['carrierName']);
        }

        if (isset($gfsData['selectedService']['carrierCode'])) {
            $lines[] = __('<strong>Service Code:</strong> %1', $gfsData['selectedService']['carrierCode']);
        }

        return implode('<br/>', $lines);
    }

    /**
     * This method will get details for the gfs delivery as well as the details of the drop point address.
     *
     * @param Order $order
     *
     * @return string
     */
    protected function _getGfsDetailsDropPoint($order)
    {
        $gfsData = $this->_getGfsCloseCheckoutData($order);
        if (!$gfsData) {
            return '-';
        }

        $lines = [];
        if (isset($gfsData['selectedDroppoint']['droppointDescription'])) {
            $lines[] = trim($gfsData['selectedDroppoint']['droppointDescription']);
        }
        if (isset($gfsData['selectedDroppoint']['geoLocation']['addressLines'])) {
            $lines[] = implode(', ', $gfsData['selectedDroppoint']['geoLocation']['addressLines']);
        }
        if (isset($gfsData['selectedDroppoint']['geoLocation']['county'])) {
            $lines[] = trim($gfsData['selectedDroppoint']['geoLocation']['county']);
        }
        if (isset($gfsData['selectedDroppoint']['geoLocation']['town'])) {
            $lines[] = trim($gfsData['selectedDroppoint']['geoLocation']['town']);
        }
        if (isset($gfsData['selectedDroppoint']['geoLocation']['postCode'])) {
            $lines[] = trim($gfsData['selectedDroppoint']['geoLocation']['postCode']);
        }
        if (isset($gfsData['selectedDroppoint']['droppointId'])) {
            $lines[] = '<strong>' . $gfsData['selectedDroppoint']['droppointId'] . '</strong>';
        }

        $html = $this->_getGfsDetailsStandard($order);
        $html .= __('<address><strong>Address:</strong><br/> %1</address>',
            implode('<br/>', $lines)
        );

        return $html;
    }

    /**
     * This method will get the data from the close checkout response stored against the order entity
     *
     * @param Order $order
     *
     * @return array
     */
    protected function _getGfsCloseCheckoutData($order)
    {
        return $this->_gfsHelper->getGfsCloseCheckoutData($order);
    }
}
