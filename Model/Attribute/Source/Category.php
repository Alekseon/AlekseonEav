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
class Category extends AbstractSource
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var
     */
    protected $categories;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param int $parentId
     * @return array|mixed
     */
    protected function getCategories($parentId = 0)
    {
        if ($this->categories === null) {
            $this->categories = [];
            $categoryCollection = $this->categoryFactory->create()->getCollection();
            $categoryCollection->addFieldToSelect('name');
            $categoryCollection->setOrder('position');
            foreach ($categoryCollection as $category) {
                $this->categories[$category->getParentId()][] = $category;
            }
        }

        if (isset($this->categories[$parentId])) {
            return $this->categories[$parentId];
        } else {
            return [];
        }
    }

    /**
     * @param int $parentCategoryId
     * @return array
     */
    protected function getCategoryOptions($parentCategory = false, $deep = 0)
    {
        $parentCategoryId = $parentCategory ? $parentCategory->getId() : 0;
        $categories = $this->getCategories($parentCategoryId);
        $categoryOptions = [];

        if (!(empty($categories))) {
            foreach ($categories as $category) {

                $subOptions = $this->getCategoryOptions($category, $deep + 1);

                if ($category->getParentId()) {
                    $optionName = str_repeat( '&nbsp;', ($deep - 1) * 3)
                        . ' '
                        . $category->getName()
                        . ' (ID: '
                        . $category->getId()
                        . ')';

                    $categoryOptions[] = [
                        'value' => $category->getId(),
                        'label' => $optionName,
                    ];
                }

                $categoryOptions = array_merge($categoryOptions, $subOptions);
            }
        }

        return $categoryOptions;
    }

    /**
     * @return array|mixed
     */
    public function getOptions()
    {
        $options = [];
        $categoryOptions = $this->getCategoryOptions();
        foreach($categoryOptions as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
