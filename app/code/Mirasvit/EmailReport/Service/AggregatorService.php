<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-report
 * @version   2.0.2
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Service;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\EmailReport\Api\Service\AggregatorServiceInterface;

class AggregatorService implements AggregatorServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function count(AbstractCollection $collection, $field, $fieldId)
    {
        return $collection->addFieldToFilter($field, $fieldId)->count();
    }
}
