<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionSkuPolicy\Model\Attribute\Option;

use MageWorx\OptionBase\Model\AttributeInterface;
use MageWorx\OptionSkuPolicy\Helper\Data as Helper;

class SkuPolicy implements AttributeInterface
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Helper::KEY_SKU_POLICY;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOwnTable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName($type = '')
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOldData($data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function collectData($entity, $options)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($object)
    {
        return;
    }
}
