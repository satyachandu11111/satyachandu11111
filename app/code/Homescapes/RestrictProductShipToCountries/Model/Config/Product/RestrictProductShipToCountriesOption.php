<?php

namespace Homescapes\RestrictProductShipToCountries\Model\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class RestrictProductShipToCountriesOption extends AbstractSource
{
	protected $_country;

	public function __construct(
        \Magento\Directory\Model\Config\Source\Country $country
    ) {
        $this->_country = $country;
    }

    public function getAllOptions()
    {
    	$options = [];
        $finalCountries = [];
		$options = $this->_country->toOptionArray();
		
        foreach ($options as $key => $country) {
            if($country['value'] != '')
            {
                $finalCountries[] = ['label' => $country['label'], 'value' => $country['value']];
            }
        }
		return $finalCountries;
	}
}