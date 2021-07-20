<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\Friend\Referral\CookieManagement;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;

/**
 * Class CookieMetadataResolver
 *
 * @package Aheadworks\Raf\Model\Friend\Referral\CookieManagement
 */
class CookieMetadataResolver
{
    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var ConfigInterface
     */
    private $sessionConfig;

    /**
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ConfigInterface $sessionConfig
     */
    public function __construct(
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigInterface $sessionConfig
    ) {
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionConfig = $sessionConfig;
    }

    /**
     * Resolve metadata
     *
     * @param PublicCookieMetadata|null $metadata
     * @return PublicCookieMetadata
     */
    public function resolve($metadata)
    {
        return !empty($metadata) || ($metadata instanceof PublicCookieMetadata) ? $metadata : $this->getMetadata();
    }

    /**
     * Retrieve cookie metadata
     *
     * @return PublicCookieMetadata
     */
    private function getMetadata()
    {
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();

        $cookieMetadata
            ->setDomain($this->sessionConfig->getCookieDomain())
            ->setPath($this->sessionConfig->getCookiePath())
            ->setDurationOneYear();

        return $cookieMetadata;
    }
}
