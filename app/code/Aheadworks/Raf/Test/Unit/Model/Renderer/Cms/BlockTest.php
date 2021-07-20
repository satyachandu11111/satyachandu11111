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
namespace Aheadworks\Raf\Test\Unit\Model\Renderer\Cms;

use Aheadworks\Raf\Model\Renderer\Cms\Block;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\Template\FilterProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Filter\Template;

/**
 * Class BlockTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Renderer\Cms
 */
class BlockTest extends TestCase
{
    /**
     * List of constants defined for testing
     */
    const STORE_ID = 1;
    const BLOCK_ID = 4;

    /**
     * @var Block
     */
    private $object;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var BlockRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsBlockRepositoryMock;

    /**
     * @var FilterProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsFilterProviderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->cmsBlockRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->cmsFilterProviderMock = $this->createPartialMock(
            FilterProvider::class,
            ['getBlockFilter']
        );

        $this->object = $objectManager->getObject(
            Block::class,
            [
                'storeManager' => $this->storeManagerMock,
                'cmsBlockRepository' => $this->cmsBlockRepositoryMock,
                'cmsFilterProvider' => $this->cmsFilterProviderMock
            ]
        );
    }

    /**
     * Test for render method
     */
    public function testRender()
    {
        $blockHtml = 'Some html';
        $blockContent = 'Some content';

        $cmsBlockMock =  $this->getMockForAbstractClass(BlockInterface::class);
        $filterTemplateMock = $this->createPartialMock(
            Template::class,
            ['setStoreId','filter']
        );

        $this->cmsBlockRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::BLOCK_ID)
            ->willReturn($cmsBlockMock);
        $cmsBlockMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);
        $cmsBlockMock->expects($this->once())
            ->method('getContent')
            ->willReturn($blockContent);
        $this->cmsFilterProviderMock->expects($this->once())
            ->method('getBlockFilter')
            ->willReturn($filterTemplateMock);
        $filterTemplateMock->expects($this->once())
            ->method('setStoreId')
            ->with(self::STORE_ID)
            ->willReturnSelf();
        $filterTemplateMock->expects($this->once())
            ->method('filter')
            ->with($blockContent)
            ->willReturn($blockHtml);

        $this->assertSame($blockHtml, $this->object->render(self::BLOCK_ID, self::STORE_ID));
    }

    /**
     * Test for render method on exception
     */
    public function testRenderOnException()
    {
        $blockHtml = '';
        $exception = new LocalizedException(__('some exception'));

        $this->cmsBlockRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::BLOCK_ID)
            ->willThrowException($exception);

        $this->assertSame($blockHtml, $this->object->render(self::BLOCK_ID, self::STORE_ID));
    }
}
