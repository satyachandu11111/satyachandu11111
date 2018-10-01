<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Scroll
 */


namespace Amasty\Scroll\Plugin;

use \Magento\Theme\Block\Html\Pager as NativePager;

class Pager
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->urlInterface = $urlInterface;
        $this->escaper = $escaper;
    }

    /**
     * @param NativePager $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(
        NativePager $subject,
        $result
    ) {
        $last = (int)$subject->getLastPageNum();
        $current = (int)$subject->getCurrentPage();

        $html = '';
        if ($current >= 2) {
            $prev = $current - 1;
            $url = $this->generateUrl($prev);
            $html .= '<link rel="prev" href="' . $url . '" />';
        }

        if ($current < $last) {
            $next = $current + 1;
            $url = $this->generateUrl($next);
            $html .= '<link rel="next" href="' . $url . '" />';
        }

        if ($html) {
            $result .= '<!--amasty_scroll_body' . $html . 'amasty_scroll_body-->';
        }

        $result .= '<div id="am-page-count" style="display: none">' . $last . '</div>';

        return  $result;
    }

    /**
     * @param int $page
     * @return string
     */
    private function generateUrl($page)
    {
        $currentUrl = $this->urlInterface->getCurrentUrl();
        $currentUrl = $this->escaper->escapeUrl($currentUrl);
        $result = preg_replace('/(\W)p=\d+/', '$1p=' . $page, $currentUrl, -1, $count);

        if (!$count) {
            $delimiter = (strpos($currentUrl, '?') === false) ? '?' : '&';
            $result .= $delimiter . 'p=' . $page;
        }

        return $result;
    }
}
