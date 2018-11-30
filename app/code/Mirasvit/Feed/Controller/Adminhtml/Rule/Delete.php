<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Rule;

use Mirasvit\Feed\Controller\Adminhtml\Rule;

class Delete extends Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $model = $this->initModel();
            $model->delete();

            $this->messageManager->addSuccess(__('Item was successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
