<?php

namespace Sprint\Migration\Builders;

use Sprint\Migration\Exceptions\ExchangeException;
use Sprint\Migration\Exceptions\HelperException;
use Sprint\Migration\Exceptions\MigrationException;
use Sprint\Migration\Exceptions\RebuildException;
use Sprint\Migration\Exceptions\RestartException;
use Sprint\Migration\Locale;
use Sprint\Migration\Module;
use Sprint\Migration\VersionBuilder;

class HlblockElementsBuilder extends VersionBuilder
{
    /**
     * @return bool
     */
    protected function isBuilderEnabled()
    {
        return (!Locale::isWin1251() && $this->getHelperManager()->Hlblock()->isEnabled());
    }

    protected function initialize()
    {
        $this->setTitle(Locale::getMessage('BUILDER_HlblockElementsExport1'));
        $this->setDescription(Locale::getMessage('BUILDER_HlblockElementsExport2'));
        $this->addVersionFields();
    }

    /**
     * @throws ExchangeException
     * @throws HelperException
     * @throws RebuildException
     * @throws RestartException
     * @throws MigrationException
     */
    protected function execute()
    {
        $hlblockId = $this->addFieldAndReturn(
            'hlblock_id',
            [
                'title'       => Locale::getMessage('BUILDER_HlblockElementsExport_HlblockId'),
                'placeholder' => '',
                'width'       => 250,
                'select'      => $this->getHelperManager()->HlblockExchange()->getHlblocksStructure(),
            ]
        );

        if (!isset($this->params['~version_name'])) {
            $this->params['~version_name'] = $this->getVersionName();
            $versionName = $this->params['~version_name'];
        } else {
            $versionName = $this->params['~version_name'];
        }

        $this->getExchangeManager()
             ->HlblockElementsExport()
             ->setLimit(20)
             ->setExportFields(
                 $this->getHelperManager()->HlblockExchange()->getHlblockFieldsCodes($hlblockId)
             )
             ->setHlblockId($hlblockId)
             ->setExchangeFile(
                 $this->getVersionResourceFile($versionName, 'hlblock_elements.xml')
             )
             ->execute();

        $this->createVersionFile(
            Module::getModuleDir() . '/templates/HlblockElementsExport.php',
            [
                'version' => $versionName,
            ]
        );

        unset($this->params['~version_name']);
    }
}
