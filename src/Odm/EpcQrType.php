<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Epc\EpcQr;
use Override;

final class EpcQrType extends AbstractStringVoType
{
    public const string NAME = 'fin_epc_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return EpcQr::class;
    }
}
