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
namespace Aheadworks\Raf\Model\Friend;

use Aheadworks\Raf\Api\Data\FriendMetadataInterface;
use Aheadworks\Raf\Model\ResourceModel\Friend\Order as FriendOrderResource;

/**
 * Class Checker
 *
 * @package Aheadworks\Raf\Model\Friend
 */
class Checker
{
    /**
     * @var FriendOrderResource
     */
    private $friendOrderResource;

    /**
     * @param FriendOrderResource $friendOrderResource
     */
    public function __construct(FriendOrderResource $friendOrderResource)
    {
        $this->friendOrderResource = $friendOrderResource;
    }

    /**
     * Check if can apply discount
     *
     * @param FriendMetadataInterface $friendMetadata
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function canApplyDiscount($friendMetadata)
    {
        $numberOrders = $this->friendOrderResource->getNumberOfOrders($friendMetadata);

        return $numberOrders == 0;
    }
}
