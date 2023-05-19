<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class EnableDisable
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class EnableDisable extends AbstractSource
{
    const VALUE_DISABLE = 0;
    const VALUE_ENABLE = 1;

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        return [
            self::VALUE_DISABLE => __('Disabled'),
            self::VALUE_ENABLE => __('Enabled'),
        ];
    }
}
