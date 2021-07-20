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
 * Transaction CRUD interface
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Save transaction data
     *
     * @param \Aheadworks\Raf\Api\Data\TransactionInterface $transaction
     * @return \Aheadworks\Raf\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Raf\Api\Data\TransactionInterface $transaction);

    /**
     * Retrieve transaction data by id
     *
     * @param  int $transactionId
     * @return \Aheadworks\Raf\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($transactionId);

    /**
     * Retrieve transactions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Raf\Api\Data\TransactionSearchResultsInterface;
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
