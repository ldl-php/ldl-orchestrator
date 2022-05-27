<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Compiler;

use LDL\Env\Util\Line\Collection\EnvLineCollection;
use LDL\Framework\Base\Collection\Exception\LockAppendException;
use LDL\Framework\Helper\ArrayHelper\Exception\InvalidKeyException;
use LDL\Orchestrator\Collection\OrchestratorCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OrchestratorCompiler implements OrchestratorCompilerInterface
{
    /**
     * @var OrchestratorCollectionInterface
     */
    private $orchestrators;

    public function __construct(
        OrchestratorCollectionInterface $orchestrators
    ) {
        $this->orchestrators = $orchestrators;
    }

    public function compile(): CompiledOrchestratorInterface
    {
        $envLines = new EnvLineCollection();
        $finalContainer = new ContainerBuilder();

        foreach ($this->orchestrators as $orchestrator) {
            $envFiles = $orchestrator->getEnvFinder()->find();
            $serviceFiles = $orchestrator->getServiceFinder()->find();
            $compilerPassFiles = $orchestrator->getCompilerPassFinder()->find();

            // Merge all containers from each orchestrator into one single container
            $finalContainer->merge($orchestrator->getContainerBuilder()->build($serviceFiles, $compilerPassFiles));

            try {
                $envLines->appendMany($orchestrator->getEnvBuilder()->build($envFiles));
            } catch (
                InvalidKeyException|
                LockAppendException $e
            ) {
                /*
                 * Nothing should be thrown, catch here is set so no IDE errors are shown
                 */
            }
        }

        return new CompiledOrchestrator($finalContainer, $envLines);
    }
}
