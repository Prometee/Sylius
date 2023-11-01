<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command\Payment;

use Sylius\Bundle\ApiBundle\Command\PaymentIdAwareInterface;

class PayPayment implements PaymentIdAwareInterface
{
    protected ?int $paymentId = null;

    protected array $return = [];

    public function __construct(
        public mixed $data,
        public string $targetUrl,
        public string $afterUrl,
        public array $targetUrlParameters = [],
        public array $afterUrlParameters = [],
    ) {
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function setReturn(array $return): void
    {
        $this->return = $return;
    }

    public function getReturn(): array
    {
        return $this->return;
    }
}
