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
namespace Aheadworks\Raf\Test\Unit\Model\Friend\Referral\CookieManagement;

use Aheadworks\Raf\Model\Friend\Referral\CookieManagement\CookieMetadataResolver;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class CookieMetadataResolverTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Friend\Referral\CookieManagement
 */
class CookieMetadataResolverTest extends TestCase
{
    /**
     * @var CookieMetadataResolver
     */
    private $object;

    /**
     * @var CookieMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cookieMetadataFactoryMock;

    /**
     * @var ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->cookieMetadataFactoryMock = $this->createMock(CookieMetadataFactory::class);

        $this->sessionConfigMock = $this->createMock(ConfigInterface::class);

        $this->object = $objectManager->getObject(
            CookieMetadataResolver::class,
            [
                'cookieMetadataFactory' => $this->cookieMetadataFactoryMock,
                'sessionConfig' => $this->sessionConfigMock,
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param bool $isSpecifiedMetaData
     * @dataProvider resolveProvider
     */
    public function testResolve($isSpecifiedMetaData)
    {
        $cookieDomain = 'domain';
        $cookiePath = 'path';

        $cookieMetadataMock = $this->createPartialMock(
            PublicCookieMetadata::class,
            ['setDomain', 'setPath', 'setDurationOneYear']
        );
        $this->cookieMetadataFactoryMock->expects($this->any())
            ->method('createPublicCookieMetadata')
            ->willReturn($cookieMetadataMock);
        $this->sessionConfigMock->expects($this->any())
            ->method('getCookieDomain')
            ->willReturn($cookieDomain);
        $this->sessionConfigMock->expects($this->any())
            ->method('getCookiePath')
            ->willReturn($cookiePath);
        $cookieMetadataMock->expects($this->any())
            ->method('setDomain')
            ->with($cookieDomain)
            ->willReturnSelf();
        $cookieMetadataMock->expects($this->any())
            ->method('setPath')
            ->with($cookiePath)
            ->willReturnSelf();
        $cookieMetadataMock->expects($this->any())
            ->method('setDurationOneYear')
            ->willReturnSelf();

        $cookieMetadataValue = $cookieMetadataMock;
        if (!$isSpecifiedMetaData) {
            $cookieMetadataValue = '';
        }

        $this->assertEquals($cookieMetadataMock, $this->object->resolve($cookieMetadataValue));
    }

    /**
     * Data provider for testResolve method
     *
     * @return array
     */
    public function resolveProvider()
    {
        return [
            'meta data is specified' => [true],
            'meta data is not specified' => [false]
        ];
    }
}
