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
namespace Aheadworks\Raf\Api;

/**
 * Interface FriendManagementInterface
 * @api
 */
interface FriendManagementInterface
{
    /**
     * Check if can apply discount
     *
     * @param \Aheadworks\Raf\Api\Data\FriendMetadataInterface $friendMetadata
     * @return bool
     */
    public function canApplyDiscount($friendMetadata);
}
