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
    public function validateValue($values)
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

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            if (!isset($options[$value])) {
                return false;
            }
        }

        return true;
    }
}
