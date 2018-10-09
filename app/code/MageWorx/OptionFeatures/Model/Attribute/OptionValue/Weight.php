<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionFeatures\Model\Attribute\OptionValue;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionBase\Model\AttributeInterface;

class Weight implements AttributeInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @param ResourceConnection $resource
     * @param Helper $helper
     */
    public function __construct(
        ResourceConnection $resource,
        Helper $helper
    ) {
        $this->resource = $resource;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Helper::KEY_WEIGHT;
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
        if (!$this->helper->isWeightEnabled()) {
            return;
        }

        $this->entity = $entity;

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareData($object)
    {
        return '';
    }
}
