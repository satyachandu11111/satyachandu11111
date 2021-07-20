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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate;

use Aheadworks\Raf\Model\Advocate\PriceFormatter;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

/**
 * Class PriceFormatterTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate
 */
class PriceFormatterTest extends TestCase
{
    /**
     * @var PriceFormatter
     */
    private $object;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->priceCurrencyMock = $this->getMockForAbstractClass(
            PriceCurrencyInterface::class
        );

        $this->storeManagerMock = $this->getMockForAbstractClass(
            StoreManagerInterface::class
        );

        $this->object = $objectManager->getObject(
            PriceFormatter::class,
            [
                'storeManager' => $this->storeManagerMock,
                'priceCurrencyInterface' => $this->priceCurrencyMock
            ]
        );
    }

    /**
     * Testing of getFormattedPercentPrice method
     */
    public function testGetFormattedPercentPrice()
    {
        $price = 5.4500;
        $expected = '5.45%';

        $this->assertEquals($expected, $this->object->getFormattedPercentPrice($price));
    }

    /**
     * Testing of getFormattedFixedPriceByStore method
     */
    public function testGetFormattedFixedPriceByStore()
    {
        $price = 5.4500;
        $storeId = 2;
        $websiteId = 1;
        $currencyCode = '$';
        $expected = '$5.45';

        $storeMock = $this->createPartialMock(Store::class, ['getWebsiteId']);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $websiteMock = $this->createPartialMock(Website::class, ['getBaseCurrencyCode']);
        $websiteMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($currencyCode);

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);

        $this->priceCurrencyMock->expects($this->any())
            ->method('format')
            ->with($price, false, null, null, $currencyCode)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getFormattedFixedPriceByStore($price, $storeId));
    }

    /**
     * Testing of getFormattedFixedPriceByWebsite method
     */
    public function testGetFormattedFixedPriceByWebsite()
    {
        $price = 5.4500;
        $websiteId = 1;
        $currencyCode = '$';
        $expected = '$5.45';

        $websiteMock = $this->createPartialMock(Website::class, ['getBaseCurrencyCode']);
        $websiteMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($currencyCode);

        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);

        $this->priceCurrencyMock->expects($this->any())
            ->method('format')
            ->with($price, false, null, null, $currencyCode)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getFormattedFixedPriceByWebsite($price, $websiteId));
    }

    /**
     * Testing of getFormattedFixedPriceByWebsite method on exception
     */
    public function testGetFormattedFixedPriceByWebsiteOnException()
    {
        $price = 5.4500;
        $websiteId = 1;
        $expected = '$5.45';

        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->with($websiteId)
            ->willThrowException(new \Exception('exception message'));

        $this->priceCurrencyMock->expects($this->any())
            ->method('format')
            ->with($price, false, null, null, null)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->object->getFormattedFixedPriceByWebsite($price, $websiteId));
    }
}
