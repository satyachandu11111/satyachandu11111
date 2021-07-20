<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Test\Unit\Model\Advocate\Reward;

use PHPUnit\Framework\TestCase;
use Aheadworks\Raf\Model\Advocate\Balance\Calculator;

/**
 * Class CalculatorTest
 *
 * @package Aheadworks\Raf\Model\Advocate\Balance
 */
class CalculatorTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->object = new Calculator();
    }

    /**
     * Testing of calculateNewCumulativeAmount method
     *
     * @dataProvider calculateNewCumulativeAmountProvider
     * @param float $currentCumulativeAmount
     * @param float $amount
     * @param float $result
     */
    public function testCalculateNewCumulativeAmount($currentCumulativeAmount, $amount, $result)
    {
        $this->assertSame($result, $this->object->calculateNewCumulativeAmount($currentCumulativeAmount, $amount));
    }

    /**
     * Data provider for testCalculateNewCumulativeAmount method
     *
     * @return array
     */
    public function calculateNewCumulativeAmountProvider()
    {
        return [
            'case 1' => [10, 10, 20],
            'case 2' => [-10, 10, 0],
            'case 3' => [-20, 10, 0]
        ];
    }
}
