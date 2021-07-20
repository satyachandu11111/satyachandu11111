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

namespace HubBox\HubBox\Observer\System\Config;

use HubBox\HubBox\Helper\Data;
use HubBox\HubBox\Api\Auth;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;


class Changed implements ObserverInterface
{
    protected $_helper;
    protected $_auth;
    protected $_messageManager;

    public function __construct(
        Data $helper,
        Auth $auth,
        ManagerInterface $messageManager
    )
    {
        $this->_helper = $helper;
        $this->_auth = $auth;
        $this->_messageManager = $messageManager;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    )
    {
        // if the user just saved a username and key, lets attempt to get the
        // token so they don't have to run the command, warn them if the credentials are wrong
        if ($this->_helper->getHubBoxApiUsername() && $this->_helper->getHubBoxApiKey()) {
            if(!$this->_auth->refreshToken()) {
                $this->_messageManager->addErrorMessage('HubBox API credentials check failed, 
                please ensure you are using the credentials appropriate to the environment (sandbox / production)');
            } else {
                $this->_messageManager->addSuccessMessage('HubBox API credentials check was successful');
            }
        }

    }
}
