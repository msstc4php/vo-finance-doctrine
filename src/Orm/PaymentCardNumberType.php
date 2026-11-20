<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Financial\Doctrine\Orm;

use MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType;
use MSSTC4PHP\ValueObject\Financial\Card\PaymentCardNumber;
use Override;

/**
 * Stores the full PAN in clear text. PCI DSS 4.0 requirement 3.5.1 requires a stored PAN to be unreadable; encryption
 * at rest is the application's job, so use this type only inside a PCI-scoped, encrypted store, and prefer a token or
 * the `masked()` / `last4()` form elsewhere.
 *
 * @see https://www.pcisecuritystandards.org/document_library/ PCI SSC document library: PCI DSS 4.0, requirement 3.5.1
 */
final class PaymentCardNumberType extends AbstractStringVoType
{
    public const string NAME = 'fin_payment_card_number';

    #[Override]
    protected function valueObjectClass(): string
    {
        return PaymentCardNumber::class;
    }

    #[Override]
    protected function columnLength(): int
    {
        return 19;
    }
}
