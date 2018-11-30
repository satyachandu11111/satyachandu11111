<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Template;

use Mirasvit\Feed\Controller\Adminhtml\Template;

class MassExport extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        foreach ($this->getRequest()->getParam('template') as $templateId) {
            $model = $this->templateFactory->create()->load($templateId);
            $path = $model->export();
            $this->messageManager->addSuccess(__('Template "%1" has been exported to "%2"', $model->getName(), $path));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
