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
namespace Aheadworks\Raf\Model\Advocate\Email\Processor\Amount;

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor\VariableProcessorInterface;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Raf\Model\Email\EmailMetadataInterface;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\BaseAmountVariables;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractProcessor
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\Amount
 */
abstract class AbstractProcessor implements AmountProcessorInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    protected $emailMetadataFactory;

    /**
     * @var VariableProcessorInterface
     */
    protected $variableProcessorComposite;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorInterface $variableProcessorComposite
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * {@inheritdoc}
     */
    public function process($advocateSummary, $amount, $amountType, $storeId)
    {
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($advocateSummary, $amount, $amountType, $storeId))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($advocateSummary))
            ->setRecipientEmail($this->getRecipientEmail($advocateSummary));

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    abstract protected function getTemplateId($storeId);

    /**
     * Retrieve recipient name
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @return string
     */
    protected function getRecipientName($advocateSummary)
    {
        return $advocateSummary->getCustomerName();
    }

    /**
     * Retrieve recipient email
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @return string
     */
    protected function getRecipientEmail($advocateSummary)
    {
        return $advocateSummary->getCustomerEmail();
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderName($storeId)
    {
        return $this->config->getEmailSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderEmail($storeId)
    {
        return $this->config->getEmailSenderEmail($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    protected function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param AdvocateSummaryInterface $advocateSummary
     * @param float $amount
     * @param string $amountType
     * @param int $storeId
     * @return array
     */
    protected function prepareTemplateVariables($advocateSummary, $amount, $amountType, $storeId)
    {
        $templateVariables = [
            BaseAmountVariables::ADVOCATE_SUMMARY => $advocateSummary,
            BaseAmountVariables::AMOUNT => $amount,
            BaseAmountVariables::AMOUNT_TYPE => $amountType,
            BaseAmountVariables::STORE => $this->storeManager->getStore($storeId)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
