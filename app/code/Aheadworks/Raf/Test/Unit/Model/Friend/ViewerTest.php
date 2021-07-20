<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Test\Unit\Model\Friend;

use Aheadworks\Raf\Model\Friend\Viewer;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Renderer\Cms\Block as CmsBlockRenderer;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ViewerTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Friend
 */
class ViewerTest extends TestCase
{
    /**
     * @var Viewer
     */
    private $object;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var CmsBlockRenderer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsBlockRendererMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createPartialMock(
            Config::class,
            ['getStaticBlockIdForWelcomePopup']
        );
        $this->cmsBlockRendererMock = $this->createPartialMock(
            CmsBlockRenderer::class,
            ['render']
        );

        $this->object = $objectManager->getObject(
            Viewer::class,
            [
                'config' => $this->configMock,
                'cmsBlockRenderer' => $this->cmsBlockRendererMock
            ]
        );
    }

    /**
     * Test for getStaticBlockHtmlForWelcomePopup method
     */
    public function testFetStaticBlockHtmlForWelcomePopup()
    {
        $storeId = 2;
        $blockId = 20;
        $result = 'html text';

        $this->configMock->expects($this->once())
            ->method('getStaticBlockIdForWelcomePopup')
            ->with($storeId)
            ->willReturn($blockId);
        $this->cmsBlockRendererMock->expects($this->once())
            ->method('render')
            ->with($blockId, $storeId)
            ->willReturn($result);

        $this->assertSame($result, $this->object->getStaticBlockHtmlForWelcomePopup($storeId));
    }

    /**
     * Test for getStaticBlockHtmlForWelcomePopup method when block is not specified
     */
    public function testFetStaticBlockHtmlForWelcomePopupWithNoBlock()
    {
        $storeId = 2;
        $blockId = null;
        $result = null;

        $this->configMock->expects($this->once())
            ->method('getStaticBlockIdForWelcomePopup')
            ->with($storeId)
            ->willReturn($blockId);

        $this->assertSame($result, $this->object->getStaticBlockHtmlForWelcomePopup($storeId));
    }
}
