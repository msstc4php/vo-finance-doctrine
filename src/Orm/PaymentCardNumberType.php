<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
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

    #[Override]
    protected function columnLength(): int
    {
        return 19;
    }
}
