<?php

namespace Mirasvit\Feed\Api\Service;


use Mirasvit\Feed\Model\Feed;

interface SchemaValidationInterface
{
    const CSV = 'csv';
    const XML = 'xml';

    /**
     * Return validation result.
     *
     * @return array
     */
    public function validateSchema();

    /**
     * Initialize schema validation service.
     *
     * @param Feed $feed
     *
     * @return $this
     */
    public function init(Feed $feed);

    /**
     * Get number of invalid entities in the schema.
     *
     * @return int
     */
    public function getInvalidEntityQty();
}
