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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\PaymentIdAwareInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/** @experimental */
final class PaymentIdAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    /**
     * @param PaymentIdAwareInterface $object
     */
    public function transform($object, string $to, array $context = []): PaymentIdAwareInterface
    {
        if (!isset($context['object_to_populate'])) {
            return $object;
        }

        /** @var PaymentInterface $payment */
        $payment = $context['object_to_populate'];

        $object->setPaymentId($payment->getId());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof PaymentIdAwareInterface;
    }
}
