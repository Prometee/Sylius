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

use Payum\Core\Bridge\Symfony\Reply\HttpResponse as SymfonyHttpResponse;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\ApiBundle\Command\Payment\PayPayment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

/** @experimental */
final class PayumPayPaymentHandler implements PayPaymentHandlerInterface
{
    public function __construct(
        private Payum $payum,
    ) {
    }

    public function supports(PaymentInterface $payment): bool
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        if (null === $paymentMethod) {
            return false;
        }

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            return false;
        }

        try {
            $this->payum->getGateway($gatewayConfig->getGatewayName());
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    public function handle(PaymentInterface $payment, PayPayment $payPayment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        $useAuthorize = $this->useAuthorize($gatewayConfig);
        $gatewayName = $gatewayConfig->getGatewayName();

        $token = $this->payum->getTokenFactory()->createToken(
            $gatewayName,
            $payment,
            $payPayment->targetUrl,
            $payPayment->targetUrlParameters,
            $payPayment->afterUrl,
            $payPayment->afterUrlParameters,
        );

        $gateway = $this->payum->getGateway($gatewayName);

        $request = $this->createPayumRequest($token, $useAuthorize);

        $reply = $gateway->execute($request, true);

        if (null === $reply) {
            return;
        }

        if ($reply instanceof HttpResponse) {
            $payPayment->setReturn([
                'tokenHash' => $token->getHash(),
                'content' => $reply->getContent(),
                'statusCode' => $reply->getStatusCode(),
                'headers' => $reply->getHeaders(),
            ]);
        }

        if ($reply instanceof SymfonyHttpResponse) {
            $response = $reply->getResponse();
            $payPayment->setReturn([
                'tokenHash' => $token->getHash(),
                'content' => $response->getContent(),
                'statusCode' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ]);
        }
    }

    private function useAuthorize(GatewayConfigInterface $gatewayConfig): bool
    {
        if (!isset($gatewayConfig->getConfig()['use_authorize'])) {
            return false;
        }

        return (bool) $gatewayConfig->getConfig()['use_authorize'];
    }

    protected function createPayumRequest(TokenInterface $token, bool $useAuthorize): Authorize|Capture
    {
        if ($useAuthorize) {
            $request = new Authorize($token);
        } else {
            $request = new Capture($token);
        }

        return $request;
    }
}
