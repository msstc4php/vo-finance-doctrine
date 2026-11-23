<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Cfi;
use Override;

final class CfiType extends AbstractStringVoType
{
    public const string NAME = 'fin_cfi';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Cfi::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return Cfi::MAX_LENGTH;
    }
}
