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



namespace Mirasvit\EmailReport\Api\Service;


interface StorageServiceInterface
{
    const QUEUE_PARAM_NAME = 'qid';

    /**
     * Save queue ID in customer's cookie.
     *
     * @param int $queueId
     *
     * @return mixed
     */
    public function persistQueueId($queueId);

    /**
     * Retrieve queue ID from customer's cookie.
     *
     * @return int
     */
    public function retrieveQueueId();
}