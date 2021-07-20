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
namespace Aheadworks\Raf\Model\Renderer\Cms;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Block
 *
 * @package Aheadworks\Raf\Model\Renderer\Cms
 */
class Block
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var BlockRepositoryInterface
     */
    private $cmsBlockRepository;

    /**
     * @var FilterProvider
     */
    private $cmsFilterProvider;

    /**
     * @var array
     */
    private $blockHtml = [];

    /**
     * @param StoreManagerInterface $storeManager
     * @param BlockRepositoryInterface $cmsBlockRepository
     * @param FilterProvider $cmsFilterProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        BlockRepositoryInterface $cmsBlockRepository,
        FilterProvider $cmsFilterProvider
    ) {
        $this->storeManager = $storeManager;
        $this->cmsBlockRepository = $cmsBlockRepository;
        $this->cmsFilterProvider = $cmsFilterProvider;
    }

    /**
     * Retrieve html code by id
     *
     * @param int $blockId
     * @param int $storeId
     * @return string
     */
    public function render($blockId, $storeId)
    {
        $cacheKey = implode('-', [$blockId, $storeId]);
        if (!isset($this->blockHtml[$cacheKey])) {
            $this->blockHtml[$cacheKey] = $this->getBlockHtml($blockId, $storeId);
        }

        return $this->blockHtml[$cacheKey];
    }

    /**
     * Retrieve block html
     *
     * @param int $blockId
     * @param int $storeId
     * @return string
     */
    private function getBlockHtml($blockId, $storeId)
    {
        $blockHtml = '';
        try {
            $cmsBlock = $this->cmsBlockRepository->getById($blockId);
            if ($cmsBlock->isActive()) {
                $blockHtml = $this->cmsFilterProvider
                    ->getBlockFilter()
                    ->setStoreId($storeId)
                    ->filter($cmsBlock->getContent());
            }
        } catch (LocalizedException $e) {
        }

        return $blockHtml;
    }
}
