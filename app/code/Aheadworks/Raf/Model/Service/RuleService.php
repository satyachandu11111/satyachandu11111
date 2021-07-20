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
namespace Aheadworks\Raf\Model\Service;

use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\RuleManagementInterface;
use Aheadworks\Raf\Api\RuleRepositoryInterface;
use Aheadworks\Raf\Model\Source\Rule\Status as RuleStatus;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class RuleService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class RuleService implements RuleManagementInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param RuleRepositoryInterface $ruleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveRule($websiteId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(RuleInterface::STATUS, RuleStatus::ENABLED)
            ->addFilter(RuleInterface::WEBSITE_IDS, $websiteId);
        $rules = $this->ruleRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return empty($rules) ? false : reset($rules);
    }

    /**
     * {@inheritdoc}
     */
    public function getRule($websiteId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(RuleInterface::WEBSITE_IDS, $websiteId);
        $rules = $this->ruleRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return empty($rules) ? false : reset($rules);
    }
}
