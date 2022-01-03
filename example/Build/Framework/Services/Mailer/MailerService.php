<?php

declare(strict_types=1);

namespace LDL\Example\Build\Framework\Services\Mailer;

use LDL\Example\Build\Framework\Services\Template\TemplateService;

class MailerService
{
    /**
     * @var TemplateService
     */
    private $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function getTemplate(): TemplateService
    {
        return $this->templateService;
    }
}
