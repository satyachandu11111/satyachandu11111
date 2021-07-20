<?php

namespace HubBox\HubBox\Model;

interface CollectableInterface {
    /**
     * Implement your own business logic to toggle HubBox
     *
     * For examples and implementation advice contact HubBox via email tech@hub-box.com, irc (freenode #magento)
     * or phone
     *
     * @return boolean
     */
    public function isCollectable();
}