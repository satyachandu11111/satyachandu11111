<?php


namespace Mageplaza\Webhook\Factory;


use Magento\Sales\Api\Data\OrderAddressInterface;
use Mageplaza\Webhook\Adapter\Address as SpbAddress;

class AddressFactory
{

    public function createFromAddress(OrderAddressInterface $address): SpbAddress
    {
        $spbAddress = new SpbAddress();
        $spbAddress->setFirstName($address->getFirstname());
        $spbAddress->setLastName($address->getLastname());
        $spbAddress->setStreet($address->getStreet()[0]);
        $spbAddress->setCity($address->getCity());
        $spbAddress->setProvinceCode($address->getRegionCode());
        $spbAddress->setCountryCode($address->getCountryId());
        $spbAddress->setPostcode($address->getPostcode());
        $spbAddress->setPhoneNumber($address->getTelephone());
        return $spbAddress;
    }

}
