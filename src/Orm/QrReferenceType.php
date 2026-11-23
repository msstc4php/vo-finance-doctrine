<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Reference\QrReference;
use Override;

final class QrReferenceType extends AbstractStringVoType
{
    public const string NAME = 'fin_qr_reference';

    #[Override]
    protected function valueObjectClass(): string
    {
        return QrReference::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return QrReference::MAX_LENGTH;
    }
}
