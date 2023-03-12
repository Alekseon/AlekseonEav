<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

use Alekseon\AlekseonEav\Model\Attribute\Source\AbstractSource;

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
        /** @var AbstractSource $sourceModel */
        $sourceModel = $this->attribute->getSourceModel();

        if (!$sourceModel) {
            return false;
        }

        if (!$sourceModel->validateOptionKeyOnObjectSave()) {
            return true;
        }

        $options = $sourceModel->getOptions();
        if (isset($options[$value])) {
            return true;
        }

        return false;
    }
}
