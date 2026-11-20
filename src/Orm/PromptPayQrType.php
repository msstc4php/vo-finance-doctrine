<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractTextVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Thailand\PromptPayQr;
use Override;

final class PromptPayQrType extends AbstractTextVoType
{
    public const string NAME = 'fin_prompt_pay_qr';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PromptPayQr::class;
    }
}
