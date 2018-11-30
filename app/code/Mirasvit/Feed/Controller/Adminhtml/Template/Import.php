<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Feed\Controller\Adminhtml\Template;

class Import extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($templates = $this->getRequest()->getParam('template')) {
            foreach ($templates as $templatePath) {
                try {
                    $model = $this->templateFactory->create()->import($templatePath);
                    $this->messageManager->addSuccess(__('Template "%1" has been imported.', $model->getName()));
                } catch (\Exception $e) {
                    $this->messageManager->addError($templatePath . ' ' . $e->getMessage());
                }
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } else {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

            $this->initPage($resultPage)
                ->getConfig()->getTitle()->prepend(__('Import Feed Templates'));

            return $resultPage;
        }
    }
}
