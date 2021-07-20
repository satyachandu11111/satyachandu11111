<?php

namespace ThLassche\RegenerateRewrites\Console\Command;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\CanonicalUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\CurrentUrlRewritesRegenerator;
use Magento\Framework\App\State as AppState;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Class RegenerateUrls.php
 */
class RegenerateUrls extends Command {
	protected $storeManager;
	protected $urlPersist;
	protected $productUrlRewriteGenerator;
	protected $categoryCanonicalUrlRewriteGenerator;
	protected $categoryUrlRewriteGenerator;
	protected $productFactory;
	protected $categoryFactory;
	protected $appState;
	protected $objectManager;
    protected $progressFormat = '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%';
    protected $input;
    protected $output;
    protected $debug;
    protected $progressbar;

	const DEBUG_OPTION = 'debug';
	const CATEGORIES_OPTION = 'categories';
	const PRODUCTS_OPTION = 'products';
	const PRODUCT_OPTION = 'product';
	const INVISIBLE_OPTION = 'invisible';


	public function __construct(
		UrlPersistInterface $urlPersist,
        CanonicalUrlRewriteGenerator $categoryCanonicalUrlRewriteGenerator,
        CurrentUrlRewritesRegenerator $categoryUrlRewriteGenerator,
		ProductFactory $productFactory,
		CategoryFactory $categoryFactory,
		AppState $appState
	) {
		$this->appState = $appState;
		$this->urlPersist                               = $urlPersist;
		$this->categoryCanonicalUrlRewriteGenerator     = $categoryCanonicalUrlRewriteGenerator;
		$this->categoryUrlRewriteGenerator              = $categoryUrlRewriteGenerator;
		$this->productFactory                           = $productFactory;
		$this->categoryFactory                          = $categoryFactory;
		parent::__construct();
	}

	/**
	 * Configure the command
	 */
	protected function configure() {
		$this->setName('thlassche:regenerate_product_urls')->setDescription('Regenerate Url Rewrites for all products');
		$this->addOption(self::DEBUG_OPTION, 'd', InputOption::VALUE_NONE, 'Debug mode');
        $this->addOption(self::PRODUCT_OPTION,'p',InputOption::VALUE_REQUIRED,'Single product',0);
        $this->addOption(self::PRODUCTS_OPTION,'x',InputOption::VALUE_NONE,'Only products');
        $this->addOption(self::CATEGORIES_OPTION,'c',InputOption::VALUE_NONE,'Only categories');
        $this->addOption(self::INVISIBLE_OPTION,'i',InputOption::VALUE_NONE,'Generate for products that are invisible');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
			$this->appState->setAreaCode('adminhtml');
		} catch (\Exception $ex) {
			# Void, already set
		}

		$this->objectManager                = \Magento\Framework\App\ObjectManager::getInstance();
        $this->debug                        = $input->getOption(self::DEBUG_OPTION);
        $this->input                        = $input;
        $this->output                       = $output;
        $this->storeManager                 = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->productUrlRewriteGenerator   = $this->objectManager->get('Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator');

        $categoriesOnly = $input->getOption(self::CATEGORIES_OPTION);
        $productsOnly = $input->getOption(self::PRODUCTS_OPTION);

