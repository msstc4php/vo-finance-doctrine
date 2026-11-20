<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Cusip;
use Override;

final class CusipType extends AbstractStringVoType
{
    public const string NAME = 'fin_cusip';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Cusip::class;
    }
}
