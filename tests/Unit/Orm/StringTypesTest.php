<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Unit\Orm;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\CfiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\CreditorReferenceType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\CusipType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\DuitNowQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\EpcQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\FigiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\FisnType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\Gost56042PayloadType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\IsinType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\JpqrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\KhqrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\LeiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\MicType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\PixBrCodeType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\PromptPayQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\QrReferenceType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\SbpPaymentLinkType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\SedolType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\UpiPaymentUriType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\UpiType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\ValorType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\WknType;
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
    private SQLitePlatform $platform;

    #[Override]
    protected function setUp(): void
    {
        TypeRegistrar::registerAll();
        $this->platform = new SQLitePlatform();
    }

    #[DataProviderExternal(Samples::class, 'stringPairs')]
    public function testRoundTripsTheCanonicalForm(string $name, StringConvertible $vo): void
    {
        $type = Type::getType($name);

        $stored = $type->convertToDatabaseValue($vo, $this->platform);
        self::assertSame((string) $vo, $stored);

        $restored = $type->convertToPHPValue($stored, $this->platform);
        self::assertInstanceOf($vo::class, $restored);
        self::assertSame((string) $vo, (string) $restored);
    }

    #[DataProviderExternal(Samples::class, 'stringLengths')]
    public function testColumnFitsTheLongestAcceptedForm(string $name, StringConvertible $vo, int $length, bool $atMax): void
    {
        $isText = \in_array($name, Samples::TEXT_TYPES, true);
        self::assertSame(
            $isText ? $this->platform->getClobTypeDeclarationSQL([]) : \sprintf('VARCHAR(%d)', $length),
            Type::getType($name)->getSQLDeclaration([], $this->platform),
        );
        if ($length > 0) {
            self::assertLessThanOrEqual($length, \mb_strlen((string) $vo));
        }

        if (!$atMax) {
            return;
        }

        self::assertSame($length, \mb_strlen((string) $vo));
        $this->expectException(InvalidValueObjectException::class);
        $vo::from($vo . \mb_substr((string) $vo, -1));
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testNullRoundTrips(string $name): void
    {
        $type = Type::getType($name);

        self::assertNull($type->convertToDatabaseValue(null, $this->platform));
        self::assertNull($type->convertToPHPValue(null, $this->platform));
    }

    #[DataProviderExternal(Samples::class, 'stringInvalids')]
    public function testInvalidStoredValueThrowsWithoutEchoingIt(string $name, string $invalid): void
    {
        try {
            Type::getType($name)->convertToPHPValue($invalid, $this->platform);
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
            Type::getType($name)->convertToDatabaseValue($foreign, $this->platform);
            self::fail('A value object of another class was written.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame(\sprintf('Expected %s, got %s.', $vo::class, $foreign::class), $exception->getMessage());
        }
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testRefusesAPlainStringOnWrite(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);

        Type::getType($name)->convertToDatabaseValue('US0378331005', $this->platform);
    }

    #[DataProviderExternal(Samples::class, 'stringNames')]
    public function testRefusesANonStringColumnValue(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);

        Type::getType($name)->convertToPHPValue(123, $this->platform);
    }
}
