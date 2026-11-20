<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Tests\Support;

use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Brazil\PixBrCode;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Cambodia\KhqrCode;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Emvco\MerchantPresentedQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Epc\EpcQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\India\UpiPaymentUri;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Japan\JpqrCode;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Malaysia\DuitNowQr;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Ru\Gost56042Payload;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Ru\SbpPaymentLink;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Swiss\QrBill;
use MSSTC4PHP\ValueObject\Financial\PaymentQr\Thailand\PromptPayQr;
use MSSTC4PHP\ValueObject\Financial\Reference\CreditorReference;
use MSSTC4PHP\ValueObject\Financial\Reference\QrReference;
use MSSTC4PHP\ValueObject\Financial\Security\Cfi;
use MSSTC4PHP\ValueObject\Financial\Security\Cusip;
use MSSTC4PHP\ValueObject\Financial\Security\Figi;
use MSSTC4PHP\ValueObject\Financial\Security\Fisn;
use MSSTC4PHP\ValueObject\Financial\Security\Isin;
use MSSTC4PHP\ValueObject\Financial\Security\Lei;
use MSSTC4PHP\ValueObject\Financial\Security\Mic;
use MSSTC4PHP\ValueObject\Financial\Security\Sedol;
use MSSTC4PHP\ValueObject\Financial\Security\Upi;
use MSSTC4PHP\ValueObject\Financial\Security\Valor;
use MSSTC4PHP\ValueObject\Financial\Security\Wkn;

/**
 * A canonical sample, the column length and a stored value that must be refused, per type name.
 */
final class Samples
{
    public const string BAD_STORED = 'zz9qq!';

    /** @var non-empty-list<non-empty-string> types without a bounded length, or above 4000 characters, map to CLOB/TEXT */
    public const array TEXT_TYPES = [
        'fin_merchant_presented_qr',
        'fin_pix_br_code',
        'fin_prompt_pay_qr',
        'fin_duit_now_qr',
        'fin_khqr_code',
        'fin_gost56042_payload',
        'fin_sbp_payment_link',
        'fin_upi_payment_uri',
    ];

    public const string QR_BILL_ANNEX_A_1 = 'SPC|0200|1|CH6431961000004421557|S|Max Muster & Söhne (sample company)|Musterstrasse|123|8000|Seldwyla|CH|||||||'
        . '|50.00|CHF|S|Simon Muster|Musterstrasse|1|8000|Seldwyla|CH|QRR|000008207791225857421286694|Payment of travel|EPD|||';

    public const string EMVCO_STATIC = '0002010412CARDACCOUNT15204541153039865802BR5904Shop6004Rome63043402';

    /**
     * The Swiss QR-bill of SIX IG 2.3 Annex A example 1 with CRLF separators, the form that must survive storage.
     */
    public static function qrBillWithCrlf(): QrBill
    {
        return QrBill::from(\str_replace('|', "\r\n", self::QR_BILL_ANNEX_A_1));
    }

    /**
     * Every element at its Table 8 maximum, CRLF separators: the longest accepted QR-bill.
     */
    public static function longestQrBill(): QrBill
    {
        $address = ['S', \str_repeat('N', 70), \str_repeat('S', 70), \str_repeat('B', 16), \str_repeat('P', 16), \str_repeat('T', 35), 'CH'];

        return QrBill::from(\implode("\r\n", [
            'SPC', '0200', '1', 'CH5204835012345671000', ...$address, ...\array_fill(0, 7, ''), '999999999.99', 'CHF', ...$address,
            'SCOR', 'RF95ABCDEFGHIJKLMNOPQRSTU', \str_repeat('m', 140), 'EPD', '', \str_repeat('a', 100), \str_repeat('b', 100),
        ]));
    }

