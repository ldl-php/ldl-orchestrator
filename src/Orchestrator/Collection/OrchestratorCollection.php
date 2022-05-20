<?php

declare(strict_types=1);

namespace LDL\Orchestrator\Collection;

use LDL\Orchestrator\OrchestratorInterface;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Validators\InterfaceComplianceValidator;

class OrchestratorCollection extends AbstractTypedCollection implements OrchestratorCollectionInterface
{
    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->append(new InterfaceComplianceValidator(OrchestratorInterface::class))
            ->lock();

        parent::__construct($items);
    }
}
