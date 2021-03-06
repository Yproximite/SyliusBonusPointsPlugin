<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusBonusPointsPlugin\Checker\Eligibility;

use BitBag\SyliusBonusPointsPlugin\Checker\Eligibility\BonusPointsStrategyEligibilityCheckerInterface;
use BitBag\SyliusBonusPointsPlugin\Checker\Eligibility\BonusPointsStrategyRulesEligibilityChecker;
use BitBag\SyliusBonusPointsPlugin\Checker\Rule\BonusPointsStrategyRuleCheckerInterface;
use BitBag\SyliusBonusPointsPlugin\Entity\BonusPointsStrategyInterface;
use BitBag\SyliusBonusPointsPlugin\Entity\BonusPointsStrategyRuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class BonusPointsStrategyRulesEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $ruleRegistry): void
    {
        $this->beConstructedWith($ruleRegistry);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BonusPointsStrategyRulesEligibilityChecker::class);
    }

    function it_implements_bonus_points_resolver_interface(): void
    {
        $this->shouldHaveType(BonusPointsStrategyEligibilityCheckerInterface::class);
    }

    function it_returns_false(
        OrderItemInterface $orderItem,
        BonusPointsStrategyInterface $bonusPointsStrategy,
        ServiceRegistryInterface $ruleRegistry,
        BonusPointsStrategyRuleInterface $bonusPointsStrategyRule,
        BonusPointsStrategyRuleCheckerInterface $checker
    ): void {
        $ruleConfiguration = ['taxons' => ['t-shirts']];

        $bonusPointsStrategy->hasRules()->willReturn(true);
        $bonusPointsStrategy->getRules()->willReturn(new ArrayCollection([$bonusPointsStrategyRule->getWrappedObject()]));
        $bonusPointsStrategyRule->getType()->willReturn('has_taxon');
        $ruleRegistry->get('has_taxon')->willReturn($checker);
        $bonusPointsStrategyRule->getConfiguration()->willReturn($ruleConfiguration);
        $checker->isEligible($orderItem, $ruleConfiguration)->willReturn(false);

        $bonusPointsStrategy->hasRules()->shouldBeCalled();
        $bonusPointsStrategy->getRules()->shouldBeCalled();
        $bonusPointsStrategyRule->getType()->shouldBeCalled();
        $ruleRegistry->get('has_taxon')->shouldBeCalled();
        $bonusPointsStrategyRule->getConfiguration()->shouldBeCalled();
        $checker->isEligible($orderItem, $ruleConfiguration)->shouldBeCalled();

        $this->isEligible($orderItem, $bonusPointsStrategy)->shouldReturn(false);
    }

    function it_returns_true(
        OrderItemInterface $orderItem,
        BonusPointsStrategyInterface $bonusPointsStrategy
    ): void {
        $bonusPointsStrategy->hasRules()->willReturn(true);
        $bonusPointsStrategy->getRules()->willReturn(new ArrayCollection());

        $bonusPointsStrategy->hasRules()->shouldBeCalled();
        $bonusPointsStrategy->getRules()->shouldBeCalled();

        $this->isEligible($orderItem, $bonusPointsStrategy)->shouldReturn(true);
    }
}
