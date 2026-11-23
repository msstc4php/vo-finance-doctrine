<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
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

    #[Override]
    protected function columnLength(): int
    {
        return Lei::MAX_LENGTH;
    }
}
