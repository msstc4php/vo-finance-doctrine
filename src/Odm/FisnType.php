<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Fisn;
use Override;

final class FisnType extends AbstractStringVoType
{
    public const string NAME = 'fin_fisn';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Fisn::class;
    }
}
