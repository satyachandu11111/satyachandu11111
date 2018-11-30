<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Category;

use Mirasvit\Feed\Controller\Adminhtml\Dynamic\Category;

class NewAction extends Category
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
