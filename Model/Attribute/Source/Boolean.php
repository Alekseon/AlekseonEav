<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class Boolean
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class Boolean extends AbstractSource
{
    const VALUE_NO = '0';
    const VALUE_YES = '1';

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        return [
            self::VALUE_NO => __('No'),
            self::VALUE_YES => __('Yes'),
        ];
    }
}
