<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Cambodia\KhqrCode;
use Override;

final class KhqrCodeType extends AbstractStringVoType
{
    public const string NAME = 'fin_khqr_code';

    #[Override]
    protected function valueObjectClass(): string
    {
        return KhqrCode::class;
    }
}
