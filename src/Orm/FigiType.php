<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Figi;
use Override;

final class FigiType extends AbstractStringVoType
{
    public const string NAME = 'fin_figi';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Figi::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return Figi::MAX_LENGTH;
    }
}
