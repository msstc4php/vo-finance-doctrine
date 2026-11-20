<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Odm;

use MSSTC4PHP\ValueObject\Doctrine\Odm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Ru\Gost56042Payload;
use Override;

final class Gost56042PayloadType extends AbstractStringVoType
{
    public const string NAME = 'fin_gost56042_payload';

    #[Override]
    protected function valueObjectClass(): string
    {
        return Gost56042Payload::class;
    }
}
