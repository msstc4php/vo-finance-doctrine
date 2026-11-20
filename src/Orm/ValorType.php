<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Valor;
use Override;

final class ValorType extends AbstractStringVoType
{
    public const string NAME = 'fin_valor';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Valor::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 9;
    }
}
