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
namespace Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer;

use Aheadworks\Raf\Api\RuleManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;

/**
 * Class ActiveRuleResolver
 * @package Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer
 */
class ActiveRuleResolver
{
    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RuleInterface;
     */
    private $activeRule;

    /**
     * @param RuleManagementInterface $ruleManagement
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RuleManagementInterface $ruleManagement,
        StoreManagerInterface $storeManager
    ) {;
        $this->ruleManagement = $ruleManagement;
        $this->storeManager = $storeManager;
    }

    /**
     * Get active rule for website
     *
     * @param int $storeId
     * @return \Aheadworks\Raf\Api\Data\RuleInterface|bool
     */
    public function getRule($storeId = null)
    {
        if (!$this->activeRule) {
            $store = $storeId
                ? $this->storeManager->getStore($storeId)
                : $this->storeManager->getStore();
            $websiteId = $store->getWebsiteId();
            $this->activeRule = $this->ruleManagement->getActiveRule($websiteId);
        }
        return $this->activeRule;
    }
}
