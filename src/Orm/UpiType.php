<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Upi;
use Override;

final class UpiType extends AbstractStringVoType
{
    public const string NAME = 'fin_upi';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Upi::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 12;
    }
}
