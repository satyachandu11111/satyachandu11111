<?php

namespace Mirasvit\Feed\Block\Adminhtml\Feed\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\Feed\Exporter;

/**
 * Status Grid Renderer
 */
class Status extends AbstractRenderer
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * {@inheritdoc}
     * @param Context  $context
     * @param Exporter $exporter
     */
    public function __construct(
        Context $context,
        Exporter $exporter
    ) {
        $this->exporter = $exporter;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function render(DataObject $feed)
    {
        /** @var \Mirasvit\Feed\Model\Feed $feed */
        $html = '';

        $handler = $this->exporter->getHandler($feed);
        if ($feed->getIsActive()) {
            if (in_array($handler->getStatus(), [Config::STATUS_COMPLETED, Config::STATUS_READY])) {
                if ($feed->getUrl()) {
                    $html = $this->getStatusHtml('notice', 'Ready');
                } else {
                    $html = $this->getStatusHtml('critical', 'Not generated');
                }
            } elseif ($handler->getStatus() == Config::STATUS_PROCESSING) {
                $html = $this->getStatusHtml('major', 'Processing', '');
            }
        } else {
            $html = $this->getStatusHtml('minor', 'Disabled');
        }

        return $html;
    }

    /**
     * Return status label (html)
     *
     * @param string $severity
     * @param string $title
     * @param string $message
     * @return string
     */
    protected function getStatusHtml($severity, $title, $message = null)
    {
        $html = '';
        $html .= sprintf('<span class="grid-severity-%s"><span>%s</span></span>', $severity, __($title));

        if ($message) {
            $html .= '<div class="state-message">' . $message . '</div>';
        }

        return $html;
    }
}
