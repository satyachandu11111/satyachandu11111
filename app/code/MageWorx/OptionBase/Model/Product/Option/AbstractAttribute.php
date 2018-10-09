<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionBase\Model\Product\Option;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Model\AttributeInterface;
use MageWorx\OptionBase\Helper\Data as Helper;

abstract class AbstractAttribute implements AttributeInterface
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
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }

    /**
     * Check if attribute has own table in database
     *
     * @return bool
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
    public function clearData()
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
        return $object->getData($this->getName());
    }
}
