<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
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

    #[Override]
    protected function columnLength(): int
    {
        return EpcQr::MAX_LENGTH;
    }
}
