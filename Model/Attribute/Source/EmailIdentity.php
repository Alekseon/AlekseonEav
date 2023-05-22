<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class EmailIdentity
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class EmailIdentity extends AbstractSource
{
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Identity
     */
    private $emailIdentitySource;

    /**
     * EmailIdentity constructor.
     * @param \Magento\Config\Model\Config\Source\Email\Identity $emailIdentitySource
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Email\Identity $emailIdentitySource
    )
    {
        $this->emailIdentitySource = $emailIdentitySource;
    }

    /**
     * @return mixed|void
     */
    public function getOptions()
    {
        $options = [];
        $emailIdentityOptions = $this->emailIdentitySource->toOptionArray();

        foreach ($emailIdentityOptions as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }
}
