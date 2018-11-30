<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Attribute;

use Mirasvit\Feed\Controller\Adminhtml\Dynamic\Attribute as DynamicAttribute;

class Save extends DynamicAttribute
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getParams()) {
            $model = $this->initModel();
            $data = $this->filterValues($data);
            $model->setData($data);

            try {
                $model->save();

                $this->messageManager->addSuccessMessage(__('Item was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Unable to find item to save'));
            return $resultRedirect->setPath('*/*/');
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterValues($data)
    {
        if (isset($data['conditions'])) {
            $data['conditions'] = array_values($data['conditions']);
        }

        return $data;
    }
}
