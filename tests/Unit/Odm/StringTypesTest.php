<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Unit\Odm;

use Doctrine\ODM\MongoDB\Types\Type;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\CfiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\CreditorReferenceType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\CusipType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\DuitNowQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\EpcQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\FigiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\FisnType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\Gost56042PayloadType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\IsinType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\JpqrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\KhqrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\LeiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\MicType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\PixBrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\PromptPayQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\QrReferenceType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\SbpPaymentLinkType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\SedolType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\UpiPaymentUriType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\UpiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\ValorType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\WknType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Samples;
use MSSTC4PHP\ValueObject\Financial\Security\Isin;
use MSSTC4PHP\ValueObject\Financial\Security\Mic;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsinType::class)]
#[CoversClass(UpiType::class)]
#[CoversClass(FigiType::class)]
#[CoversClass(CusipType::class)]
#[CoversClass(FisnType::class)]
#[CoversClass(MicType::class)]
#[CoversClass(SedolType::class)]
#[CoversClass(WknType::class)]
#[CoversClass(ValorType::class)]
#[CoversClass(CfiType::class)]
#[CoversClass(LeiType::class)]
#[CoversClass(PaymentCardNumberType::class)]
#[CoversClass(QrReferenceType::class)]
#[CoversClass(CreditorReferenceType::class)]
#[CoversClass(EpcQrType::class)]
#[CoversClass(JpqrCodeType::class)]
#[CoversClass(QrBillType::class)]
#[CoversClass(MerchantPresentedQrType::class)]
#[CoversClass(PixBrCodeType::class)]
#[CoversClass(PromptPayQrType::class)]
#[CoversClass(DuitNowQrType::class)]
#[CoversClass(KhqrCodeType::class)]
#[CoversClass(Gost56042PayloadType::class)]
#[CoversClass(SbpPaymentLinkType::class)]
#[CoversClass(UpiPaymentUriType::class)]
#[UsesClass(TypeRegistrar::class)]
final class StringTypesTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        TypeRegistrar::registerAll();
    }

    #[DataProviderExternal(Samples::class, 'stringPairs')]
    public function testRoundTripsTheCanonicalForm(string $name, StringConvertible $vo): void
    {
        $type = Type::getType($name);

        $stored = $type->convertToDatabaseValue($vo);
        self::assertSame((string) $vo, $stored);

        $restored = $type->convertToPHPValue($stored);
        self::assertInstanceOf($vo::class, $restored);
        self::assertSame((string) $vo, (string) $restored);
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testNullRoundTrips(string $name): void
    {
        $type = Type::getType($name);

        self::assertNull($type->convertToDatabaseValue(null));
        self::assertNull($type->convertToPHPValue(null));
    }

    #[DataProviderExternal(Samples::class, 'stringInvalids')]
    public function testInvalidStoredValueThrowsWithoutEchoingIt(string $name, string $invalid): void
    {
        try {
            Type::getType($name)->convertToPHPValue($invalid);
            self::fail('An invalid stored value was accepted.');
        } catch (InvalidValueObjectException $exception) {
            self::assertStringNotContainsString($invalid, $exception->getMessage());
        }
    }

    #[DataProviderExternal(Samples::class, 'stringPairs')]
    public function testRefusesAValueObjectOfAnotherClassOnWrite(string $name, StringConvertible $vo): void
    {
        $foreign = $vo instanceof Isin ? Mic::from('XNAS') : Isin::from('US0378331005');

        try {
            Type::getType($name)->convertToDatabaseValue($foreign);
            self::fail('A value object of another class was written.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame(\sprintf('Expected %s, got %s.', $vo::class, $foreign::class), $exception->getMessage());
        }
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testRefusesAPlainStringOnWrite(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);

        Type::getType($name)->convertToDatabaseValue('US0378331005');
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testRefusesANonStringColumnValue(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);

        Type::getType($name)->convertToPHPValue(123);
    }
}
