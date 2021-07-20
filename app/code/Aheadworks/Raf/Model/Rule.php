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
namespace Aheadworks\Raf\Model;

use Aheadworks\Raf\Api\Data\RuleInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Raf\Model\ResourceModel\Rule as ResourceRule;
use Aheadworks\Raf\Model\Source\Rule\AdvocateEarnType;
use Aheadworks\Raf\Model\Source\Rule\AdvocateOffType;
use Aheadworks\Raf\Model\Rule\Validator\Composite as CompositeValidator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Rule
 * @package Aheadworks\Raf\Model
 */
class Rule extends AbstractModel implements RuleInterface
{
    /**
     * @var CompositeValidator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CompositeValidator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CompositeValidator $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceRule::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function isRegistrationRequired()
    {
        return $this->getData(self::IS_REGISTRATION_REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRegistrationRequired($isRegistrationRequired)
    {
        return $this->setData(self::IS_REGISTRATION_REQUIRED, $isRegistrationRequired);
    }

    /**
     * {@inheritdoc}
     */
    public function getFriendOff()
    {
        return $this->getData(self::FRIEND_OFF);
    }

    /**
     * {@inheritdoc}
     */
    public function setFriendOff($friendOff)
    {
        return $this->setData(self::FRIEND_OFF, $friendOff);
    }

    /**
     * {@inheritdoc}
     */
    public function getFriendOffType()
    {
        return $this->getData(self::FRIEND_OFF_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFriendOffType($friendOffType)
    {
        return $this->setData(self::FRIEND_OFF_TYPE, $friendOffType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvocateOff()
    {
        return $this->getData(self::ADVOCATE_OFF);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvocateOff($advocateOff)
    {
        return $this->setData(self::ADVOCATE_OFF, $advocateOff);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvocateOffType()
    {
        return $this->getData(self::ADVOCATE_OFF_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvocateOffType($advocateOffType)
    {
        return $this->setData(self::ADVOCATE_OFF_TYPE, $advocateOffType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvocateEarnType()
    {
        return $this->getData(self::ADVOCATE_EARN_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvocateEarnType($advocateEarnType)
    {
        return $this->setData(self::ADVOCATE_EARN_TYPE, $advocateEarnType);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplyToShipping()
    {
        return $this->getData(self::IS_APPLY_TO_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsApplyToShipping($isApplyToShipping)
    {
        return $this->setData(self::IS_APPLY_TO_SHIPPING, $isApplyToShipping);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteIds($websiteIds)
    {
        return $this->setData(self::WEBSITE_IDS, $websiteIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Raf\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        // @todo
        // It must be removed when we add 'multiple use' type for advocate earning
        // and 'percent' type for advocate off
        $this->setAdvocateEarnType(AdvocateEarnType::CUMULATIVE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
