<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Rule;

use Mirasvit\Feed\Controller\Adminhtml\Rule;

class Export extends Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();
        $path = $model->export();

        $this->messageManager->addSuccess(__('Filter rule exported to %1', $path));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
