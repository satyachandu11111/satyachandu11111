<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


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
