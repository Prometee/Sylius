<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Checker\Rule;

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'item_total';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        $promotionSubjectTotal = $subject->getPromotionSubjectTotal();

        // Legacy operator to avoid BC break
        if (!isset($configuration['comparison_operator'])) {
            $configuration['comparison_operator'] = '>=';
        }

        if ('>=' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal >= $configuration['amount'];
        }

        if ('===' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal === $configuration['amount'];
        }

        if ('!==' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal !== $configuration['amount'];
        }

        if ('<' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal < $configuration['amount'];
        }

        if ('<=' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal <= $configuration['amount'];
        }

        if ('>' === $configuration['comparison_operator']) {
            return $promotionSubjectTotal > $configuration['amount'];
        }

        return false;
    }
}
