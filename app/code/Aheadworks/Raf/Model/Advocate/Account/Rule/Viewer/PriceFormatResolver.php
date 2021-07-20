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

use Aheadworks\Raf\Model\Advocate\PriceFormatter;
use Aheadworks\Raf\Model\Source\Rule\BaseOffType;

/**
 * Class Viewer
 * @package Aheadworks\Raf\Model\Advocate\Account\Rule
 */
class PriceFormatResolver
{
    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @param PriceFormatter $priceFormatter
     */
    public function __construct(
        PriceFormatter $priceFormatter
    ) {
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * Resolve price and format
     *
     * @param float $price
     * @param string $type
     * @param int $storeId
     * @return string
     */
    public function resolve($price, $type, $storeId)
    {
        if ($type == BaseOffType::FIXED) {
            return $this->priceFormatter->getFormattedFixedPriceByStore($price, $storeId);
        }
        return $this->priceFormatter->getFormattedPercentPrice($price);
    }
}
