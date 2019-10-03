<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class ActivateConfiguredModulesCommand extends Command
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleActivationServiceInterface $moduleActivationService,
        ModuleStateServiceInterface $moduleStateService
    ) {
        parent::__construct();

        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleStateService = $moduleStateService;
    }

    protected function configure()
    {
        $this->setDescription('Applies configuration for installed modules.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('shop-id') && $input->getOption('shop-id')) {
            $this->activateModulesForOneShop($output, (int) $input->getOption('shop-id'));
        } else {
            $this->activateModuleForAllShops($output);
        }
    }

    private function activateModulesForOneShop(OutputInterface $output, int $shopId): void
    {
        $shopConfiguration = $this->shopConfigurationDao->get($shopId);

        $this->activateModulesForShop($output, $shopConfiguration, $shopId);
    }

    private function activateModuleForAllShops(OutputInterface $output): void
    {
        $shopConfigurations = $this->shopConfigurationDao->getAll();

        foreach ($shopConfigurations as $shopId => $shopConfiguration) {
            $this->activateModulesForShop($output, $shopConfiguration, $shopId);
        }
    }

    private function activateModulesForShop(
        OutputInterface $output,
        ShopConfiguration $shopConfiguration,
        int $shopId
    ): void {
        $output->writeln('<info>Applying modules configuration for the shop with id ' . $shopId . ':</info>');

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $output->writeln(
                '<info>Applying configuration for module with id '
                . $moduleConfiguration->getId()
                . '</info>'
            );
            try {
                if ($moduleConfiguration->isConfigured()) {
                    $this->activateModule($moduleConfiguration, $shopId);
                } elseif ($this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)) {
                    $this->moduleActivationService->deactivate($moduleConfiguration->getId(), $shopId);
                }
            } catch (\Exception $exception) {
                $this->showErrorMessage($output, $exception);
            }
        }
    }

    private function activateModule(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        if ($this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)) {
            $this->moduleActivationService->deactivate($moduleConfiguration->getId(), $shopId);
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        } else {
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        }
    }

    private function showErrorMessage(OutputInterface $output, \Exception $exception): void
    {
        $output->writeln(
            '<error>'
            . 'Module configuration wasn\'t applied. An exception occurred: '
            . \get_class($exception) . ' '
            . $exception->getMessage()
            . '</error>'
        );
    }
}
