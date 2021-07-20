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
namespace Aheadworks\Raf\Model\Source\Cms;

use Magento\Framework\Option\ArrayInterface;
use Magento\Cms\Model\ResourceModel\Block\Collection as BlockCollection;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;

/**
 * Class Block
 *
 * @package Aheadworks\Raf\Model\Source\Cms
 */
class Block implements ArrayInterface
{
    /**
     * @var int
     */
    const DONT_DISPLAY = 0;

    /**
     * @var BlockCollection
     */
    private $blockCollection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param BlockCollectionFactory $blockCollectionFactory
     */
    public function __construct(
        BlockCollectionFactory $blockCollectionFactory
    ) {
        $this->blockCollection = $blockCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = array_merge(
                [self::DONT_DISPLAY => __('Don\'t Display')],
                $this->blockCollection->toOptionArray()
            );
        }

        return $this->options;
    }
}
