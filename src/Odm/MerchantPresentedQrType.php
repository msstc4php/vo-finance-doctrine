<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use Override;

final class MerchantPresentedQrType extends AbstractStringVoType
{
    public const string NAME = 'fin_merchant_presented_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return MerchantPresentedQr::class;
    }
}
