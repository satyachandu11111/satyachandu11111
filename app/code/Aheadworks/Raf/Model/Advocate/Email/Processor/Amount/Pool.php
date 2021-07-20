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
namespace Aheadworks\Raf\Model\Advocate\Email\Processor\Amount;

/**
 * Class Pool
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\Amount
 */
class Pool
{
    /**#@+
     * Advocate email processors
     */
    const NEW_FRIEND = 'newFriend';
    const EXPIRATION_REMINDER = 'expiration_reminder';
    const EXPIRATION = 'expiration';
    /**#@-*/

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
     * Retrieve advocate amount processor
     *
     * @param string $type
     * @return AmountProcessorInterface
     */
    public function get($type)
    {
        if (!isset($this->processors[$type])) {
            throw new \InvalidArgumentException($type . ' is unknown type');
        }

        return $this->processors[$type];
    }
}
