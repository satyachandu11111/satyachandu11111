<?php

namespace JustShout\Gfs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;

/**
 * Gfs Helper
 *
 * @package   JustShout\Gfs
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2018
 */
class Data extends AbstractHelper
{
    /**
     * Json
     *
     * @var Json
     */
    protected $_json;

    /**
     * Data constructor
     *
     * @param Context $context
     * @param Json    $json
     */
    public function __construct(
        Context $context,
        Json    $json
    ) {
        parent::__construct($context);
        $this->_json = $json;
    }

    /**
     * This method will get the data from the close checkout response stored against the order entity
     *
     * @param Order $order
     *
     * @return array
     */
    public function getGfsCloseCheckoutData(Order $order)
    {
        try {
            $gfsCloseCheckout = $order->getData('gfs_close_checkout');
            if (!$gfsCloseCheckout) {
                throw new \Exception();
            }
            $gfsCloseCheckoutData = $this->_json->unserialize($gfsCloseCheckout);
        } catch (\Exception $e) {
            $gfsCloseCheckoutData = [];
        }

        return $gfsCloseCheckoutData;
    }

    /**
     * This method will get the gfs shipping stored against the order entity
     *
     * @param Order $order
     *
     * @return array
     */
    public function getGfsShippingData(Order $order)
    {
        try {
            $gfsShipping = $order->getData('gfs_shipping_data');
            if (!$gfsShipping) {
                throw new \Exception();
            }
            $gfsShippingData = $this->_json->unserialize($gfsShipping);
        } catch (\Exception $e) {
            $gfsShippingData = [];
        }

        return $gfsShippingData;
    }

    /**
     * Get a gfs date as a date time object
     *
     * @param string $date
     *
     * @return \DateTime
     */
    public function getGfsDate($date)
    {
        return new \DateTime($date);
    }
}
