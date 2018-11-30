<?php

namespace Mirasvit\Feed\Controller\Adminhtml\Feed;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Feed\Controller\Adminhtml\Feed;

class ReportGrid extends Feed
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initModel();

        $this->getResponse()->setBody(
            $resultPage->getLayout()->createBlock(\Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\Report\Grid::class)
                ->toHtml()
        );
    }
}
