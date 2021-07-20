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
namespace Aheadworks\Raf\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class Sender
 *
 * @package Aheadworks\Raf\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        TransportBuilder $transportBuilder
    ) {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return bool
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($emailMetadata)
    {
        $this->transportBuilder
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
            ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

        $this->transportBuilder->getTransport()->sendMessage();

        return true;
    }
}
