<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class EmailTemplate
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class EmailTemplate extends AbstractSource
{
    protected $path;

    /**
     * EmailTemplate constructor.
     * @param \Magento\Config\Model\Config\Source\Email\Template $emailTemplateSource
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Email\Template $emailTemplateSource
    )
    {
        $this->emailTemplateSource = $emailTemplateSource;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return mixed|void
     */
    public function getOptions()
    {
        $options = [];
        $emailTemplateOptions = $this->emailTemplateSource->setPath($this->path)->toOptionArray();

        foreach ($emailTemplateOptions as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }
}
