<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Mic;
use Override;

final class MicType extends AbstractStringVoType
{
    public const string NAME = 'fin_mic';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Mic::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return Mic::MAX_LENGTH;
    }
}
