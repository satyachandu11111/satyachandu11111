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
namespace Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\VariableProcessor;

use Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer\PriceFormatResolver;
use Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor\VariableProcessorInterface;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\BaseAmountVariables;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Amount
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\VariableProcessor
 */
class Amount implements VariableProcessorInterface
{
    /**
     * @var PriceFormatResolver
     */
    private $priceFormatResolver;

    /**
     * @param PriceFormatResolver $priceFormatResolver
     */
    public function __construct(
        PriceFormatResolver $priceFormatResolver
    ) {
        $this->priceFormatResolver = $priceFormatResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        $amount = abs($variables[BaseAmountVariables::AMOUNT]);
        $amountType = $variables[BaseAmountVariables::AMOUNT_TYPE];
        /** @var StoreInterface $store */
        $store = $variables[BaseAmountVariables::STORE];

        $variables[BaseAmountVariables::AMOUNT_FORMATTED] = $this->priceFormatResolver->resolve(
            $amount,
            $amountType,
            $store->getId()
        );

        return $variables;
    }
}
