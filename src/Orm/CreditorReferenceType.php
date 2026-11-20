<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Reference\CreditorReference;
use Override;

final class CreditorReferenceType extends AbstractStringVoType
{
    public const string NAME = 'fin_creditor_reference';

    #[Override]
    protected function valueObjectClass(): string
    {
        return CreditorReference::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 25;
    }
}
