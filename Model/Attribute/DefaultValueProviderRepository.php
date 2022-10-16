<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Attribute;

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
    protected $defaultValueProviderByAttributeId = [];

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
     * @return array
     */
    public function getDefaultValueProviders(Attribute $attribute): array
    {
        if (!isset($this->defaultValueProvidersByCodes[$attribute->getAttributeId()])) {
            $this->defaultValueProvidersByCodes[$attribute->getAttributeId()] = [];
            foreach ($this->defaultValueProviders as $code => $data) {
                $valueProviderFactory = $data['factory'];
                unset($data['factory']);
                $valueProvider = $valueProviderFactory->create();
                $valueProvider->setData($data);
                $valueProvider->setCode($code);
                if ($valueProvider->canBeUsedForAttribute($attribute)) {
                    $this->defaultValueProvidersByCodes[$attribute->getAttributeId()][$code] = $valueProvider;
                }
            }
        }

        return $this->defaultValueProvidersByCodes[$attribute->getAttributeId()];
    }

    /**
     * @param Attribute $attribute
     * @return Attribute\DefaultValueProvider\AbstractProvider
     */
    public function getAttributeDefaultValueProvider(Attribute $attribute)
    {
        if (!isset($this->defaultValueProviderByAttributeId[$attribute->getAttributeId()])) {
            $this->defaultValueProviderByAttributeId[$attribute->getAttributeId()] = false;
            $defaultValueProviders = $this->getDefaultValueProviders($attribute);
            $defaultValue = $attribute->getData('default_value');
            $prefixLength = strlen(DefaultValueProvider::PROVIDER_OPTION_PREFIX);
            if (substr($defaultValue, 0, $prefixLength) == DefaultValueProvider::PROVIDER_OPTION_PREFIX) {
                $providerCode = substr($defaultValue, $prefixLength);
                if (isset($defaultValueProviders[$providerCode])) {
                    $this->defaultValueProviderByAttributeId[$attribute->getAttributeId()] = $defaultValueProviders[$providerCode];
                }
            }
        }

        return $this->defaultValueProviderByAttributeId[$attribute->getAttributeId()];
    }
}
