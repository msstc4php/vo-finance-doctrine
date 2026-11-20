<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Lei;
use Override;

final class LeiType extends AbstractStringVoType
{
    public const string NAME = 'fin_lei';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Lei::class;
    }
}
