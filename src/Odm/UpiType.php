<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
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
}
