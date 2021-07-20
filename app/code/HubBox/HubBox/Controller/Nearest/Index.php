<?php
/**
 * HubBox Click and Collect
 * Copyright (C) 2017  2017
 *
 * This file is part of HubBox/HubBox.
 *
 * HubBox/HubBox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace HubBox\HubBox\Controller\Nearest;

use HubBox\HubBox\Api\CollectPoints;
use HubBox\HubBox\Logger\Logger;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{
    protected $_logger;
    protected $_collectPoints;
    protected $_request;
    protected $_resultJsonFactory;

    public function __construct(
        Context $context,
        CollectPoints $collectPoints,
        Logger $logger,
        JsonFactory $resultJsonFactory
	) {
		parent::__construct($context);
        $this->_collectPoints = $collectPoints;
        $this->_logger = $logger;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $query = $this->getRequest()->getParam('query');
        $result = $this->_resultJsonFactory->create();

        try {
            $response = $this->_collectPoints->nearest($query);
            $response = ['success' => true, 'data' => $response];
        } catch (\Exception $e) {
            $response = ['success' => false];
            $this->_logger->info('API: nearest request error: ' . print_r($e->getMessage(),true));
        }

        return $result->setData($response);
    }
}
