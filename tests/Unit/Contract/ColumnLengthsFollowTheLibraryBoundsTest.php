<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Unit\Contract;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Samples;
use MSSTC4PHP\ValueObject\Financial\Internal\EmvcoTlv;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Epc\EpcQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Japan\JpqrCode;
use MSSTC4PHP\ValueObject\Financial\Security\Fisn;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;

#[CoversNothing]
final class ColumnLengthsFollowTheLibraryBoundsTest extends TestCase
{
    private const int VARCHAR_LIMIT = 4000;

    /**
     * Fixed-width identifiers and the references have no constant in the library; the at-max samples of the string type
     * tests prove them. The QR-bill is not a constant either: 997 is SIX IG 2.3 section 6.2 (quoted in FINANCE.md), and
     * the string type tests store the longest payload the element limits of Table 8 allow (902 characters).
     *
     * @return iterable<string, array{non-empty-string, int}>
     */
    public static function bounds(): iterable
    {
        yield 'fisn' => ['fin_fisn', self::constant(Fisn::class, 'MAX_LENGTH')];
        yield 'epc qr' => ['fin_epc_qr', self::constant(EpcQr::class, 'MAX_LENGTH')];
        yield 'jpqr code' => ['fin_jpqr_code', self::constant(JpqrCode::class, 'MAX_BYTES')];
        yield 'qr bill' => ['fin_qr_bill', 997];
    }

    #[DataProvider('bounds')]
    public function testDeclaredLengthEqualsTheLibraryBound(string $name, int $bound): void
    {
        TypeRegistrar::registerAll();

        self::assertSame(
            \sprintf('VARCHAR(%d)', $bound),
            Type::getType($name)->getSQLDeclaration([], new SQLitePlatform()),
        );
        // SQL Server NVARCHAR and Oracle VARCHAR2 stop at 4000.
        self::assertLessThanOrEqual(self::VARCHAR_LIMIT, $bound);
    }

    public function testTheLongestTable8QrBillIs902CharactersAndFitsTheColumn(): void
    {
        TypeRegistrar::registerAll();
        $length = \strlen(Samples::longestQrBill()->value);

        self::assertSame(902, $length);
        self::assertSame('VARCHAR(997)', Type::getType('fin_qr_bill')->getSQLDeclaration([], new SQLitePlatform()));
    }

    public function testAnEmvcoPayloadBoundAboveTheVarcharLimitMapsToAClob(): void
    {
        TypeRegistrar::registerAll();
        $platform = new SQLitePlatform();
        $bound = self::constant(EmvcoTlv::class, 'MAX_PAYLOAD_LENGTH');

        self::assertGreaterThan(self::VARCHAR_LIMIT, $bound);
        foreach (['fin_merchant_presented_qr', 'fin_pix_br_code', 'fin_prompt_pay_qr', 'fin_duit_now_qr', 'fin_khqr_code'] as $name) {
            self::assertSame($platform->getClobTypeDeclarationSQL([]), Type::getType($name)->getSQLDeclaration([], $platform), $name);
        }
    }

    /**
     * No bound exists in the library for these, so no length is safe: a CLOB never refuses a valid value.
     */
    public function testUnboundedPayloadsMapToAClob(): void
    {
        TypeRegistrar::registerAll();
        $platform = new SQLitePlatform();

        foreach (['fin_gost56042_payload', 'fin_sbp_payment_link', 'fin_upi_payment_uri'] as $name) {
            self::assertSame($platform->getClobTypeDeclarationSQL([]), Type::getType($name)->getSQLDeclaration([], $platform), $name);
        }
    }

    public function testEveryOtherOrmTypeIsBounded(): void
    {
        $listed = [
            ...\array_column(\iterator_to_array(self::bounds()), 0),
            'fin_merchant_presented_qr', 'fin_pix_br_code', 'fin_prompt_pay_qr', 'fin_duit_now_qr', 'fin_khqr_code',
            'fin_gost56042_payload', 'fin_sbp_payment_link', 'fin_upi_payment_uri',
        ];
        $unlisted = \array_diff(\array_keys(TypeRegistrar::types()), $listed);
        TypeRegistrar::registerAll();

        self::assertNotSame([], $unlisted);
        foreach ($unlisted as $name) {
            $declaration = Type::getType($name)->getSQLDeclaration([], new SQLitePlatform());
            self::assertMatchesRegularExpression('/^VARCHAR\((\d{1,3}|[1-3]\d{3}|4000)\)$/', $declaration, $name);
        }
    }

    /**
     * @param class-string $class
     */
    private static function constant(string $class, string $name): int
    {
        $value = new ReflectionClassConstant($class, $name)->getValue();
        self::assertIsInt($value);

        return $value;
    }
}
