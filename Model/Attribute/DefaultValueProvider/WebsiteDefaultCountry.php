<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider;

use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class WebsiteDefaultCountry
 * @package Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider
 */
class WebsiteDefaultCountry extends AbstractProvider
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * DefaultWebsiteCountry constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $data = [])
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($data);
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        $value = $this->scopeConfig->getValue(
            Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT
            , ScopeInterface::SCOPE_STORE
        );

        $allowedCountries = $this->attribute->getSourceModel()->getOptions();

        if (isset($allowedCountries[$value])) {
            return $value;
        }

        $defaulSourceValues = $this->attribute->getData('default_value') ?? '';
        return explode(',', $defaulSourceValues);
    }
}
