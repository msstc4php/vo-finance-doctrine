<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractTextVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\India\UpiPaymentUri;
use Override;

final class UpiPaymentUriType extends AbstractTextVoType
{
    public const string NAME = 'fin_upi_payment_uri';

    #[Override]
    protected function valueObjectClass(): string
    {
        return UpiPaymentUri::class;
    }
}
