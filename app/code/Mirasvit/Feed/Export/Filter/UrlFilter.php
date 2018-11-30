<?php
namespace Mirasvit\Feed\Export\Filter;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\Store;

class UrlFilter
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * UrlFilter constructor.
     * @param StoreManagerInterface $storeManager
     * @param Filesystem            $filesystem
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Filesystem $filesystem
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * To secure URL
     *
     * Return secure url
     *
     * @param string $input
     * @return string
     */
    public function secure($input)
    {
        return str_replace('http://', 'https://', $input);
    }

    /**
     * To unsecure URL
     *
     * Return secure url
     *
     * @param string $input
     * @return string
     */
    public function unsecure($input)
    {
        return str_replace('https://', 'http://', $input);
    }

    /**
     * Return secure media url
     *
     * CDN
     *
     * @param string $input
     * @return string
     */
    public function mediaSecure($input)
    {
        return $this->replaceMediaUrl($input, true);
    }

    /**
     * Return unsecure media url
     *
     * CDN
     *
     * @param string $input
     * @return string
     */
    public function mediaUnsecure($input)
    {
        return $this->replaceMediaUrl($input, false);
    }

    /**
     * @param string $url
     * @param bool   $secure
     * @return string
     */
    protected function replaceMediaUrl($url, $secure)
    {
        $path = $secure ? Store::XML_PATH_SECURE_BASE_MEDIA_URL : Store::XML_PATH_UNSECURE_BASE_MEDIA_URL;

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $staticUrl = $store->getConfig($path);

        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, false)
            . $this->filesystem->getUri(DirectoryList::MEDIA) . '/';

        if (!$staticUrl) {
            return $url;
        } else {
            return str_replace($baseUrl, $staticUrl, $url);
        }
    }
}