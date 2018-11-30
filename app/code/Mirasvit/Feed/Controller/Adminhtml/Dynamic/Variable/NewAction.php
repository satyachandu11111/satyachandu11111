<?php
namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Variable;

use Mirasvit\Feed\Controller\Adminhtml\Dynamic\Variable as DynamicVariable;

class NewAction extends DynamicVariable
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
