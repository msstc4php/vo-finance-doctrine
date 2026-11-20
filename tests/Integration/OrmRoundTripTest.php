<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Integration;

use Composer\InstalledVersions;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Orm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Orm\Payment;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Samples;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaymentCardNumberType::class)]
#[CoversClass(QrBillType::class)]
#[CoversClass(MerchantPresentedQrType::class)]
#[UsesClass(TypeRegistrar::class)]
final class OrmRoundTripTest extends TestCase
{
    private Connection $connection;

    private EntityManager $entityManager;

    #[Override]
    protected function setUp(): void
    {
        TypeRegistrar::registerAll();

        $this->connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $config = new Configuration();
        $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/../Support/Orm']));
        $config->setProxyDir(\sys_get_temp_dir() . '/fin-proxies');
        $config->setProxyNamespace('FinProxies');
        // ORM 3.3 (the lowest supported) has no native lazy objects and falls back to its own proxies.
        if (\version_compare(InstalledVersions::getVersion('doctrine/orm') ?? '0', '3.4.0', '>=')) {
            $config->enableNativeLazyObjects(true);
        }

        $this->entityManager = new EntityManager($this->connection, $config);
        new SchemaTool($this->entityManager)->createSchema([$this->entityManager->getClassMetadata(Payment::class)]);
    }

    public function testSchemaDeclaresTheColumnLengthsOfTheClasses(): void
    {
        $sql = \implode("\n", new SchemaTool($this->entityManager)->getCreateSchemaSql(
            [$this->entityManager->getClassMetadata(Payment::class)],
        ));

        self::assertStringContainsString('card VARCHAR(19) NOT NULL', $sql);
        self::assertStringContainsString('qrBill VARCHAR(997) DEFAULT NULL', $sql);
        self::assertStringContainsString('emvco CLOB DEFAULT NULL', $sql);
    }

    public function testSwissQrBillWithCrlfSurvivesAFlushAndReload(): void
    {
        $qrBill = Samples::qrBillWithCrlf();
        $this->entityManager->persist(new Payment(PaymentCardNumber::from('4111 1111 1111 1111'), $qrBill));
        $this->entityManager->flush();
        $this->entityManager->clear();

        $payment = $this->entityManager->getRepository(Payment::class)->findOneBy([]);

        $stored = $this->connection->fetchOne('SELECT qrBill FROM payment');
        self::assertIsString($stored);
        self::assertSame((string) $qrBill, $stored);
        self::assertStringContainsString("\r\n", $stored);
        self::assertInstanceOf(Payment::class, $payment);
        self::assertNotNull($payment->qrBill);
        self::assertTrue($qrBill->equals($payment->qrBill));
        self::assertSame('CH6431961000004421557', $payment->qrBill->iban()->value);
        self::assertNull($payment->emvco);
    }

    public function testTheLongestQrBillFitsItsColumn(): void
    {
        $qrBill = Samples::longestQrBill();
        $this->entityManager->persist(new Payment(PaymentCardNumber::from('4111111111111111'), $qrBill));
        $this->entityManager->flush();
        $this->entityManager->clear();

        $payment = $this->entityManager->getRepository(Payment::class)->findOneBy([]);

        self::assertInstanceOf(Payment::class, $payment);
        self::assertNotNull($payment->qrBill);
        self::assertTrue($qrBill->equals($payment->qrBill));
    }

    public function testEmvcoPayloadAndCardNumberAreStoredCanonically(): void
    {
        $emvco = MerchantPresentedQr::from(Samples::EMVCO_STATIC);
        $this->entityManager->persist(new Payment(PaymentCardNumber::from('4111 1111 1111 1111'), null, $emvco));
        $this->entityManager->flush();
        $this->entityManager->clear();

        $row = $this->connection->fetchAssociative('SELECT card, emvco FROM payment');
        $payment = $this->entityManager->getRepository(Payment::class)->findOneBy([]);

        self::assertIsArray($row);
        self::assertSame('4111111111111111', $row['card']);
        self::assertSame(Samples::EMVCO_STATIC, $row['emvco']);
        self::assertInstanceOf(Payment::class, $payment);
        self::assertSame('4111111111111111', (string) $payment->card);
        self::assertNotNull($payment->emvco);
        self::assertTrue($emvco->equals($payment->emvco));
        self::assertNull($payment->qrBill);
    }
}
