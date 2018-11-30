<?php

namespace Mirasvit\Feed\Api\Data;


interface ValidationInterface
{
    const TABLE_NAME = 'mst_feed_validation';

    const ID         = 'validation_id';
    const ENTITY_ID  = 'entity_id';
    const LINE_NUM   = 'line_num';
    const FEED_ID    = 'feed_id';
    const ATTRIBUTE  = 'attribute';
    const VALIDATOR  = 'validator';
    const VALUE      = 'value';

    /**
     * @return string
     */
    public function getValidator();
}
