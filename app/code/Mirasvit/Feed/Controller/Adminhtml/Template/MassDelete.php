<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Template;

use Mirasvit\Feed\Controller\Adminhtml\Template;

class MassDelete extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $deleted = 0;
        foreach ($this->getRequest()->getParam('template') as $id) {
            $this->templateFactory->create()->load($id)->delete();
            $deleted++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        return $resultRedirect->setPath('*/*/');
    }
}
