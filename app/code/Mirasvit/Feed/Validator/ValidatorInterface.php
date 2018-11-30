<?php

namespace Mirasvit\Feed\Validator;

interface ValidatorInterface
{
    /**
     * Get validator code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Get validator name.
     *
     * @return string
     */
    public function getName();

    /**
     * Validate given value according to concrete validator logic.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid($value);

    /**
     * Retrieve validator error message.
     *
     * @param bool $isHtml
     *
     * @return string
     */
    public function getMessage($isHtml = false);

    /**
     * Get validator hint to fix error.
     *
     * @param string $attribute
     *
     * @return string
     */
    public function getHint($attribute = '');
}
