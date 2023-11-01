<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Bundle\ApiBundle\Command\Payment\PayPayment;
use Sylius\Component\Core\Model\PaymentInterface;

/** @experimental */
interface PayPaymentHandlerInterface
{
    public function supports(PaymentInterface $payment): bool;

    public function handle(PaymentInterface $payment, PayPayment $payPayment): void;
}
