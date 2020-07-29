<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Builder\Config\Interfaces;

interface OptionsInterface extends \JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;
}