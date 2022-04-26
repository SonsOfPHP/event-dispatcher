<?php

declare(strict_types=1);

namespace SonsOfPHP\Component\EventSourcing;

/**
 * Aggregate ID
 *
 * @author Joshua Estes <joshua@sonsofphp.com>
 */
final class AggregateId implements AggregateIdInterface
{
    use AggregateIdTrait;
}