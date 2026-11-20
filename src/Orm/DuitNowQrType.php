<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractTextVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Malaysia\DuitNowQr;
use Override;

final class DuitNowQrType extends AbstractTextVoType
{
    public const string NAME = 'fin_duit_now_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return DuitNowQr::class;
    }
}
