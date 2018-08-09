<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel;

use Amasty\Faq\Api\Data\TagInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\Faq\Setup\Operation;

class Tag extends AbstractDb
{
    public function _construct()
    {
        $this->_init(Operation\CreateTagTable::TABLE_NAME, TagInterface::TAG_ID);
    }

    /**
     * @param \Amasty\Faq\Api\Data\TagInterface[] $tags
     *
     * @return int[]
     */
    public function saveNoExistTags($tags)
    {
        $tagIds = [];
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (!$tag->getTagId()) {
                    $this->save($tag);
                }
                $tagIds[] = $tag->getTagId();
            }
        }

        return $tagIds;
    }
}
