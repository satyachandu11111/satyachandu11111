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
namespace Aheadworks\Raf\Model\Advocate\Share;

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer\PriceFormatResolver;
use Aheadworks\Raf\Api\RuleManagementInterface;
use Aheadworks\Raf\Api\Data\RuleInterface;

/**
 * Class MessageConfig
 * @package Aheadworks\Raf\Model\Advocate\Share
 */
class MessageConfig
{
    /**
     * @var PriceFormatResolver
     */
    private $priceFormatResolver;

    /**
     * @var RuleManagementInterface
     */
    private $ruleManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param PriceFormatResolver $priceFormatResolver
     * @param RuleManagementInterface $ruleManagement
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PriceFormatResolver $priceFormatResolver,
        RuleManagementInterface $ruleManagement
    ) {
        $this->storeManager = $storeManager;
        $this->priceFormatResolver = $priceFormatResolver;
        $this->ruleManagement = $ruleManagement;
    }

    /**
     * Retrieve message configuration data
     *
     * @return array
     */
    public function getConfigData()
    {
        $preparedData = [];
        $store = $this->storeManager->getStore();
        if ($rule = $this->ruleManagement->getActiveRule($store->getWebsiteId())) {
            $preparedData = [
                'activeRuleData' => $this->prepareActiveRuleData($rule, $store->getId()),
                'storeName' => $store->getName()
            ];
        }
        return $preparedData;
    }

    /**
     * Prepare active rule data
     *
     * @param RuleInterface $rule
     * @param int $storeId
     * @return array
     */
    private function prepareActiveRuleData($rule, $storeId)
    {
        $activeRuleData = [];
        $activeRuleData['registration_required'] = $rule->isRegistrationRequired();
        $activeRuleData['friend_off'] = $this->priceFormatResolver->resolve(
            $rule->getFriendOff(),
            $rule->getFriendOffType(),
            $storeId
        );

        return $activeRuleData;
    }
}
