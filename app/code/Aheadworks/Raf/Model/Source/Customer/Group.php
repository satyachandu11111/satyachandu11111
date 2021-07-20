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
namespace Aheadworks\Raf\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Convert\DataObject;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class Group
 * @package Aheadworks\Raf\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * Group constructor.
     * @param GroupManagementInterface $groupManagement
     * @param DataObject $objectConverter
     */
    public function __construct(
        GroupManagementInterface $groupManagement,
        DataObject $objectConverter
    ) {
        $this->groupManagement = $groupManagement;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $groups = $this->groupManagement->getLoggedInGroups();
        return $this->objectConverter->toOptionArray($groups, GroupInterface::ID, GroupInterface::CODE);
    }
}
