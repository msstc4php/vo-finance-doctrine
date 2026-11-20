<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
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
}
