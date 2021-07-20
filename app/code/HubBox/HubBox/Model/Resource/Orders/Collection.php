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

namespace HubBox\HubBox\Model\Resource\Orders;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    /**
     *
     * @param $data
     * @param $id
     * @return number
     */
    public function saveData($data, $id)
    {

        return $this->getConnection()->update($this->getTable('hubbox_sales_order'), $data, '`id`=' . (int)$id);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_idFieldName = 'id';
        $this->_init(
            'HubBox\HubBox\Model\Orders', 'HubBox\HubBox\Model\Resource\Orders'
        );
    }
}
