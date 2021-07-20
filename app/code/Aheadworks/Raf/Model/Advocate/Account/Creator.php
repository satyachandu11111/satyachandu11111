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
namespace Aheadworks\Raf\Model\Advocate\Account;

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterfaceFactory;
use Aheadworks\Raf\Model\Advocate\Generator\ReferralLink as ReferralLinkGenerator;

/**
 * Class Creator
 *
 * @package Aheadworks\Raf\Model\Advocate\Account
 */
class Creator
{
    /**
     * @var AdvocateSummaryInterfaceFactory
     */
    private $advocateSummaryDataFactory;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var ReferralLinkGenerator
     */
    private $referralLinkGenerator;

    /**
     * @param AdvocateSummaryInterfaceFactory $advocateSummaryDataFactory
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param ReferralLinkGenerator $referralLinkGenerator
     */
    public function __construct(
        AdvocateSummaryInterfaceFactory $advocateSummaryDataFactory,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        ReferralLinkGenerator $referralLinkGenerator
    ) {
        $this->advocateSummaryDataFactory = $advocateSummaryDataFactory;
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->referralLinkGenerator = $referralLinkGenerator;
    }

    /**
     * Create advocate
     *
     * @param int $customerId
     * @param int $websiteId
     * @return AdvocateSummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAdvocate($customerId, $websiteId)
    {
        /** @var AdvocateSummaryInterface $advocate */
        $advocate = $this->advocateSummaryDataFactory->create();
        $advocate
            ->setCustomerId($customerId)
            ->setWebsiteId($websiteId)
            ->setReferralLink($this->referralLinkGenerator->generate());

        $this->advocateSummaryRepository->save($advocate);

        return $advocate;
    }
}
