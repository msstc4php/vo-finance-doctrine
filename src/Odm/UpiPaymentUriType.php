<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\India\UpiPaymentUri;
use Override;

final class UpiPaymentUriType extends AbstractStringVoType
{
    public const string NAME = 'fin_upi_payment_uri';

    #[Override]
    protected function valueObjectClass(): string
    {
        return UpiPaymentUri::class;
    }
}
