<?php declare(strict_types=1);

namespace LDL\Orchestrator\Config;

use LDL\Orchestrator\Builder\Config\Interfaces\OptionsInterface;

class OrchestratorConfig implements OptionsInterface
{
    public const MODE_PRODUCTION = 'PROD';
    public const MODE_DEVELOPMENT = 'DEV';

    public const DEFAULT_MODE = self::MODE_PRODUCTION;
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $mode = self::DEFAULT_MODE;

    /**
     * @var array
     */
    private $finder = [];

    /**
     * @var array
     */
    private $writer = [];

    /**
     * @var array
     */
    private $reader = [];

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $instance->setSource(getcwd());
        $defaults = $instance->toArray();
        $merge = array_replace_recursive($defaults, $options);

        return $instance->setSource($merge['source'])
            ->setMode($merge['mode'])
            ->setFinder($merge['config']['finder'])
            ->setReader($merge['config']['reader'])
            ->setWriter($merge['config']['writer']);
    }

    public function toArray(): array
    {
        return [
            'source' => $this->getSource(),
            'mode' => $this->getMode(),
            'config' => [
                'finder' => $this->getFinder(),
                'reader' => $this->getReader(),
                'writer' => $this->getWriter()
            ]
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return OrchestratorConfig
     */
    private function setSource(string $source): OrchestratorConfig
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return OrchestratorConfig
     */
    private function setMode(string $mode): OrchestratorConfig
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return array
     */
    public function getFinder(): array
    {
        return $this->finder;
    }

    /**
     * @param array $finder
     * @return OrchestratorConfig
     */
    private function setFinder(array $finder): OrchestratorConfig
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * @return array
     */
    public function getWriter(): array
    {
        return $this->writer;
    }

    /**
     * @param array $writer
     * @return OrchestratorConfig
     */
    private function setWriter(array $writer): OrchestratorConfig
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * @return array
     */
    public function getReader(): array
    {
        return $this->reader;
    }

    /**
     * @param array $reader
     * @return OrchestratorConfig
     */
    private function setReader(array $reader): OrchestratorConfig
    {
        $this->reader = $reader;
        return $this;
    }
}