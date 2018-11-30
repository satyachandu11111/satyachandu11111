<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Template;

use Mirasvit\Feed\Controller\Adminhtml\Template;

class NewAction extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
