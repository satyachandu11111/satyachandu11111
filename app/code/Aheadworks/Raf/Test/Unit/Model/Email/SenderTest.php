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
namespace Aheadworks\Raf\Test\Unit\Model\Email;

use Aheadworks\Raf\Model\Email\EmailMetadataInterface;
use Aheadworks\Raf\Model\Email\Sender;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class SenderTest
 *
 * @package Aheadworks\Raf\Test\Unit\Model\Email
 */
class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $object;

    /**
     * @var TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportBuilderMock;

    /**
     * @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->transportBuilderMock = $this->createPartialMock(
            TransportBuilder::class,
            [
                'setTemplateIdentifier',
                'setTemplateOptions',
                'setTemplateVars',
                'setFrom',
                'addTo',
                'getTransport'
            ]
        );
        $this->transportMock = $this->getMockForAbstractClass(TransportInterface::class);

        $this->object = $objectManager->getObject(
            Sender::class,
            ['transportBuilder' => $this->transportBuilderMock]
        );
    }

    /**
     * Testing of send method
     */
    public function testSend()
    {
        $expectedValue = true;
        $emailMetadataMock = $this->initSender();
        $this->transportMock->expects($this->once())
            ->method('sendMessage');

        $this->assertEquals($expectedValue, $this->object->send($emailMetadataMock));
    }

    /**
     * Testing of send method on exception
     *
     * @expectedException MailException
     */
    public function testSendOnException()
    {
        $this->expectException(MailException::class);
        $exception = new MailException(__('Exception message.'));
        $emailMetadataMock = $this->initSender();

        $this->transportMock->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($exception);

        $this->object->send($emailMetadataMock);
    }

    /**
     * Init sender method
     *
     * @return EmailMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function initSender()
    {
        $emailMetadata = [
            EmailMetadataInterface::TEMPLATE_ID => 'template_id',
            EmailMetadataInterface::TEMPLATE_OPTIONS => ['opt1', ['opt2']],
            EmailMetadataInterface::TEMPLATE_VARIABLES => ['var1', 'var2'],
            EmailMetadataInterface::SENDER_NAME => 'sender_name',
            EmailMetadataInterface::SENDER_EMAIL => 'sender_email',
            EmailMetadataInterface::RECIPIENT_NAME => 'recipient_name',
            EmailMetadataInterface::RECIPIENT_EMAIL => 'recipient_email',
        ];

        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateId')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_ID]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateOptions')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateVariables')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_EMAIL]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL]);

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_ID])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with([
                'name' => $emailMetadata[EmailMetadataInterface::SENDER_NAME],
                'email' => $emailMetadata[EmailMetadataInterface::SENDER_EMAIL]
            ])->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with(
                $emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL],
                $emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]
            )->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturn($this->transportMock);

        return $emailMetadataMock;
    }
}
