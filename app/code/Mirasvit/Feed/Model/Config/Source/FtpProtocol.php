<?php

namespace Mirasvit\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class FtpProtocol implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('FTP / FTPS'),
                'value' => 'ftp',
            ],
            [
                'label' => __('SFTP'),
                'value' => 'sftp',
            ],
        ];
    }
}
