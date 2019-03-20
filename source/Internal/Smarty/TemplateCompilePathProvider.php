<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class TemplateCompilePathProvider implements TemplateCompilePathProviderInterface
{
    const COMPILE_DIRECTORY = '/smarty/';

    /**
     * @var string
     */
    private $shopCompilePath;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * TemplateCompilePathProvider constructor.
     *
     * @param SmartyContextInterface $context
     * @param Filesystem $fileSystem
     */
    public function __construct(SmartyContextInterface $context, Filesystem $fileSystem)
    {
        $this->shopCompilePath = $context->getShopCompileDirectory();
        $this->fileSystem = $fileSystem;
    }

    /**
     * Returns a full path to Smarty compile dir
     *
     * @return string
     */
    public function getTemplateCompilePath(): string
    {
        //check for the Smarty dir
        $smartyCompileDir = $this->shopCompilePath . self::COMPILE_DIRECTORY;

        try {
            $this->fileSystem->mkdir($smartyCompileDir);
        } catch (IOException $exception) {
            $smartyCompileDir = $this->shopCompilePath;
        }

        return $smartyCompileDir;
    }
}