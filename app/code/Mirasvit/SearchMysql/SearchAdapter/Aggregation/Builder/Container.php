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
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.33
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchMysql\SearchAdapter\Aggregation\Builder;

class Container
{
    private $buckets;

    public function __construct(
        array $buckets = []
    ) {
        $this->buckets = $buckets;
    }

    public function get(string $bucketType)
    {
        return $this->buckets[$bucketType];
    }
}