    /**
     * Per type: the type name, a canonical sample, the column length (0 for a text type without a bound), an invalid
     * stored value and whether the sample has the longest accepted length (then one more character must be refused).
     *
     * @return iterable<string, array{non-empty-string, StringConvertible, non-negative-int, non-empty-string, bool}>
     */
    public static function strings(): iterable
    {
        yield 'isin' => ['fin_isin', Isin::from('US0378331005'), 12, self::BAD_STORED, true];
        yield 'upi' => ['fin_upi', Upi::from('QZ12345678BC'), 12, self::BAD_STORED, true];
        yield 'figi' => ['fin_figi', Figi::from('BBG000BLNQ16'), 12, self::BAD_STORED, true];
        yield 'cusip' => ['fin_cusip', Cusip::from('037833100'), 9, self::BAD_STORED, true];
        yield 'fisn' => ['fin_fisn', Fisn::from('ABCDEFGHIJKLMNOPQRSTUVWXYZ/ABCDEFGH'), 35, self::BAD_STORED, true];
        yield 'mic' => ['fin_mic', Mic::from('XNAS'), 4, self::BAD_STORED, true];
        yield 'sedol' => ['fin_sedol', Sedol::from('0263494'), 7, self::BAD_STORED, true];
        yield 'wkn' => ['fin_wkn', Wkn::from('716460'), 6, self::BAD_STORED, true];
        yield 'valor' => ['fin_valor', Valor::from('123456789'), 9, self::BAD_STORED, true];
        yield 'cfi' => ['fin_cfi', Cfi::from('ESVUFR'), 6, self::BAD_STORED, true];
        yield 'lei' => ['fin_lei', Lei::from('F50EOCWSQFAUVO9Q8Z97'), 20, self::BAD_STORED, true];
        yield 'payment card number' => ['fin_payment_card_number', PaymentCardNumber::from('4111111111111111110'), 19, self::BAD_STORED, true];
        yield 'qr reference' => ['fin_qr_reference', QrReference::from('210000000003139471430009017'), 27, self::BAD_STORED, true];
        yield 'creditor reference' => ['fin_creditor_reference', CreditorReference::from('RF95ABCDEFGHIJKLMNOPQRSTU'), 25, self::BAD_STORED, true];
        yield 'epc qr' => [
            'fin_epc_qr',
            EpcQr::from("BCD\n001\n1\nSCT\nBHBLDEHHXXX\nFranz Mustermänn\nDE71110220330123456789\nEUR12.3\nGDDS\nRF18539007547034"),
            331,
            self::BAD_STORED,
            false,
        ];
        yield 'jpqr code' => [
            'fin_jpqr_code',
            JpqrCode::from('00020101021126680019jp.or.paymentsjapan0113123456789012302040001030600000204060000035204581253033925802JP5913CASHLESS CAFE6009MINATO-KU6107105000164220002JA0112キャッシュレス推進協議会63041A67'),
            512,
            self::BAD_STORED,
            false,
        ];
        yield 'qr bill' => ['fin_qr_bill', self::qrBillWithCrlf(), 997, self::BAD_STORED, false];
        yield 'longest qr bill' => ['fin_qr_bill', self::longestQrBill(), 997, self::BAD_STORED, false];
        yield 'merchant presented qr' => ['fin_merchant_presented_qr', MerchantPresentedQr::from(self::EMVCO_STATIC), 7089, self::BAD_STORED, false];
        yield 'pix br code' => [
            'fin_pix_br_code',
            PixBrCode::from('00020126580014br.gov.bcb.pix0136123e4567-e12b-12d1-a456-4266554400005204000053039865802BR5913Fulano de Tal6008BRASILIA62070503***63041D3D'),
            7089,
            self::BAD_STORED,
            false,
        ];
        yield 'prompt pay qr' => [
            'fin_prompt_pay_qr',
            PromptPayQr::from('00020101021129370016A0000006770101110113006681234567853037645406100.505802TH5904Shop6304B040'),
            7089,
            self::BAD_STORED,
            false,
        ];
        yield 'duit now qr' => [
            'fin_duit_now_qr',
            DuitNowQr::from('00020201021126410014A000000615000101065016640209123456789520499995303458540510.005802MY5909QRCSDNBHD6005BANGI6304343F'),
            7089,
            self::BAD_STORED,
            false,
        ];
        yield 'khqr code' => [
            'fin_khqr_code',
            KhqrCode::from('00020101021129200016test_bakong@devb5204599953031165802KH5917Lucky Supermarket6010Phnom Penh9917001316339210247786304102F'),
            7089,
            self::BAD_STORED,
            false,
        ];
        yield 'gost 56042 payload' => [
            'fin_gost56042_payload',
            Gost56042Payload::from('ST00012|Name=ООО "Три кита"|PersonalAcc=40702810138250123017|BankName=ОАО "БАНК"|BIC=044525225|CorrespAcc=30101810400000000225|PayeeINN=6200098765|Sum=100000'),
            0,
            self::BAD_STORED,
            false,
        ];
        yield 'sbp payment link' => [
            'fin_sbp_payment_link',
            SbpPaymentLink::from('https://qr.nspk.ru/AS1000670LSS7DN18SJQDNP4B05KLJL2?type=01&bank=100000000001&crc=C08B'),
            0,
            self::BAD_STORED,
            false,
        ];
        yield 'upi payment uri' => [
            'fin_upi_payment_uri',
            UpiPaymentUri::from('upi://pay?pa=merchant@bank&pn=TestMerchant&tr=ORDER123&am=10.50&cu=INR'),
            0,
            self::BAD_STORED,
            false,
        ];
    }

    /**
     * @return iterable<string, array{non-empty-string}>
     */
    public static function stringNames(): iterable
    {
        foreach (self::strings() as $label => [$name]) {
            yield $label => [$name];
        }
    }

    /**
     * @return iterable<string, array{non-empty-string, StringConvertible}>
     */
    public static function stringPairs(): iterable
    {
        foreach (self::strings() as $label => [$name, $vo]) {
            yield $label => [$name, $vo];
        }
    }

    /**
     * @return iterable<string, array{non-empty-string, StringConvertible, non-negative-int, bool}>
     */
    public static function stringLengths(): iterable
    {
        foreach (self::strings() as $label => [$name, $vo, $length, , $atMax]) {
            yield $label => [$name, $vo, $length, $atMax];
        }
    }

    /**
     * @return iterable<string, array{non-empty-string, non-empty-string}>
     */
    public static function stringInvalids(): iterable
    {
        foreach (self::strings() as $label => [$name, , , $invalid]) {
            yield $label => [$name, $invalid];
        }
    }
}
