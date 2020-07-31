<?php declare(strict_types=1);

namespace LDL\Orchestrator\Console\Command;

use LDL\DependencyInjection\Container\Config\ContainerConfig;
use LDL\DependencyInjection\Container\Config\ContainerConfigFactory;
use LDL\DependencyInjection\Container\Writer\ContainerFileWriter;
use LDL\DependencyInjection\Container\Writer\Options\ContainerWriterOptions;
use LDL\Env\Config\EnvConfig;
use LDL\Env\Writer\EnvFileWriter;
use LDL\Env\Writer\Options\EnvWriterOptions;
use LDL\Orchestrator\Builder\Builder;
use LDL\Orchestrator\Builder\Config\Config\BuilderConfig;
use LDL\Orchestrator\Builder\Config\Writer\BuilderConfigWriter;
use LDL\Orchestrator\Builder\Config\Writer\Options\BuilderConfigWriterOptions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrchestratorBuildCommand extends AbstractOrchestratorCommand
{
    public const COMMAND_NAME = 'orchestrator:build';

    public function configure() : void
    {
        parent::configure();

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Builds container dependencies');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            $this->build($input, $output);

            return parent::EXIT_SUCCESS;

        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return parent::EXIT_ERROR;
        }
    }

    private function build(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $start = hrtime(true);
        $file = $input->getOption('input-file');

        try {

            $title = sprintf(
                '[ Building container in "%s" mode ]',
                $this->orchestrator->isDevMode() ? 'DEV' : 'PROD'
            );

            $output->writeln("\n<info>$title</info>\n");

            $progressBar = new ProgressBar($output);
            $progressBar->start();

            $builder = Builder::fromConfigFile($file);
            $builder->build();

            /**
             * Get founded services files
             */
            $services = [];
            $servicesFiles = $builder->getLDLContainerBuilder()->getServiceFinder()->find(true);

            foreach($servicesFiles as $servicesFile){
                $services[] = $servicesFile->getRealPath();
            }

            /**
             * Get founded compiler pass files
             */
            $cpass = [];
            $cpassFiles = $builder->getLDLContainerBuilder()->getCompilerPassFinder()->find(true);

            foreach($cpassFiles as $cpassFile){
                $cpass[] = $cpassFile->getRealPath();
            }

            $builderConfig = $builder->getBuilderConfig();
            $containerConfig = $builderConfig->getContainerConfig();
            $containerConfig['services']['finder']['files'] = $services;
            $containerConfig['compilerPass']['finder']['files'] = $cpass;

            $fixBuilderConfig = BuilderConfig::fromArray([
                'description' => $builderConfig->getDescription(),
                'orchestrator' => $builderConfig->getOrchestratorConfig(),
                'env' => $builderConfig->getEnvConfig(),
                'container' => $containerConfig
            ]);

            $orchestratorLock = $builder->compileLock($fixBuilderConfig);
            $lockConfig = json_encode($orchestratorLock->toArray(), \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);

            $orchestratorConfig = $builder->getOrchestratorConfig();

            $builderConfigWriter = new BuilderConfigWriter(
                BuilderConfigWriterOptions::fromArray($orchestratorConfig->getWriter())
            );

            $builderConfigWriter->write($lockConfig, true);

            $envFixedDate = $fixBuilderConfig->getEnvConfig();
            $envFixedDate['generation']['date'] = \DateTime::createFromFormat(\DateTimeInterface::W3C, $envFixedDate['generation']['date']);
            $envConfig = EnvConfig::fromArray($envFixedDate);

            $containerFixedDate = $fixBuilderConfig->getContainerConfig();
            $containerFixedDate['generation']['date'] = \DateTime::createFromFormat(\DateTimeInterface::W3C, $containerFixedDate['generation']['date']);
            $containerConfig = ContainerConfig::fromArray($containerFixedDate);

            if(false === $this->orchestrator->isDevMode()){
                $envWriter = new EnvFileWriter(EnvWriterOptions::fromArray($envConfig->getWriterOptions()));
                $containerWriter = new ContainerFileWriter(ContainerWriterOptions::fromArray($containerConfig->getContainerWriter()));

                $envWriter->write($envConfig, $builder->getEnvContent());
                $containerWriter->write($containerConfig, $builder->getContainerBuilder());
            }

            $output->writeln("");

        }catch(\Exception $e){

            $output->writeln("\n<error>Build failed!</error>\n");
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

            return;
        }

        $progressBar->finish();

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000,2);

        $output->writeln("\n<info>Took: $total seconds</info>");
    }

    public function getOrchestrator() : ?Builder
    {
        return $this->orchestrator;
    }

}