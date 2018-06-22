<?php

namespace Homescapes\Ajaxcart\Controller\Cart;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }
            
            //$this->cart->addProduct($product, $params);
            
            $html = '';
           
            
//            echo "<pre>";
//            print_r($product->getName().'---->'.$params['qty']);
            
            $loadConfigChildProduct = '';
            //echo $product->getTypeId();
            
            $storeId = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore()->getId();
            
            if($product->getTypeId()=='grouped'){
                if($params['super_group']){
                    foreach($params['super_group'] as $key => $val)
                    {
                        if($val!=0){
                            $loadConfigChildProduct = $this->productRepository->getById($key, false, $storeId);
                            $html.='<span class="name">'.$val.' x '.$loadConfigChildProduct->getName().'</span>';
                        }
                    }

                }
            }else{
                $html.='<span class="name">'.$params['qty'].' x '.$product->getName().'</span>';
            }
            
            $result = array();
            $result['product_name'] = $html;
            $result['ajaxcart-product-id'] = $params['product'];
            
            //$html.='<span class="name">'.$val.' x '.Mage::getModel('catalog/product')->load($key)->getName().'</span>';
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                return $this->goBack(null, $product,$result);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->_objectManager->get(\Magento\Checkout\Helper\Cart::class)->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            return $this->goBack();
        }
    }
    
    protected function goBack($backUrl = null, $product = null,$html = array())
    {
        
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }
        
        $result = array_merge($result,$html);
        
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
    
}
