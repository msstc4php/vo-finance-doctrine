<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use Override;

final class PaymentCardNumberType extends AbstractStringVoType
{
    public const string NAME = 'fin_payment_card_number';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PaymentCardNumber::class;
    }
}
