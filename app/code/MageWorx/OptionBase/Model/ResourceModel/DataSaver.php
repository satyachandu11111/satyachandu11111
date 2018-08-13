<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface as Connection;

class DataSaver
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    /**
     * Delete option/option value data by certain condition
     *
     * @param string $tableName
     * @param string $condition
     * @return void
     */
    public function deleteData($tableName, $condition)
    {
        $this->connection->delete($this->resource->getTableName($tableName), $condition);
    }

    /**
     * Insert multiple option/option value data
     *
     * @param string $tableName
     * @param array $data
     * @return void
     */
    public function insertMultipleData($tableName, $data)
    {
        $this->connection->insertMultiple($this->resource->getTableName($tableName), $data);
    }
}
