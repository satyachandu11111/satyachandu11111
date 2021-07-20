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

namespace HubBox\HubBox\Command;

use HubBox\HubBox\Cron\SyncOrder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncOrders extends Command
{
    protected $_syncOrder;
    protected $_objectManager;
    protected $_state;

    public function __construct(ObjectManagerInterface $objectManager, State $state)
    {
        $this->_objectManager = $objectManager;
        $this->_state = $state;

        parent::__construct('hubbox:syncorders');
    }

    protected function configure()
    {
        $this->setName('hubbox:syncorders');
        $this->setDescription('Sync orders.');
        parent::configure('hubbox:syncorders');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {
            $this->_state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $this->_syncOrder = $this->_objectManager->create(SyncOrder::class);
        $this->_syncOrder->execute();
        $output->writeln('Orders synced');
    }
}
