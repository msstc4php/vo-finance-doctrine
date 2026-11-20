<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Malaysia\DuitNowQr;
use Override;

final class DuitNowQrType extends AbstractStringVoType
{
    public const string NAME = 'fin_duit_now_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return DuitNowQr::class;
    }
}
