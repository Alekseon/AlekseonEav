<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\InputType;

/**
 * Class Datetime
 * @package Alekseon\AlekseonEav\Model\Attribute\InputType
 */
class Date extends AbstractInputType
{
    /**
     * @var string
     */
    protected $defaultBackendType = 'datetime';
    /**
     * @var string
     */
    protected $inputFieldType = 'date';
    /**
     * @var string
     */
    protected $gridColumnType = 'date';
    /**
     * @var bool
     */
    protected $backendModel = 'Alekseon\AlekseonEav\Model\Attribute\Backend\Datetime';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;
    /**
     * @var \Alekseon\Eav\Model\Attribute\MetadataForm\DataFactort
     */
    private $metadataFormFactory;

    /**
     * Date constructor.
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Alekseon\AlekseonEav\Model\Attribute\MetadataForm\DateFactory $metadataFormFactory
    ) {
        $this->localeDate = $localeDate;
        $this->metadataFormFactory = $metadataFormFactory;
        parent::__construct($universalFactory);
    }

    /**
     * @param $fieldConfig
     * @return $this
     */
    public function prepareFormFieldConfig(&$fieldConfig)
    {
        $fieldConfig['date_format'] = $this->localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        return $this;
    }

    /**
     * @param $columnConfig
     * @return $this
     */
    public function prepareGridColumnConfig(&$columnConfig)
    {
        $columnConfig['timezone'] = false;
        return $this;
    }

    /**
     * @inheirtDoc
     */
    public function getMetadataFormModel()
    {
        return $this->metadataFormFactory->create();
    }
}
