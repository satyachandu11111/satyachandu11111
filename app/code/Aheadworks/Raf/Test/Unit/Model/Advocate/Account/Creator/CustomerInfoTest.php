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
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Account\Creator;

use Aheadworks\Raf\Model\Advocate\Account\Creator\CustomerInfo;
use Magento\Customer\Api\CustomerRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CustomerInfoTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Advocate\Account\Creator
 */
class CustomerInfoTest extends TestCase
{
    /**
     * Constant defined for testing
     */
    const CUSTOMER_ID = 143;

    /**
     * @var CustomerInfo
     */
    private $object;

    /**
     * @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);

        $this->object = $objectManager->getObject(
            CustomerInfo::class,
            [
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    /**
     * Test for getCustomerEmail method
     */
    public function testGetCustomerEmail()
    {
        $email = 'some@email.com';

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::CUSTOMER_ID)
            ->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $this->assertSame($email, $this->object->getCustomerEmail(self::CUSTOMER_ID));
    }

    /**
     * Test for getCustomerEmail method on exception
     */
    public function testGetCustomerEmailOnException()
    {
        $email = '';
        $exception = new NoSuchEntityException(__('some_exception'));

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::CUSTOMER_ID)
            ->willThrowException($exception);

        $this->assertSame($email, $this->object->getCustomerEmail(self::CUSTOMER_ID));
    }

    /**
     * Test for getCustomerName method
     */
    public function testGetCustomerName()
    {
        $name = 'Some Name';
        $firstName = 'Some';
        $lastName = 'Name';

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::CUSTOMER_ID)
            ->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())
            ->method('getFirstname')
            ->willReturn($firstName);
        $this->customerMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($lastName);
        $this->assertSame($name, $this->object->getCustomerName(self::CUSTOMER_ID));
    }

    /**
     * Test for getCustomerName method on exception
     */
    public function testGetCustomerNameOnException()
    {
        $name = '';
        $exception = new NoSuchEntityException(__('some_exception'));

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(self::CUSTOMER_ID)
            ->willThrowException($exception);

        $this->assertSame($name, $this->object->getCustomerName(self::CUSTOMER_ID));
    }
}
