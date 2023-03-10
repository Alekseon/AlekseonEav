<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 *
 */
class Select extends AbstractValidator
{
    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value)
    {
        $sourceModel = $this->attribute->getSourceModel();
        if (!$sourceModel) {
            return false;
        }

        $options = $sourceModel->getOptions();
        if (isset($options[$value])) {
            return true;
        }

        return false;
    }
}
