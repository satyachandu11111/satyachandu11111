<?php
/**
 * Copyright © Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 */

namespace Amazon\PayV2\Block\Adminhtml\System\Config\Form;

/**
 * Displays links to available custom logs
 */
class DeveloperLogs extends \Magento\Config\Block\System\Config\Form\Field
{
    const DOWNLOAD_PATH = 'amazon_payv2/payv2/downloadLog';

    const LOGS = [
        ['name' => 'IPN V2 Log', 'path' => \Amazon\PayV2\Logger\Handler\AsyncIpn::FILENAME, 'type' => 'async'],
        ['name' => 'Client V2 Log', 'path' => \Amazon\PayV2\Logger\Handler\Client::FILENAME, 'type' => 'client']
    ];

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $urlBuilder;

    /**
     * DeveloperLogs constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $directoryList
     * @param \Magento\Framework\App\Filesystem\DirectoryList $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryList = $directoryList;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/logs.phtml');
        }
        return $this;
    }

    /**
     * Render log list
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Renders string as an html element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Returns markup for developer log field.
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getLinks()
    {
        $links = $this->getLogFiles();

        if ($links) {
            $output = '';

            foreach ($links as $link) {
                $output .= '<a href="' . $link['link'] . '">' . $link['name'] . '</a><br />';
            }

            return $output;
        }
        return __('No logs are currently available.');
    }

    /**
     * Get list of available log files.
     *
     * @return array
     */
    private function getLogFiles()
    {
        $links = [];

        foreach (self::LOGS as $name => $data) {
            $links[] = ['link' => $this->urlBuilder->getUrl(self::DOWNLOAD_PATH, [
                'type' => $data['type']]),
                'name' => $data['name']];
        }

        return $links;
    }
}