        if (!$categoriesOnly)
  		    $this->generateProductRewrites();
        if (!$productsOnly)
  		    $this->generateCategoryRewrites();
	}

	protected function generateProductRewrites()
    {
        $productCollection  = $this->productFactory->create()->getCollection();
        $arrProducts        = $productCollection->getItems();
        $invisble           = $this->input->getOption(self::INVISIBLE_OPTION);
        $singleProduct      = $this->input->getOption(self::PRODUCT_OPTION);

        if ($invisble)
            $this->output->writeln('WARNING: Generating URLs for invisible products too');

        if ($singleProduct)
            $this->output->writeln('Generating for single product with ID: '.$singleProduct);

        $this->startProgressBar('Started generating URL rewrites.', count($arrProducts));

        foreach ($arrProducts as $k => $objProduct)
        {
            if ($singleProduct && $objProduct->getId() != $singleProduct)
                continue;

            // Delete existing
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $objProduct->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0
            ]);

            if ($this->debug)
                $this->output->writeln('Started product '.$objProduct->getId());

            foreach ($this->storeManager->getStores() as $store)
            {
                $repo = $this->objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');

                # M2.0 does not have this method
                if (method_exists($repo, 'cleanCache'))
                    $repo->cleanCache();

                $objProduct = $repo->getById($objProduct->getId(), false, $store->getId());
                if ($invisble && $objProduct->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE)
                {
                    $objProduct->setVisibility(Visibility::VISIBILITY_IN_CATALOG);
                }
                if ($this->debug && $objProduct->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE)
                {
                    $this->output->writeln('Product is not visible in store '.$store->getId().' and therefore skipped');
                }
                $saved = false;
                $i = 1;
                do {
                    try {
                        $i++;
                        $arrUrls = $this->productUrlRewriteGenerator->generate($objProduct);

                        if ($this->debug)
                        {
                            foreach ($arrUrls as $url)
                                $this->output->writeln('Product '.$objProduct->getId().' :: '. $url->getStoreId(). ' :: '. $url->getRequestPath());
                        }
                        $this->urlPersist->replace($arrUrls);
                        $saved = true;
                    } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                        $urlKey = preg_match('/(.*)-(\d+)$/', $objProduct->getUrlKey(), $matches) ? $matches[1] . '-' . ($matches[2] + 1) : $objProduct->getUrlKey() . '-1';
                        if ($this->debug)
                            $this->output->writeln('Setting URL key for product to: '.$urlKey);
                        $objProduct->setUrlKey($urlKey);
                    }
                    catch (\Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException $e) {
                       $urlKey = preg_match('/(.*)-(\d+)$/', $objProduct->getUrlKey(), $matches) ? $matches[1] . '-' . ($matches[2] + 1) : $objProduct->getUrlKey() . '-1';
                       if ($this->debug)
                           $this->output->writeln('Setting URL key for product to: '.$urlKey);
                       $objProduct->setUrlKey($urlKey);
                   }
                } while (!$saved && $i < 10);
            }

            if (!$this->debug)
                $this->progressbar->advance();
        }

        $this->finishProgressBar('Regenerated URL rewrites successfully');
    }

	protected function generateCategoryRewrites()
    {
        # Fetch all categories
        $arrCategories = $this->categoryFactory->create()->getCollection()->getItems();

        # Start a progressbar
        $this->startProgressBar('Started generating category URL rewrites.', count($arrCategories));

        foreach ($arrCategories as $objCategory)
        {
            // Delete existing
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $objCategory->getId(),
                UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0
            ]);

            foreach ($this->storeManager->getStores() as $store)
            {
                $repo           =  $this->objectManager->get('Magento\Catalog\Api\CategoryRepositoryInterface');
                $objCategory    = $repo->get($objCategory->getId(), $store->getId());
                $saved          = false;
                $i              = 1;
                do {
                    try {
                        $i++;
                        $arrUrls = array_merge(
                            $this->categoryCanonicalUrlRewriteGenerator->generate($store->getId(), $objCategory),
                            $this->categoryUrlRewriteGenerator->generate($store->getId(), $objCategory)
                        );
                        $this->urlPersist->replace($arrUrls);

                        if ($this->debug)
                        {
                            foreach ($arrUrls as $url)
                                $this->output->writeln('Category '.$objCategory->getId().' :: '. $url->getStoreId(). ' :: '. $url->getRequestPath());
                        }
                        $saved = true;
                    } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                        $urlKey = preg_match('/(.*)-(\d+)$/', $objCategory->getUrlKey(), $matches) ? $matches[1] . '-' . ($matches[2] + 1) : $objCategory->getUrlKey() . '-1';
                        if ($this->debug)
                            $this->output->writeln('Setting URL key for category to: '.$urlKey);
                        $objCategory->setUrlKey($urlKey);
                    }
                    catch (\Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException $e) {
                       $urlKey = preg_match('/(.*)-(\d+)$/', $objCategory->getUrlKey(), $matches) ? $matches[1] . '-' . ($matches[2] + 1) : $objCategory->getUrlKey() . '-1';
                       if ($this->debug)
                           $this->output->writeln('Setting URL key for category to: '.$urlKey);
                       $objCategory->setUrlKey($urlKey);
                   }
                } while (!$saved && $i < 5);
            }

            $this->advanceProgressBar();
        }

        $this->finishProgressBar('Regenerated category URL rewrites successfully');
    }

    /**
     * Increment the progress bar
     * @author T.H. Lassche
     */
    protected function advanceProgressBar()
    {
        if (!$this->debug)
            $this->progressbar->advance();
    }

    /**
     * Finishes the progress bar
     * @param $message
     *
     * @author T.H. Lassche
     */
    protected function finishProgressBar($message) {
        if (!$this->debug)
            $this->progressbar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>'.$message.'</info>');
    }

    /**
     * Starts a new progressbar
     * @param $message
     * @param $total
     *
     * @author T.H. Lassche
     */
    protected function startProgressBar($message, $total) {
        $this->progressbar = new ProgressBar($this->output, $total);
        $this->progressbar->setFormat($this->progressFormat);
        $this->output->writeln('<info>'.$message.'</info>');
        $this->progressbar->start();

        if (!$this->debug)
            $this->progressbar->display();
    }
}