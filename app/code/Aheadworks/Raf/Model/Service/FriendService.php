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
namespace Aheadworks\Raf\Model\Service;

use Aheadworks\Raf\Api\FriendManagementInterface;
use Aheadworks\Raf\Model\Friend\Checker;

/**
 * Class FriendService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class FriendService implements FriendManagementInterface
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Checker $checker
     */
    public function __construct(
        Checker $checker
    ) {
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function canApplyDiscount($friendMetadata)
    {
        return $this->checker->canApplyDiscount($friendMetadata);
    }
}
