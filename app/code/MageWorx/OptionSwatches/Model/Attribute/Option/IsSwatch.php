<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionSwatches\Model\Attribute\Option;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Model\AttributeInterface;
use MageWorx\OptionSwatches\Helper\Data as Helper;

class IsSwatch implements AttributeInterface
{
    const KEY_IS_SWATCH = 'is_swatch';

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Helper::KEY_IS_SWATCH;
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
        $this->entity = $entity;

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
