<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Attribute\MetadataForm;

/**
 * Class Date
 * @package Alekseon\Eav\Model\Attribute\MetadataForm
 */
class Date extends AbstractMetadataForm
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * Date constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    )
    {
        $this->localeDate = $localeDate;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function extractValue(\Magento\Framework\App\RequestInterface $request, $paramName = null)
    {
        $value = parent::extractValue($request, $paramName);
        $filterClass = 'Magento\Framework\Data\Form\Filter\Date';
        $filter = new $filterClass($this->dateFilterFormat(), $this->localeResolver);
        $value = $filter->inputFilter($value);
        return $value;
    }

    /**
     * @return mixed
     */
    protected function dateFilterFormat()
    {
        return $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }
}
