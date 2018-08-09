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
interface TagRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Faq\Api\Data\TagInterface $tag
     * @return \Amasty\Faq\Api\Data\TagInterface
     */
    public function save(\Amasty\Faq\Api\Data\TagInterface $tag);

    /**
     * Get by id
     *
     * @param int $tagId
     * @return \Amasty\Faq\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($tagId);

    /**
     * Delete
     *
     * @param \Amasty\Faq\Api\Data\TagInterface $tag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Faq\Api\Data\TagInterface $tag);

    /**
     * Delete by id
     *
     * @param int $tagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($tagId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
