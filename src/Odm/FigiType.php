<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
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
}
