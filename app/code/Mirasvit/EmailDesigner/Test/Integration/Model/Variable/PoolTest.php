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
 * @package   mirasvit/module-email-designer
 * @version   1.1.23
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\EmailDesigner\Service\TemplateEngine\Php\Variable;

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\EmailDesigner\Service\TemplateEngine\Php\Variable\Pool
     */
    protected $pool;

    protected function setUp()
    {
        $this->pool = Bootstrap::getObjectManager()->create('Mirasvit\EmailDesigner\Service\TemplateEngine\Php\Variable\Pool');

        $this->pool->getContext()->addData([
            'store_id' => 1,
        ]);
    }

    /**
     * @dataProvider nameProvider
     *
     * @param string $name
     * @param string $expected
     */
    public function testResolve($name, $expected)
    {
        $this->assertEquals($expected, $this->pool->resolve($name));
    }

    public function nameProvider()
    {
        return [
            ['storeName', 'Main Website Store'],
            ['getStoreName', 'Main Website Store'],
            ['storeEmail', 'owner@example.com'],
            ['storePhone', ''],
            ['storeAddress', ''],
            ['storeUrl', 'http://localhost/index.php/']
        ];
    }
}
