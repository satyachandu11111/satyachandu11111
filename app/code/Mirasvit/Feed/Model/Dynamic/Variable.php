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
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Model\Dynamic;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Feed\Model\Config;

/**
 * @method string getName()
 * @method string getCode()
 * @method string getPhpCode()
 * @method $this setPhpCode($code)
 */
class Variable extends AbstractModel
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context,
        Config $config,
        Registry $registry
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Mirasvit\Feed\Export\Resolver\ProductResolver $resolver
     * @return string
     * @SuppressWarnings(PHPMD)
     */
    public function getValue($product, $resolver)
    {
        $objectManager = $this->objectManager;
        $tmpPath = $this->config->getTmpPath() . '/' . time() . rand(1, 10000).'.php';
        $code = '<?php ' . $this->getPhpCode() . ' ?>';
        file_put_contents($tmpPath, $code);
        $value = $this->getOutput($tmpPath, $product);

        return $value;
    }

    /**
     * @param string $tmpPath
     * @param \Magento\Catalog\Model\Product $product;
     * @return string
     */
    private function getOutput($tmpPath, $product)
    {
        $objectManager = $this->objectManager;
        ob_start();
        $output =  include $tmpPath;
        unlink($tmpPath);
        ob_get_clean();

        return $output;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function isValid($code = null)
    {
        if (!$code) {
            $code = $this->getPhpCode();
        }

        $code = escapeshellarg('<?php ' . $code . ' ?>');
        $lint = "echo $code | php -l";

        return (preg_match('/No syntax errors detected in -/', $lint));
    }

    /**
     * @return array
     */
    public function getRowsToExport()
    {
        $array = [
            'name',
            'code',
            'php_code',
        ];

        return $array;
    }
}
