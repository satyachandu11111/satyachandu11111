<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionVisibility\Model\Attribute\OptionValue;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionVisibility\Helper\Data as Helper;
use MageWorx\OptionBase\Api\AttributeInterface;
use MageWorx\OptionBase\Model\Product\Option\AbstractAttribute;

class Disabled extends AbstractAttribute implements AttributeInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param ResourceConnection $resource
     * @param Helper $helper
     */
    public function __construct(
        ResourceConnection $resource,
        Helper $helper
    ) {
        $this->helper = $helper;
        parent::__construct($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Helper::KEY_DISABLED;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataForFrontend($object)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function importTemplateMageOne($data)
    {
        return 0;
    }
}
