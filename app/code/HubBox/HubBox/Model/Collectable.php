<?php
/**
 * A Magento 2 module named HubBox/HubBox
 * Copyright (C) 2017 HubBox Ltd
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

namespace HubBox\HubBox\Model;

use HubBox\HubBox\Helper\Data;
use \Magento\Checkout\Model\Session;
use Magento\Framework\Model\AbstractModel;

class Collectable extends AbstractModel implements CollectableInterface {

    protected $_checkoutSession;
    protected $_helper;

    public function __construct(
        Data $helper,
        Session $checkoutSession
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $helper;
    }

    public function isCollectable()
    {
        return true;
    }
}
