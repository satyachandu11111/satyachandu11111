<?php

namespace Homescapes\Completelook\Controller\Adminhtml\Import;          


use Magento\ImportExport\Controller\Adminhtml\Import as ImportController;
use Magento\Framework\Controller\ResultFactory;

class Index extends ImportController
{
    
    public function execute()
    {
        $this->messageManager->addNotice(
            $this->_objectManager->get(\Magento\ImportExport\Helper\Data::class)->getMaxUploadSizeMessage()
        );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Homescapes_Completelook::importcompletelook');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Complete Look'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        return $resultPage;
    }
    
}
