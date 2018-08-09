<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Api;

/**
 * @api
 */
interface QuestionRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Faq\Api\Data\QuestionInterface $question
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     */
    public function save(\Amasty\Faq\Api\Data\QuestionInterface $question);

    /**
     * Get by id
     *
     * @param int $questionId
     * @return \Amasty\Faq\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($questionId);

    /**
     * Delete
     *
     * @param \Amasty\Faq\Api\Data\QuestionInterface $question
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Faq\Api\Data\QuestionInterface $question);

    /**
     * Delete by id
     *
     * @param int $questionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($questionId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
