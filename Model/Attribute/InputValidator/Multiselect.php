<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputValidator;

/**
 *
 */
class Multiselect extends Select
{
    /**
     * @param $value
     * @return bool
     */
    public function validateValue($values)
    {
        if (!is_array($values)) {
            $values = explode(',', (string) $values);
        }
        return parent::validateValue($values);
    }
}
