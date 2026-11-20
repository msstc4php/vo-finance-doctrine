<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Odm;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Swiss\QrBill;

#[ODM\Document(collection: 'payment')]
class Payment
{
    #[ODM\Id]
    public ?string $id = null;

    public function __construct(
        #[ODM\Field(type: PaymentCardNumberType::NAME)]
        public PaymentCardNumber $card,
        #[ODM\Field(type: QrBillType::NAME, nullable: true)]
        public ?QrBill $qrBill = null,
        #[ODM\Field(type: MerchantPresentedQrType::NAME, nullable: true)]
        public ?MerchantPresentedQr $emvco = null,
    ) {
    }
}
