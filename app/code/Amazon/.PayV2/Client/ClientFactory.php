<?php
/**
 * Copyright © Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\PayV2\Client;

use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerAwareInterface;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var \Amazon\PayV2\Model\AmazonConfig
     */
    private $amazonConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * ClientFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface        $logger
     * @param string $instanceName
     */
    public function __construct(
        \Amazon\PayV2\Model\AmazonConfig $amazonConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        $instanceName = '\\AmazonPayV2\\ClientInterface'
    ) {
        $this->amazonConfig  = $amazonConfig;
        $this->objectManager = $objectManager;
        $this->logger        = $logger;
        $this->instanceName  = $instanceName;
    }

    /**
     * {@inheritDoc}
     */
    public function create($scopeId = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $config = [
            'public_key_id' => $this->amazonConfig->getPublicKeyId($scope, $scopeId),
            'private_key'   => $this->amazonConfig->getPrivateKey($scope, $scopeId),
            'sandbox'       => $this->amazonConfig->isSandboxEnabled($scope, $scopeId),
            'region'        => $this->amazonConfig->getRegion($scope, $scopeId),
        ];

        $client = $this->objectManager->create($this->instanceName, ['amazonConfig' => $config]);

        if ($client instanceof LoggerAwareInterface && $this->amazonConfig->isLoggingEnabled($scope, $scopeId)) {
            $client->setLogger($this->logger);
        }

        return $client;
    }
}
