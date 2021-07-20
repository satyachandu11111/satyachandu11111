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
namespace Aheadworks\Raf\Model\Rule\Discount\Customer\Resolver;

use Aheadworks\Raf\Model\Metadata\Rule as RuleMetadata;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Rule
 *
 * @package Aheadworks\Raf\Model\Rule\Discount\Customer\Resolver
 */
abstract class AbstractRule
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Resolve rule
     *
     * @param Quote $quote
     * @param AddressInterface $address
     * @return RuleMetadata
     */
    public function resolve($quote, $address)
    {
        $ruleData = $this->prepareData($quote, $address);

        return $this->objectManager->create(RuleMetadata::class, ['data' => $ruleData]);
    }

    /**
     * Prepare rule data
     *
     * @param Quote $quote
     * @param AddressInterface $address
     * @return array
     */
    abstract protected function prepareData($quote, $address);
}
