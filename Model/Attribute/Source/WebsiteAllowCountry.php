<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\Source;

use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class WebsiteAllowCountry
 * @package Alekseon\AlekseonEav\Model\Attribute\Source
 */
class WebsiteAllowCountry extends Country
{
    /**
     * Countries
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;
    /**
     * @var \Magento\Directory\Model\AllowedCountries
     */
    protected $allowedCountries;

    /**
     * WebsiteAllowCountry constructor.
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     * @param \Magento\Directory\Model\AllowedCountries $allowedCountries
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \Magento\Directory\Model\AllowedCountries $allowedCountries,
        \Magento\Framework\App\State $appState
    ) {
        $this->countryCollection = $countryCollection;
        $this->allowedCountries = $allowedCountries;
        $this->appState = $appState;
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $allowedCountries = null;
        if ($this->appState->getAreaCode() == Area::AREA_FRONTEND) {
            $allowedCountries = $this->allowedCountries->getAllowedCountries();
        }

        $countryOptions = $this->getCountryOptions();
        $options = [];
        foreach($countryOptions as $option) {
            if ($allowedCountries === null || in_array($option['value'], $allowedCountries)) {
                $options[$option['value']] = $option['label'];
            }
        }
        return $options;
    }
}
