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

namespace Aheadworks\Raf\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for rule search results
 * @api
 */
interface RuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rule list
     *
     * @return \Aheadworks\Raf\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set rule list
     *
     * @param \Aheadworks\Raf\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
