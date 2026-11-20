<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Ru\SbpPaymentLink;
use Override;

final class SbpPaymentLinkType extends AbstractStringVoType
{
    public const string NAME = 'fin_sbp_payment_link';

    #[Override]
    protected function valueObjectClass(): string
    {
        return SbpPaymentLink::class;
    }
}
