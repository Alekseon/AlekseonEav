<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 * Class MaxLength
 * @package Alekseon\AlekseonEav\Model\Attribute\InputValidator
 */
class MaxFileSize extends AbstractValidator
{
    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value)
    {
        $attrCode = $this->attribute->getAttributeCode();
        $maxSize = (int) $this->attribute->getInputParam('maxFileSizeInMb');
        // phpcs:disable Magento2.Security.Superglobal
        $size = $_FILES[$attrCode]['size'] ?? 0;
        // phpcs:enable Magento2.Security.Superglobal

        $size = $size / 1024 / 1024;
        if ($size > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getDataValidateParams()
    {
        $maxSize = (int) $this->attribute->getInputParam('maxFileSizeInMb');
        return [
            'alekseon-validate-form-filesize' => $maxSize,
        ];
    }
}
