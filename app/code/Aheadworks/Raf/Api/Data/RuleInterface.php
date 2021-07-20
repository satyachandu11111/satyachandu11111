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
namespace Aheadworks\Raf\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RuleInterface
 * @api
 */
interface RuleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const IS_REGISTRATION_REQUIRED = 'registration_required';
    const FRIEND_OFF = 'friend_off';
    const FRIEND_OFF_TYPE = 'friend_off_type';
    const ADVOCATE_OFF = 'advocate_off';
    const ADVOCATE_OFF_TYPE = 'advocate_off_type';
    const ADVOCATE_EARN_TYPE = 'advocate_earn_type';
    const IS_APPLY_TO_SHIPPING = 'apply_to_shipping';
    const WEBSITE_IDS = 'website_ids';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Is registration required
     *
     * @return bool
     */
    public function isRegistrationRequired();

    /**
     * Set is registration required
     *
     * @param bool $isRegistrationActive
     * @return $this
     */
    public function setIsRegistrationRequired($isRegistrationActive);

    /**
     * Get friend's off
     *
     * @return float
     */
    public function getFriendOff();

    /**
     * Set friend's off
     *
     * @param float $friendOff
     * @return $this
     */
    public function setFriendOff($friendOff);

    /**
     * Get friend's off type
     *
     * @return string
     */
    public function getFriendOffType();

    /**
     * Set friend's off type
     *
     * @param string $friendOffType
     * @return $this
     */
    public function setFriendOffType($friendOffType);

    /**
     * Get advocate's off
     *
     * @return float
     */
    public function getAdvocateOff();

    /**
     * Set advocate's off
     *
     * @param float $advocateOff
     * @return $this
     */
    public function setAdvocateOff($advocateOff);

    /**
     * Get advocate's off type
     *
     * @return string
     */
    public function getAdvocateOffType();

    /**
     * Set advocate's off type
     *
     * @param string $advocateOffType
     * @return $this
     */
    public function setAdvocateOffType($advocateOffType);

    /**
     * Get advocate's earn type
     *
     * @return string
     */
    public function getAdvocateEarnType();

    /**
     * Set advocate's earn type
     *
     * @param string $advocateEarnType
     * @return $this
     */
    public function setAdvocateEarnType($advocateEarnType);

    /**
     * Is apply to shipping
     *
     * @return bool
     */
    public function isApplyToShipping();

    /**
     * Set is apply to shipping
     *
     * @param bool $isApplyToShipping
     * @return $this
     */
    public function setIsApplyToShipping($isApplyToShipping);

    /**
     * Get website IDs
     *
     * @return int[]
     */
    public function getWebsiteIds();

    /**
     * Set website IDs
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Raf\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Raf\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\RuleExtensionInterface $extensionAttributes
    );
}
