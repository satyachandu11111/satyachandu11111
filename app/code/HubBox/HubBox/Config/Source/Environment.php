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

namespace HubBox\HubBox\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{

    public function toOptionArray()
    {
        return [
            [ 'value' => 'https://sandbox.api.hub-box.com/v1/', 'label' => __('Sandbox') ],
            [ 'value' => 'https://api.hub-box.com/v1/', 'label' => __('Production') ]
        ];
    }
}
