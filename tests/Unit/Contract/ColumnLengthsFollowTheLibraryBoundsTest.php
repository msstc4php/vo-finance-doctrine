<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Unit\Contract;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;
use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Samples;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Swiss\QrBill;
use MSSTC4PHP\ValueObject\Financial\Security\Valor;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every ORM column follows the public `MAX_LENGTH` of its value object, the stored-value bound vo-finance freezes for
 * 1.x: `VARCHAR(MAX_LENGTH)` up to 4000 characters, a CLOB above (SQL Server NVARCHAR and Oracle VARCHAR2 stop at 4000).
 */
#[CoversNothing]
final class ColumnLengthsFollowTheLibraryBoundsTest extends TestCase
{
    private const int VARCHAR_LIMIT = 4000;

    /**
     * @return iterable<string, array{non-empty-string, StringConvertible}>
     */
    public static function types(): iterable
    {
        foreach (Samples::strings() as $label => [$name, $sample]) {
            yield $label => [$name, $sample];
        }
    }

    #[DataProvider('types')]
    public function testTheColumnIsTheMaxLengthOfTheValueObject(string $name, StringConvertible $sample): void
    {
        TypeRegistrar::registerAll();
        $platform = new SQLitePlatform();
        $bound = \constant($sample::class . '::MAX_LENGTH');
        self::assertIsInt($bound);

        $expected = $bound > self::VARCHAR_LIMIT ? $platform->getClobTypeDeclarationSQL([]) : \sprintf('VARCHAR(%d)', $bound);

        self::assertSame($expected, Type::getType($name)->getSQLDeclaration([], $platform));
    }

    public function testEveryOrmTypeHasASample(): void
    {
        $sampled = [];
        foreach (Samples::strings() as [$name]) {
            $sampled[$name] = true;
        }

        self::assertEqualsCanonicalizing(\array_keys(TypeRegistrar::types()), \array_keys($sampled));
    }

    public function testTheValorColumnLeavesRoomAboveTheAcceptedNineCharacters(): void
    {
        TypeRegistrar::registerAll();

        self::assertSame('VARCHAR(12)', Type::getType('fin_valor')->getSQLDeclaration([], new SQLitePlatform()));
        self::assertNull(Valor::tryFrom('1234567890'));
    }

    public function testTheLongestTable8QrBillIs902CharactersAndFitsTheColumn(): void
    {
        self::assertSame(902, \strlen(Samples::longestQrBill()->value));
        self::assertLessThanOrEqual(QrBill::MAX_LENGTH, \strlen(Samples::longestQrBill()->value));
    }
}
