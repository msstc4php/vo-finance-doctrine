<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Swiss\QrBill;
use Override;

final class QrBillType extends AbstractStringVoType
{
    public const string NAME = 'fin_qr_bill';

    #[Override]
    protected function valueObjectClass(): string
    {
        return QrBill::class;
    }
}
