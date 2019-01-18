<?php
namespace Dividebuy\Payment\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class PaymentObserver implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendModelSession;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileManagement;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Backend\Model\Session                     $backendModelSession
     * @param \Magento\Framework\Filesystem\Driver\File          $file
     * @param \Magento\Framework\Filesystem                      $fileSystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendModelSession,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_backendModelSession = $backendModelSession;
        $this->_fileManagement      = $file;
        $this->_fileSystem          = $fileSystem;
        $this->_config              = $config;
        $this->storeManager         = $storeManager;
    }

    /**
     * Used to set hide dividebuy field to 1
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $storeId                 = $this->storeManager->getStore()->getId();
        $dividebuyMediaDirectory = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath() . \Dividebuy\CheckoutConfig\Helper\Data::DIVIDEBUY_MEDIA_DIR;

        $paymentButtonImage = $this->_config->getValue("payment/dbpayment/button_image",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!empty($this->_backendModelSession->getPreviousPaymentButtonImage()) && $this->_backendModelSession->getPreviousPaymentButtonImage() != $paymentButtonImage) {
            $fullPath = $dividebuyMediaDirectory . $this->_backendModelSession->getPreviousPaymentButtonImage();
            if ($this->_fileManagement->isExists($fullPath)) {
                $this->_fileManagement->deleteFile($fullPath);
            }
        }
        $this->_backendModelSession->unsPreviousPaymentButtonImage();
    }
}
