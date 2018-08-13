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
 * @package   mirasvit/module-email
 * @version   2.1.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Controller\Adminhtml\Campaign;

use Mirasvit\Email\Controller\Adminhtml\Campaign;

class NewAction extends Campaign
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['_current' => true]);
    }
}
