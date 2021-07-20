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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Account;

use Aheadworks\Raf\Model\Advocate\Account\Viewer;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\Raf\Model\Advocate\Account\RewardMessage as AdvocateRewardMessage;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Raf\Model\AdvocateSummary;

/**
 * Class ViewerTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate\Account
 */
class ViewerTest extends TestCase
{
    /**
     *  List of constants defined for test
     */
    const WEBSITE_ID = 1;
    const STORE_CODE = 'code';

    /**
     * @var Viewer
     */
    private $object;

    /**
     * @var AdvocateSummaryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryRepositoryMock;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyInterfaceMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var AdvocateRewardMessage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateRewardMessageMock;

    /**
     * @var AdvocateSummary|\PHPUnit_Framework_MockObject_MockObject
     */
    private $advocateSummaryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->advocateSummaryRepositoryMock = $this->getMockForAbstractClass(
            AdvocateSummaryRepositoryInterface::class
        );
        $this->priceCurrencyInterfaceMock = $this->getMockForAbstractClass(
            PriceCurrencyInterface::class
        );
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);

        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(self::WEBSITE_ID);
        $storeMock->expects($this->any())
            ->method('getCode')
            ->willReturn(self::STORE_CODE);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->advocateSummaryMock = $this->createPartialMock(
            AdvocateSummary::class,
            ['getCumulativeAmount', 'getExpirationDate']
        );
        $this->advocateSummaryRepositoryMock->expects($this->any())
            ->method('getByCustomerId')
            ->willReturn($this->advocateSummaryMock);

        $this->advocateRewardMessageMock = $this->createPartialMock(
            AdvocateRewardMessage::class,
            ['getMessage']
        );

        $this->object = $objectManager->getObject(
            Viewer::class,
            [
                'advocateSummaryRepository' => $this->advocateSummaryRepositoryMock,
                'priceCurrencyInterface' => $this->priceCurrencyInterfaceMock,
                'localeDate' => $this->localeDateMock,
                'storeManager' => $this->storeManagerMock,
                'advocateRewardMessage' => $this->advocateRewardMessageMock
            ]
        );
    }

    /**
     * Testing of getCumulativeBalanceFormatted method
     */
    public function testGetCumulativeBalanceFormatted()
    {
        $customerId = 10;
        $storeId = 4;
        $cumulativeAmount = 10.6;
        $result = '$10.6';

        $this->advocateSummaryMock->expects($this->once())
            ->method('getCumulativeAmount')
            ->willReturn($cumulativeAmount);
        $this->priceCurrencyInterfaceMock->expects($this->once())
            ->method('convertAndFormat')
            ->with(
                $cumulativeAmount,
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $storeId
            )->willReturn($result);

        $this->assertSame($result, $this->object->getCumulativeBalanceFormatted($customerId, $storeId));
    }

    /**
     * Testing of getCumulativeBalanceFormatted method without cumulative amount
     */
    public function testGetCumulativeBalanceFormattedWithoutCumulativeAmount()
    {
        $customerId = 10;
        $storeId = 4;
        $cumulativeAmount = null;
        $result = __('N/A');

        $this->advocateSummaryMock->expects($this->once())
            ->method('getCumulativeAmount')
            ->willReturn($cumulativeAmount);

        $this->assertEquals($result, $this->object->getCumulativeBalanceFormatted($customerId, $storeId));
    }

    /**
     * Testing of getBalanceExpiredFormatted method
     */
    public function testGetBalanceExpiredFormatted()
    {
        $customerId = 10;
        $storeId = 4;
        $expirationDate = '10-10-2010';
        $timezone = 'test timezone';
        $result = '10 October, 2010';

        $this->advocateSummaryMock->expects($this->once())
            ->method('getExpirationDate')
            ->willReturn($expirationDate);
        $this->localeDateMock->expects($this->once())
            ->method('getConfigTimezone')
            ->with(ScopeInterface::SCOPE_STORE, self::STORE_CODE)
            ->willReturn($timezone);
        $this->localeDateMock->expects($this->once())
            ->method('formatDateTime')
            ->with(
                new \DateTime($expirationDate),
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE,
                null,
                $timezone
            )->willReturn($result);

        $this->assertSame($result, $this->object->getBalanceExpiredFormatted($customerId, $storeId));
    }
}
