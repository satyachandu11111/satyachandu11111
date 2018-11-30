<?php

namespace Mirasvit\Feed\Model;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\Feed\Api\Data\ValidationInterface;

class Validation extends AbstractModel implements ValidationInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'feed_validation';

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Validation::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getValidator()
    {
        return $this->getData(self::VALIDATOR);
    }
}
