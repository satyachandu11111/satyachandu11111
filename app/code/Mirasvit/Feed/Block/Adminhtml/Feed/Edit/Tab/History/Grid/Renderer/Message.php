<?php
namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\History\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Message extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        return nl2br(htmlentities($row->getMessage()));
    }
}
