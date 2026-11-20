<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use Doctrine\DBAL\Types\Type;

/**
 * Registers every finance value-object type. Call once at bootstrap for bare Doctrine; under Symfony map {@see types()}
 * under `doctrine.dbal.types` instead. Registration is idempotent, so it is safe in long-running workers.
 */
final class TypeRegistrar
{
    /**
     * @var array<non-empty-string, class-string<Type>>
     */
    private const array TYPES = [
        IsinType::NAME => IsinType::class,
        UpiType::NAME => UpiType::class,
        FigiType::NAME => FigiType::class,
        CusipType::NAME => CusipType::class,
        FisnType::NAME => FisnType::class,
        MicType::NAME => MicType::class,
        SedolType::NAME => SedolType::class,
        WknType::NAME => WknType::class,
        ValorType::NAME => ValorType::class,
        CfiType::NAME => CfiType::class,
        LeiType::NAME => LeiType::class,
        PaymentCardNumberType::NAME => PaymentCardNumberType::class,
        QrReferenceType::NAME => QrReferenceType::class,
        CreditorReferenceType::NAME => CreditorReferenceType::class,
        EpcQrType::NAME => EpcQrType::class,
        JpqrCodeType::NAME => JpqrCodeType::class,
        QrBillType::NAME => QrBillType::class,
        MerchantPresentedQrType::NAME => MerchantPresentedQrType::class,
        PixBrCodeType::NAME => PixBrCodeType::class,
        PromptPayQrType::NAME => PromptPayQrType::class,
        DuitNowQrType::NAME => DuitNowQrType::class,
        KhqrCodeType::NAME => KhqrCodeType::class,
        Gost56042PayloadType::NAME => Gost56042PayloadType::class,
        SbpPaymentLinkType::NAME => SbpPaymentLinkType::class,
        UpiPaymentUriType::NAME => UpiPaymentUriType::class,
    ];

    public static function registerAll(): void
    {
        foreach (self::types() as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }
    }

    /**
     * @return array<non-empty-string, class-string<Type>>
     */
    public static function types(): array
    {
        return self::TYPES;
    }
}
