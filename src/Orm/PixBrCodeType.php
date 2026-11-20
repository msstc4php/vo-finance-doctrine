<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractTextVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Brazil\PixBrCode;
use Override;

final class PixBrCodeType extends AbstractTextVoType
{
    public const string NAME = 'fin_pix_br_code';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PixBrCode::class;
    }
}
