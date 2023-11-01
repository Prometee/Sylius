<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler\Payment;

use Sylius\Bundle\ApiBundle\Command\Payment\PayPayment;
use Sylius\Bundle\ApiBundle\Provider\CompositePayPaymentProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class PayPaymentHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CompositePayPaymentProviderInterface $compositePayPaymentProvider,
    ) {
    }

    public function __invoke(PayPayment $payPayment): JsonResponse
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($payPayment->getPaymentId());
        Assert::notNull($payment, 'Payment has not been found.');

        $this->compositePayPaymentProvider->provide($payment, $payPayment);

        return new JsonResponse($payPayment->getReturn());
    }
}
