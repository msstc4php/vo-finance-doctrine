<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractTextVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use Override;

final class MerchantPresentedQrType extends AbstractTextVoType
{
    public const string NAME = 'fin_merchant_presented_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return MerchantPresentedQr::class;
    }
}
