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
 * @package   mirasvit/module-email
 * @version   2.1.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Campaign\View\Email;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Mirasvit\Email\Api\Data\ChainInterface;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;

class Modifier implements ModifierInterface
{
    private $queueRepository;

    public function __construct(
        QueueRepositoryInterface $queueRepository
    ) {
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyData(array $data)
    {
        $data['report']['pending'] = $this->countPendingEmails($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function countPendingEmails($data)
    {
        $queues = $this->queueRepository->getCollection();
        $queues->addFieldToFilter(ChainInterface::ID, $data[ChainInterface::ID])
            ->addFieldToFilter(QueueInterface::STATUS, QueueInterface::STATUS_PENDING);

        return $queues->count();
    }
}
