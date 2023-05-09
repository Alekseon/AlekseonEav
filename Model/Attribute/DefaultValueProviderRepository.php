<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute;

use Alekseon\AlekseonEav\Api\Data\AttributeInterface;
use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\DefaultValueProvider;
use Alekseon\AlekseonEav\Model\Attribute;

/**
 * Class DefaultValueProviderRepository
 * @package Alekseon\AlekseonEav\Model\Attribute
 */
class DefaultValueProviderRepository
{
    /**
     * @var
     */
    protected $defaultValueProviders;
    /**
     * @var array
     */
    protected $defaultValueProvidersByCodes = [];
    /**
     * @var array
     */
    protected $defaultValueProviderByAttribute = [];

    /**
     * DefaultValueProviderRepository constructor.
     * @param array $defaultValueProviders
     */
    public function __construct(
        array $defaultValueProviders = []
    ) {
        $this->defaultValueProviders = $defaultValueProviders;
    }

    /**
     * @return DefaultValueProvider
     */
    public function getDefaultValueProviders(AttributeInterface $attribute): array
    {
        $attributeKey = $attribute->getResource()->getEntityTypeCode() . '_' . $attribute->getId();
        if (!isset($this->defaultValueProvidersByCodes[$attributeKey])) {
            $this->defaultValueProvidersByCodes[$attributeKey] = [];
            foreach ($this->defaultValueProviders as $code => $data) {
                $valueProviderFactory = $data['factory'];
                unset($data['factory']);
                $valueProvider = $valueProviderFactory->create();
                $valueProvider->addData($data);
                $valueProvider->setCode($code);
                $valueProvider->setAttribute($attribute);
                if ($valueProvider->canBeUsedForAttribute()) {
                    $this->defaultValueProvidersByCodes[$attributeKey][$code] = $valueProvider;
                }
            }
        }

        return $this->defaultValueProvidersByCodes[$attributeKey];
    }

    /**
     * @param AttributeInterface $attribute
     * @return Attribute\DefaultValueProvider\AbstractProvider | false
     */
    public function getAttributeDefaultValueProvider(AttributeInterface $attribute)
    {
        $attributeKey = $attribute->getResource()->getEntityTypeCode() . '_' . $attribute->getId();
        if (!isset($this->defaultValueProviderByAttribute[$attributeKey])) {
            $this->defaultValueProviderByAttribute[$attributeKey] = false;
            $defaultValueProviders = $this->getDefaultValueProviders($attribute);
            $defaultValue = $attribute->getDefaultValue() ?? '';

            // if there is more default values, provider is always as first one
            if (is_array($defaultValue)) {
                $defaultValue = $defaultValue[0] ?? '';
            }

            $prefixLength = strlen(DefaultValueProvider::PROVIDER_OPTION_PREFIX);
            if (substr($defaultValue, 0, $prefixLength) == DefaultValueProvider::PROVIDER_OPTION_PREFIX) {
                $providerCode = substr($defaultValue, $prefixLength);
                if (isset($defaultValueProviders[$providerCode])) {
                    $this->defaultValueProviderByAttribute[$attributeKey] = $defaultValueProviders[$providerCode];
                }
            }
        }

        return $this->defaultValueProviderByAttribute[$attributeKey];
    }
}
