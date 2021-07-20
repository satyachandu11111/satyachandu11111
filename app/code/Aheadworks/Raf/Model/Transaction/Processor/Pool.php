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
namespace Aheadworks\Raf\Model\Transaction\Processor;

/**
 * Class Pool
 *
 * @package Aheadworks\Raf\Model\Transaction\Processor
 */
class Pool
{
    /**
     * @var string
     */
    const BASE_PROCESSOR = 'base_processor';

    /**
     * @var array
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve advocate transaction processor by action
     *
     * @param string $type
     * @return ProcessorInterface
     */
    public function getByAction($type)
    {
        if (!isset($this->processors[$type])) {
            $type = self::BASE_PROCESSOR;
        }

        return $this->processors[$type];
    }
}
