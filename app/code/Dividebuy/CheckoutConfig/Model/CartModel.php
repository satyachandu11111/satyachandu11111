<?php
namespace Dividebuy\CheckoutConfig\Model;

class CartModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productloader;

    /**
     * @param \Magento\Checkout\Model\Cart    $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\Product  $productloader
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Product $productloader
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_productloader   = $productloader;
    }

    /**
     * Used to add product in cart
     * 
     * @param integer $productId
     * @param array $itemParam
     */
    public function addProductsInCart($productId, $itemParams)
    {
        $cart          = $this->_checkoutSession;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeId       = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
        $product       = $objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($productId);
        //$product = $this->_productloader->load($productId);

        $cart->addProduct($product, $itemParams);
        $session = $this->_customerSession;
        $session->setCartWasUpdated(true);
        $cart->save();
    }
}
