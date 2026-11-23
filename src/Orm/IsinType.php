<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Isin;
use Override;

final class IsinType extends AbstractStringVoType
{
    public const string NAME = 'fin_isin';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Isin::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return Isin::MAX_LENGTH;
    }
}
