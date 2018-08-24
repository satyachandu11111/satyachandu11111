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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.48
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Service;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\SearchAutocomplete\Api\Repository\IndexRepositoryInterface;
use Mirasvit\SearchAutocomplete\Model\Config;
use Magento\Search\Helper\Data as SearchHelper;

class JsonConfigService
{
    private $fs;

    private $scopeConfig;

    private $config;

    private $indexRepository;

    private $searchHelper;

    public function __construct(
        Filesystem $fs,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        IndexRepositoryInterface $indexRepository,
        SearchHelper $searchHelper
    ) {
        $this->fs = $fs;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->indexRepository = $indexRepository;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @return $this
     */
    public function ensure()
    {
        $path = $this->fs->getDirectoryRead(DirectoryList::CONFIG)->getAbsolutePath();
        $filePath = $path . 'autocomplete.json';

        if (!$this->config->isFastMode()) {
            @unlink($filePath);

            return $this;
        }

        $config = $this->generate();

        @file_put_contents($filePath, \Zend_Json::encode($config));

        return $this;
    }

    /**
     * @return array
     */
    public function generate()
    {
        $config = [
            'engine'                    => $this->scopeConfig->getValue('search/engine/engine'),
            'is_optimize_mobile'        => $this->config->isOptimizeMobile(),
            'is_show_cart_button'       => $this->config->isShowCartButton(),
            'is_show_image'             => $this->config->isShowImage(),
            'is_show_price'             => $this->config->isShowPrice(),
            'is_show_rating'            => $this->config->isShowRating(),
            'is_show_sku'               => $this->config->isShowSku(),
            'is_show_short_description' => $this->config->isShowShortDescription(),
            'textAll'                   => __('Show all %1 results →', "%s")->render(),
            'textEmpty'                 => __('Sorry, nothing found for "%1".', "%s")->render(),
            'urlAll'                    => $this->searchHelper->getResultUrl(""),
        ];

        foreach ($this->indexRepository->getIndices() as $index) {
            $identifier = $index->getIdentifier();

            if (!$this->config->getIndexOptionValue($identifier, 'is_active')) {
                continue;
            }

            if ($identifier == 'magento_catalog_categoryproduct' || $identifier == 'magento_search_query') {
                continue;
            }

            $index->addData($this->config->getIndexOptions($identifier));

            $config['indexes'][$identifier] = [
                'title' => __($index->getTitle())->render(),
                'order' => $index->getOrder(),
                'limit' => $index->getLimit(),
            ];
        }

        return $config;
    }
}