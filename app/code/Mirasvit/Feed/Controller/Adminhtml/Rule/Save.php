<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Rule;

use Mirasvit\Feed\Controller\Adminhtml\Rule;

class Save extends Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getParams()) {
            $model = $this->initModel();
            $model->addData($data['data']);

            if (isset($data['rule'])) {
                $model->loadPost($data['rule']);
            }

            try {
                $model->save();

                $this->messageManager->addSuccess(__('Filter was successfully saved'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        } else {
            $this->messageManager->addError(__('Unable to find item to save'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
