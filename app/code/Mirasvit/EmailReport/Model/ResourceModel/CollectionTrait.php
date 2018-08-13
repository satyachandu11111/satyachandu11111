<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-report
 * @version   2.0.2
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Model\ResourceModel;


use Mirasvit\Email\Api\Data\QueueInterface;

trait CollectionTrait
{
    /**
     * Add inner join to queue table in order to exclude statistic for removed emails.
     *
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(['queue' => $this->_resource->getTable(QueueInterface::TABLE_NAME)],
            'queue.'.QueueInterface::ID . ' = main_table.queue_id',
            []
        );

        return $this;
    }
}
