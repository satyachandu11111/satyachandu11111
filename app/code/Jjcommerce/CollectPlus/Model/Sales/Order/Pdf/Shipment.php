<?php
/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
//namespace Magento\Sales\Model\Order\Pdf;
namespace Jjcommerce\CollectPlus\Model\Sales\Order\Pdf;
use Jjcommerce\CollectPlus\Model\OSRef;

/**
 * Sales Order Shipment PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipment extends \Magento\Sales\Model\Order\Pdf\Shipment
{

    /**
     * Return PDF document
     *
     * @param \Magento\Sales\Model\Order\Shipment[] $shipments
     * @return \Zend_Pdf
     */
    public function getPdf($shipments = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                $this->_localeResolver->emulate($shipment->getStoreId());
                $this->_storeManager->setCurrentStore($shipment->getStoreId());
            }
            $page = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Packing Slip # ') . $shipment->getIncrementId());

            /*Collect+ Information Start*/
            $shipmethod = $order->getShippingMethod();
            $pos = strpos($shipmethod, 'collect_collect');

            $showcollectinfo = $this->_scopeConfig->getValue(
                'carriers/collect/pdf_shipment',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            );

            $isCollectEnable = (bool)$this->_scopeConfig->getValue(
                'carriers/collect/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($pos !== false && $showcollectinfo) {
                $agentData = unserialize($order->getAgentData());
                $GridX = $agentData['GridX'];
                $GridY = $agentData['GridY'];
                $latlng = $this->getLatLong($GridX, $GridY);
                $siteName = $agentData['SiteName'];
                $address = $agentData['Address'] . ', ' . $agentData['City'] . ', ' . $agentData['Postcode'];
                $disableAccess = $agentData['DisabledAccessCode'];
                if ($order->getSmsAlert()) {
                    $collectionInstruction = $this->_scopeConfig->getValue('carriers/collect/collection_instruction',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $collectionInstruction = str_replace(array('{email}', '{number}'), array("<b>" . $order->getCustomerEmail() . "</b>", "<b>" . $order->getSmsAlert() . "</b>"), $collectionInstruction);
                } else {
                    $collectionInstruction = str_replace('{email}', "<b>" . $order->getCustomerEmail() . "</b>", $this->_scopeConfig->getValue('carriers/collect/collection_instruction2',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                }
                $top = $this->y + 7;
                //$top -= 10;
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $top, 275, ($top - 25));
                $page->drawRectangle(275, $top, 570, ($top - 25));
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $this->_setFontBold($page, 12);
                $imagepath = '/collectplus/images/collect_logo_small.png';
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagepath));
                $y1 = $top - 23;
                $y2 = $top - 3;
                $x1 = 35;
                $x2 = $x1 + 60;
                $page->drawImage($image, $x1, $y1, $x2, $y2);
                $page->drawText(__('CollectPlus Site Address:'), $x2 + 10, ($top - 15), 'UTF-8');
                $page->drawText(__("CollectPlus Site's opening times:"), 285, ($top - 15), 'UTF-8');


                $addressesHeight = 105;
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
                $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $this->_setFontRegular($page, 10);
                $this->y = $top - 40;
                $addressesStartY = $this->y;

                $text = array();
                $text[] = 'Site Name: ' . $siteName;
                $text[] = 'Address: ' . $address;
                $text[] = 'Disable Access: ' . $disableAccess;
                $text[] = 'Collection Instruction: ';
                //foreach (Mage::helper('core/string')->str_split($collectionInstruction, 45, true, true) as $_value) {
                foreach (str_split($collectionInstruction, 45) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }

                $addressesEndY = $this->y;
                $this->y = $addressesStartY;
                $text = array();
                if ($agentData['MondayOpen'] != '0000' && $agentData['MondayClose'] != '0000') {
                    $text[] = 'Monday: ' . $agentData['MondayOpen'] . '-' . $agentData['MondayClose'];
                }
                if ($agentData['TuesdayOpen'] != '0000' && $agentData['TuesdayClose'] != '0000') {
                    $text[] = 'Tuesday: ' . $agentData['TuesdayOpen'] . '-' . $agentData['TuesdayClose'];
                }
                if ($agentData['WednesdayOpen'] != '0000' && $agentData['WednesdayClose'] != '0000') {
                    $text[] = 'Wednesday: ' . $agentData['WednesdayOpen'] . '-' . $agentData['WednesdayClose'];
                }
                if ($agentData['ThursdayOpen'] != '0000' && $agentData['ThursdayClose'] != '0000') {
                    $text[] = 'Thursday: ' . $agentData['ThursdayOpen'] . '-' . $agentData['ThursdayClose'];
                }
                if ($agentData['FridayOpen'] != '0000' && $agentData['FridayClose'] != '0000') {
                    $text[] = 'Friday: ' . $agentData['FridayOpen'] . '-' . $agentData['FridayClose'];
                }
                if ($agentData['SaturdayOpen'] != '0000' && $agentData['SaturdayClose'] != '0000') {
                    $text[] = 'Saturday: ' . $agentData['SaturdayOpen'] . '-' . $agentData['SaturdayClose'];
                }
                if ($agentData['SundayOpen'] != '0000' && $agentData['SundayClose'] != '0000') {
                    $text[] = 'Sunday: ' . $agentData['SundayOpen'] . '-' . $agentData['SundayClose'];
                }

                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                    $this->y -= 15;
                }

                $addressesEndY = min($addressesEndY, $this->y);
                $this->y = $addressesEndY;
            }
            /*Collect+ Information End */

            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            $this->_localeResolver->revert();
        }
        return $pdf;
    }

    /**
     * @param float $GridX
     * @param float $GridY
     * @return null|string
     */
    public function getLatLong($GridX, $GridY)
    {


        $OSRef = new OSRef($GridX, $GridY); //Easting, Northing
        $LatLng = $OSRef->toLatLng();
        $LatLng->toWGS84(); //optional, for GPS compatibility

        $lat =  $LatLng->getLat();
        $long = $LatLng->getLng();
        $latlong = $lat.','.$long;
        return $latlong;
    }

}
