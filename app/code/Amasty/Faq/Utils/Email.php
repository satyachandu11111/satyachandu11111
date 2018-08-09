<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Utils;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Email
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Email constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder      $transportBuilder
     * @param LoggerInterface       $logger
     * @param ConfigProvider        $configProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * Send email helper
     * emailTo and sendFrom can be array with keys email and name.
     * Otherwise string with key to Store Email address.
     *
     * @param string|array $emailTo
     * @param string $templateConfigPath
     * @param array  $vars
     * @param string $area
     * @param string|array $sendFrom
     */
    public function sendEmail(
        $emailTo = '',
        $templateConfigPath = '',
        $vars = [],
        $area = \Magento\Framework\App\Area::AREA_FRONTEND,
        $sendFrom = ''
    ) {
        try {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore();
            $data =  array_merge(
                [
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(),
                ],
                $vars
            );

            if (empty($sendFrom)) {
                $sendFrom = 'general';
            }

            if (!is_array($emailTo)) {
                $emailTo = [
                    'email' => $this->configProvider->getValue('trans_email/ident_' . $emailTo . '/email'),
                    'name' => $this->configProvider->getValue('trans_email/ident_' . $emailTo . '/name')
                ];
            }

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->configProvider->getValue($templateConfigPath)
            )->setTemplateOptions(
                ['area' => $area, 'store' => $store->getId()]
            )->setTemplateVars(
                $data
            )->setFrom(
                $sendFrom
            )->addTo(
                $emailTo['email'],
                $emailTo['name']
            )->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
