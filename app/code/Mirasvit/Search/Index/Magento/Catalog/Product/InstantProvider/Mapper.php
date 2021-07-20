<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.33
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Index\Magento\Catalog\Product\InstantProvider;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\LayoutInterface;
use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\View\Design;
use Mirasvit\SearchAutocomplete\InstantProvider\EmulatorService;
use Mirasvit\SearchAutocomplete\Model\ConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mapper
{
    private $config;

    private $storeManager;

    private $imageHelper;

    private $design;

    private $layout;

    /**
     * @var \Magento\Framework\Pricing\Render
     */
    private $priceRender;

    private $reviewRenderer;

    private $productBlock;

    private $emulatorService;

    public function __construct(
        ConfigProvider $config,
        StoreManagerInterface $storeManager,
        ImageHelper $imageHelper,
        Design $design,
        LayoutInterface $layout,
        ReviewRenderer $reviewRenderer,
        EmulatorService $emulatorService
    ) {
        $this->config          = $config;
        $this->storeManager    = $storeManager;
        $this->imageHelper     = $imageHelper;
        $this->design          = $design;
        $this->layout          = $layout;
        $this->reviewRenderer  = $reviewRenderer;
        $this->emulatorService = $emulatorService;
    }

    public function getProductName(Product $product): string
    {
        return $this->clearString((string)$product->getName());
    }

    public function getProductSku(Product $product): string
    {
        if (!$this->config->isShowSku()) {
            return '';
        }

        return $this->clearString((string)$product->getSku());
    }

    public function getProductUrl(Product $product, int $storeId): string
    {
        $url     = $product->setStore($storeId)->getProductUrl();
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();

        if (strripos($url, $baseUrl) === false || strripos($url, $this->getAdminPath()) !== false) {
            $url = $baseUrl . $product->getUrlKey() . $this->config->getProductUrlSuffix($storeId);
        }

        $p = strpos($url, "?");
        if ($p !== false) { //remove GET params (sometimes they are present)
            $url = substr($url, 0, $p);
        }

        return $url;
    }

    public function getDescription(Product $product): string
    {
        if (!$this->config->isShowShortDescription()) {
            return '';
        }

        $result = $product->getDataUsingMethod('description');
        if (!$result) {
            $result = $product->getDataUsingMethod('short_description');
        }

        return $this->clearString((string)$result);
    }

    public function getProductImage(Product $product, int $storeId): string
    {
        if (!$this->config->isShowImage()) {
            return '';
        }

        $image = $this->imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getImage())
            ->resize(65 * 2, 80 * 2)
            ->getUrl();

        if (!$image || strpos($image, '/.') !== false) {
            $emulation = ObjectManager::getInstance()->get(\Magento\Store\Model\App\Emulation::class);

            try {
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);
                $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
            } catch (\Exception $e) {
                $this->design->setDesignTheme('Magento/backend', 'adminhtml');
                $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
            } finally {
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $image;
    }

    public function getPrice(Product $product, int $storeId): string
    {
        if (!$this->config->isShowPrice()) {
            return '';
        }

        $priceRenderer = $this->getPriceRenderer();
        $price         = '';
        if ($priceRenderer) {

            $emulation = ObjectManager::getInstance()->get(\Magento\Store\Model\App\Emulation::class);
            try {
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);
                $price = $priceRenderer->render(
                    FinalPrice::PRICE_CODE,
                    $product,
                    [
                        'include_container'     => true,
                        'display_minimal_price' => true,
                        'zone'                  => Render::ZONE_ITEM_LIST,
                        'list_category_page'    => true,
                    ]
                );
            } catch (\Exception $e) {
                $state = ObjectManager::getInstance()->get(\Magento\Framework\App\State::class);
                $state->emulateAreaCode(
                    'frontend',
                    function (&$price, $product, $priceRenderer, $storeId) {
                        $price = $priceRenderer->render(
                            FinalPrice::PRICE_CODE,
                            $product,
                            [
                                'include_container'     => true,
                                'display_minimal_price' => true,
                                'zone'                  => Render::ZONE_ITEM_LIST,
                                'list_category_page'    => true,
                            ]
                        );
                    },
                    [&$price, $product, $priceRenderer, $storeId]
                );
            } finally {
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $price;
    }

    public function getRating(Product $product, int $storeId, array $reviews): string
    {
        if (!$this->config->isShowRating()) {
            return '';
        }

        $rating = '';
        if (array_key_exists($product->getId(), $reviews)) {
            /** @var \Magento\Review\Model\Review\Summary $summary */
            $summary = $reviews[$product->getId()];

            $product->setData('reviews_count', $summary)
                ->setData('rating_summary', $summary);
            if (!is_string($product->getRatingSummary())) {
                $product->setData('reviews_count', $summary->getReviewsCount())
                    ->setData('rating_summary', $summary->getRatingSummary());
            }

            $emulation = ObjectManager::getInstance()->get(\Magento\Store\Model\App\Emulation::class);

            try {
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);
                $rating = $this->reviewRenderer->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
            } catch (\Exception $e) {
                $state = ObjectManager::getInstance()->get(\Magento\Framework\App\State::class);
                $state->emulateAreaCode(
                    'frontend',
                    function (&$rating, $product, $storeId) {
                        $rating = $this->reviewRenderer->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
                    },
                    [&$rating, $product, $storeId]
                );
            } finally {
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $rating;
    }

    public function getCart(Product $product, int $storeId): array
    {
        if ($this->productBlock === null) {
            $this->productBlock = ObjectManager::getInstance()
                ->create(\Magento\Catalog\Block\Product\ListProduct::class);
        }

        $cart = [
            'visible' => $this->config->isShowCartButton(),
            'label'   => $this->emulatorService->getStoreText('Add to Cart', $storeId),
        ];

        $params = $this->productBlock->getAddToCartPostParams($product);

        $baseUrl = parse_url($this->storeManager->getStore($storeId)->getBaseUrl())['host'];

        $adminUrl = $this->getAdminUrl();
        if (strripos($params['action'], $adminUrl) !== false) {
            $params['action'] = str_ireplace($adminUrl, $baseUrl, $params['action']);
        }

        $adminPath = $this->getAdminPath();
        if (strripos($params['action'], $adminPath) !== false) {
            $params['action'] = str_ireplace($adminPath, '', $params['action']);
        }

        $cart['params'] = $params;

        return $cart;
    }

    private function clearString(string $string): string
    {
        return html_entity_decode(strip_tags($string));
    }

    private function getAdminPath(): string
    {
        $url = ObjectManager::getInstance()->get(\Magento\Backend\Helper\Data::class)
            ->getHomePageUrl();

        $components = parse_url($url);
        $components = explode('/', $components['path']);

        return end($components);
    }

    private function getPriceRenderer(): \Magento\Framework\Pricing\Render
    {
        if ($this->priceRender) {
            return $this->priceRender;
        }

        $this->priceRender = $this->layout->getBlock('product.price.render.default');

        if ($this->priceRender) {
            $this->priceRender->setData('is_product_list', true);
        } else {
            $this->priceRender = $this->layout->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        return $this->priceRender;
    }

    private function getAdminUrl(): string
    {
        $url = ObjectManager::getInstance()->get(\Magento\Backend\Helper\Data::class)
            ->getHomePageUrl();

        $components = parse_url($url);

        return $components['host'] . '/' . $this->getAdminPath();
    }
}
