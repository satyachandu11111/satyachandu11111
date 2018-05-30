<?php

namespace Homescapes\Orderswatch\Ui\Component\Listing\Column;

class Countries extends \Magento\Ui\Component\Listing\Columns\Column {

    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
     */
    protected $countryInformation;

     /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
        array $components = [],
        array $data = []
    ){
        $this->countryInformation = $countryInformation;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $countryNames = [];
                $countryCodes = [];
                if(!empty($item[$fieldName])){
                    $countryCodes = explode(',', $item[$fieldName]);
                }
                foreach($countryCodes as $codes){
                    $country = $this->countryInformation->getCountryInfo($codes);
                    $countryNames[] = $country->getFullNameLocale();
                }
                $item[$fieldName] = implode(',', $countryNames);
            }
        }

        return $dataSource;
    }
}