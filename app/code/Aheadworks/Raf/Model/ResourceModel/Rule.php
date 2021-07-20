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
namespace Aheadworks\Raf\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;

/**
 * Class Rule
 * @package Aheadworks\Raf\Model\ResourceModel
 */
class Rule extends AbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_raf_rule', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function save(MagentoFrameworkAbstractModel $object)
    {
        $object->beforeSave();
        return parent::save($object);
    }
}
