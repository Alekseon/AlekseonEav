<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class AbstractBackendType
 * @package Alekseon\AlekseonEav\Model\Attribute\BackendType
 */
class Textarea extends AbstractInputType
{
    /**
     * @var string
     */
    protected $defaultBackendType = 'text';
    /**
     * @var string
     */
    protected $inputFieldType = 'textarea';
    /**
     * @var bool
     */
    protected $canUseWysiwyg = true;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    private $wysiwygConfig;

    /**
     * Textarea constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($universalFactory);
    }

    /**
     * @return string
     */
    public function getInputFieldType()
    {
        if ($this->getAttribute()->getIsWysiwygEnabled()) {
            return 'editor';
        } else {
            return $this->inputFieldType;
        }
    }

    /**
     * @param $fieldConfig
     * @return $this
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        if ($this->getAttribute()->getIsWysiwygEnabled()) {
            $wysiwygConfig = $this->wysiwygConfig->getConfig([]);
            $fieldConfig['config'] = $wysiwygConfig;
        }

        $maxLength = $this->getAttribute()->getInputParam('maxlength');
        if ($maxLength) {
            $fieldConfig['maxlength'] = $maxLength;
        }

        return $this;
    }
}
