<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Japan\JpqrCode;
use Override;

final class JpqrCodeType extends AbstractStringVoType
{
    public const string NAME = 'fin_jpqr_code';

    #[Override]
    protected function valueObjectClass(): string
    {
        return JpqrCode::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 512;
    }
}
