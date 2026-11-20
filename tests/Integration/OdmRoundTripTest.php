<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Integration;

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AttributeDriver;
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception as MongoException;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\MerchantPresentedQrType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\PaymentCardNumberType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\QrBillType;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Odm\TypeRegistrar;
use MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support\Odm\Payment;
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
final class OdmRoundTripTest extends TestCase
{
    private const string DATABASE = 'vo_finance_doctrine_test';

    private DocumentManager $documentManager;

    private Client $client;

    private bool $connected = false;

    #[Override]
    protected function setUp(): void
    {
        $uri = \getenv('MONGODB_URI');
        $server = $uri !== false && $uri !== '' ? $uri : 'mongodb://127.0.0.1:27017';
        $this->client = new Client($server, ['serverSelectionTimeoutMS' => 1000]);

        try {
            $this->client->selectDatabase('admin')->command(['ping' => 1]);
        } catch (MongoException) {
            self::markTestSkipped('MongoDB is not reachable; set MONGODB_URI to run the ODM integration test.');
        }

        $this->connected = true;
        TypeRegistrar::registerAll();
        $this->client->dropDatabase(self::DATABASE);

        $config = new Configuration();
        $config->setDefaultDB(self::DATABASE);
        $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/../Support/Odm']));
        $config->setProxyDir(\sys_get_temp_dir() . '/fin-proxies');
        $config->setProxyNamespace('FinProxies');
        $config->setHydratorDir(\sys_get_temp_dir() . '/fin-hydrators');
        $config->setHydratorNamespace('FinHydrators');
        $config->setAutoGenerateProxyClasses(Configuration::AUTOGENERATE_EVAL);
        $config->setAutoGenerateHydratorClasses(Configuration::AUTOGENERATE_EVAL);

        $this->documentManager = DocumentManager::create($this->client, $config);
    }

    #[Override]
    protected function tearDown(): void
    {
        if ($this->connected) {
            $this->client->dropDatabase(self::DATABASE);
        }
    }

    public function testSwissQrBillWithCrlfSurvivesAFlushAndReload(): void
    {
        $qrBill = Samples::qrBillWithCrlf();
        $this->documentManager->persist(new Payment(PaymentCardNumber::from('4111 1111 1111 1111'), $qrBill));
        $this->documentManager->flush();
        $this->documentManager->clear();

        $payment = $this->documentManager->getRepository(Payment::class)->findOneBy([]);
        $raw = $this->client->selectCollection(self::DATABASE, 'payment')->findOne([], ['typeMap' => ['root' => 'array', 'document' => 'array']]);

        self::assertIsArray($raw);
        self::assertSame((string) $qrBill, $raw['qrBill']);
        self::assertStringContainsString("\r\n", $raw['qrBill']);
        self::assertInstanceOf(Payment::class, $payment);
        self::assertNotNull($payment->qrBill);
        self::assertTrue($qrBill->equals($payment->qrBill));
        self::assertSame('CH6431961000004421557', $payment->qrBill->iban()->value);
        self::assertNull($payment->emvco);
    }

    public function testEmvcoPayloadAndCardNumberAreStoredCanonically(): void
    {
        $emvco = MerchantPresentedQr::from(Samples::EMVCO_STATIC);
        $this->documentManager->persist(new Payment(PaymentCardNumber::from('4111 1111 1111 1111'), null, $emvco));
        $this->documentManager->flush();
        $this->documentManager->clear();

        $raw = $this->client->selectCollection(self::DATABASE, 'payment')->findOne([], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
        $payment = $this->documentManager->getRepository(Payment::class)->findOneBy([]);

        self::assertIsArray($raw);
        self::assertSame('4111111111111111', $raw['card']);
        self::assertSame(Samples::EMVCO_STATIC, $raw['emvco']);
        self::assertInstanceOf(Payment::class, $payment);
        self::assertSame('4111111111111111', (string) $payment->card);
        self::assertNotNull($payment->emvco);
        self::assertTrue($emvco->equals($payment->emvco));
        self::assertNull($payment->qrBill);
    }
}
