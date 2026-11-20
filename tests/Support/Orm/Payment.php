<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Orm;

use Doctrine\ORM\Mapping as ORM;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Swiss\QrBill;

#[ORM\Entity]
#[ORM\Table(name: 'payment')]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    public function __construct(
        #[ORM\Column(type: PaymentCardNumberType::NAME)]
        public PaymentCardNumber $card,
        #[ORM\Column(type: QrBillType::NAME, nullable: true)]
        public ?QrBill $qrBill = null,
        #[ORM\Column(type: MerchantPresentedQrType::NAME, nullable: true)]
        public ?MerchantPresentedQr $emvco = null,
    ) {
    }
}
