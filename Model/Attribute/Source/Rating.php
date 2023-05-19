<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 *
 */
class Rating extends AbstractSource
{
    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $label = '&#9733;';
        for ($i = 1; $i <= 5; $i ++) {
            $options[$i] = $label;
            $label .= '&#9733;';
        }
        return $options;
    }
}
