<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Thailand\PromptPayQr;
use Override;

final class PromptPayQrType extends AbstractStringVoType
{
    public const string NAME = 'fin_prompt_pay_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PromptPayQr::class;
    }
}
