<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Security\Wkn;
use Override;

final class WknType extends AbstractStringVoType
{
    public const string NAME = 'fin_wkn';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Wkn::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 6;
    }
}
