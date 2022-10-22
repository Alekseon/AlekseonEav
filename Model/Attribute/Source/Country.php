<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Source;

/**
 * Class Country
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class Country extends AbstractSource
{
    /**
     * Countries
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;

    /**
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
    ) {
        $this->countryCollection = $countryCollection;
    }

    /**
     * @return array
     */
    protected function getCountryOptions()
    {
        $countryOptions = $this->countryCollection->loadData()
            ->toOptionArray(false);
        return $countryOptions;
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $countryOptions = $this->getCountryOptions();
        $options = [];
        foreach($countryOptions as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
