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
namespace Aheadworks\Raf\Block\Advocate\Info;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;

/**
 * Class Email
 * @package Aheadworks\Raf\Block\Advocate\Info
 */
class EmailForm extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Raf::advocate/info/email_form.phtml';

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateRepository;

    /**
     * ReferralUrl constructor.
     * @param Context $context
     * @param AdvocateSummaryRepositoryInterface $advocateRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        AdvocateSummaryRepositoryInterface $advocateRepository,
        array $data = []
    ) {
        $this->advocateRepository = $advocateRepository;
        parent::__construct($context, $data);
    }

    public function getNewRewardSubscriptionStatus()
    {
        /** @var \Aheadworks\Raf\Block\Advocate\Info $info */
        $info = $this->getParentBlock();
        list($customerId, $websiteId) = $info->getCustomerIdAndWebsiteId();
        try {
            $advocate = $this->advocateRepository->getByCustomerId($customerId, $websiteId);
        } catch (\Exception $e) {
            return false;
        }
        return (bool)$advocate->getNewRewardSubscriptionStatus();
    }
}
