<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Sedol;
use Override;

final class SedolType extends AbstractStringVoType
{
    public const string NAME = 'fin_sedol';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Sedol::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 7;
    }
}
