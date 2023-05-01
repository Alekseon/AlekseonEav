<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source;

use Alekseon\AlekseonEav\Model\Attribute\InputTypeRepository;

/**
 * Class InputType
 * @package Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source
 */
class InputType extends AbstractSource
{
    /**
     * @var InputTypeRepository
     */
    protected $inputTypeRepository;

    /**
     * InputType constructor.
     * @param InputTypeRepository $inputTypeRepository
     */
    public function __construct(
        InputTypeRepository $inputTypeRepository
    )
    {
        $this->inputTypeRepository = $inputTypeRepository;
    }

    /**
     * @return array
     */
    public function getOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];
            $inputTypes = $this->inputTypeRepository->getFrontendInputTypes();
            foreach ($inputTypes as $inputType) {
                $this->options[$inputType->getCode()] = __($inputType->getLabel());
                $this->hasOptions = true;
            }
        }
        return $this->options;
    }
}
