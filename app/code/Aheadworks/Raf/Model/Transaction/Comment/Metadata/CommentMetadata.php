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
namespace Aheadworks\Raf\Model\Transaction\Comment\Metadata;

use Magento\Framework\DataObject;

/**
 * Class CommentMetadata
 *
 * @package Aheadworks\Raf\Model\Transaction\Comment\Metadata
 */
class CommentMetadata extends DataObject implements CommentMetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return $this->getData(self::PLACEHOLDER);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
}
