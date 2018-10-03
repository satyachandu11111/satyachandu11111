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
 * @package   mirasvit/module-email-designer
 * @version   1.1.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\EmailDesigner\Controller\Adminhtml\Theme;

use Magento\TestFramework\TestCase\AbstractBackendController;

class IndexTest extends AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Mirasvit_EmailDesigner::email_designer_theme';
        $this->uri = 'backend/email_designer/theme/index';

        parent::setUp();
    }

    /**
     * @covers Mirasvit\EmailDesigner\Controller\Adminhtml\Theme\Index::execute
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/email_designer/theme/index');
        $body = $this->getResponse()->getBody();

        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Add Theme', $body);
    }
}
