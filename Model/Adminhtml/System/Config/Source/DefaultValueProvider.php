<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProviderRepository;

/**
 * Class DefaultValueProvider
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class DefaultValueProvider extends AbstractSource
{
    const PROVIDER_OPTION_PREFIX = ':provider:';
    /**
     * @var DefaultValueProviderRepository
     */
    protected $defaultValueProviderRepository;

    /**
     * DefaultValueProvider constructor.
     * @param DefaultValueProviderRepository $defaultValueProviderRepository
     */
    public function __construct(
        DefaultValueProviderRepository $defaultValueProviderRepository
    )
    {
        $this->defaultValueProviderRepository = $defaultValueProviderRepository;
    }

    /**
     * @return array
     */
    public function getOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = ['' => __('None')];
            $defaultValueProviders = $this->defaultValueProviderRepository->getDefaultValueProviders($this->attribute);
            foreach ($defaultValueProviders as $provider) {
                $this->options[self::PROVIDER_OPTION_PREFIX . $provider->getCode()] = __($provider->getLabel());
                $this->hasOptions = true;
            }
        }

        return $this->options;
    }
}
