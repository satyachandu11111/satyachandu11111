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
namespace Aheadworks\Raf\Model\Rule\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Composite
 * @package Aheadworks\Raf\Model\Rule\Validator
 */
class Composite extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($rule)
    {
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($rule)) {
                $this->_addMessages($validator->getMessages());
            }
        }
        return empty($this->getMessages());
    }
}
