<?php
namespace Dividebuy\Payment\Model;

use Dividebuy\RetailerConfig\Helper\Data as RetailerConfigHelper;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class DividebuyConfigProvider
 */
class DividebuyConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Dividebuy\RetailerConfig\Helper\Data
     */
    protected $_retailerConfigHelper;

    /**
     * @param RetailerConfigHelper $retailerConfigHelper
     */
    public function __construct(RetailerConfigHelper $retailerConfigHelper)
    {
        $this->_retailerConfigHelper = $retailerConfigHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'dividebuy' => [
                    'imageSrc' => $this->getImageSrc(),
                ],
            ],
        ];

        return $config;
    }

    /**
     * Used to get checkout banner URL
     *
     * @return string
     */
    public function getImageSrc()
    {
        return $this->_retailerConfigHelper->getCheckoutBannerUrl();
    }

}
