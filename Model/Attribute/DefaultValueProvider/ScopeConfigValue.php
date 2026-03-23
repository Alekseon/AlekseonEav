<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute\Backend\DefaultValue;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class WebsiteDefaultCountry
 * @package Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider
 */
class ScopeConfigValue extends AbstractProvider
{
    protected $backendModelMode = DefaultValue::MODE_NOT_SET;

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
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($data);
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        $configPath = $this->attribute->getAttributeExtraParam('valueScopeConfigPath');
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
        );
    }
}
