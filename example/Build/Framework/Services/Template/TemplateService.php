<?php

declare(strict_types=1);

namespace LDL\Example\Build\Framework\Services\Template;

class TemplateService
{
    public function get(): string
    {
        return file_get_contents(__DIR__."/example.tpl");
    }
}
