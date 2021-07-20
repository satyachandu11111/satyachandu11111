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

use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;
use Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor\VariableProcessorInterface;
use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\BaseAmountVariables;

/**
 * Class ExpiredInDays
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\Amount\VariableProcessor
 */
class ExpiredInDays implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var AdvocateSummaryInterface $advocateSummary */
        $advocateSummary = $variables[BaseAmountVariables::ADVOCATE_SUMMARY];

        $today = new \DateTime('today', new \DateTimeZone('UTC'));
        $expiredDate = new \DateTime($advocateSummary->getExpirationDate(), new \DateTimeZone('UTC'));
        $expiredInDays = $today->diff($expiredDate);
        $expiredInDays = (int)$expiredInDays->format('%a');

        if ($expiredInDays > 0) {
            $variables[BaseAmountVariables::EXPIRED_IN_DAYS] = $expiredInDays;
        }

        return $variables;
    }
}
