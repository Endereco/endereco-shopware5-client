<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\AttributeBundle\Service\CrudService;

class AddMetaAndSessionAttributes extends MetaAndSessionAttributes
{
    /**
     * Executes the process of adding or updating meta and session attributes.
     *
     * @param CrudService $crud The CRUD service used for attribute operations.
     * @param string $versionSuffix An optional suffix to append to the attribute names, defaults to 'gh'.
     *
     * @return void
     */
    public function execute(CrudService $crud, $versionSuffix = 'gh')
    {
        $attributeDataList = $this->generateAllAttributeCombinations($versionSuffix);
        $this->createOrUpdateAttributes($attributeDataList, $crud);
        $this->flush();
    }
}
