<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core;

use ADS\TakeHome\RequestValidation\Core\Rules\AfterOrEqualRule;
use ADS\TakeHome\RequestValidation\Core\Rules\DateFormatRule;
use ADS\TakeHome\RequestValidation\Core\Rules\IntegerRule;
use ADS\TakeHome\RequestValidation\Core\Rules\MinimumRule;
use ADS\TakeHome\RequestValidation\Core\Rules\RequiredRule;
use ADS\TakeHome\RequestValidation\Core\Rules\StringRule;
use InvalidArgumentException;

final class ValidationRuleRegistry implements ValidationRuleResolverInterface
{
    /** @var array<string, ValidationRuleInterface> */
    private array $rules;

    /** @param array<string, ValidationRuleInterface> $rules */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public static function defaults(): self
    {
        return new self([
            'required' => new RequiredRule(),
            'string' => new StringRule(),
            'integer' => new IntegerRule(),
            'min' => new MinimumRule(),
            'date_format' => new DateFormatRule(),
            'after_or_equal' => new AfterOrEqualRule(),
        ]);
    }

    public function get(string $name): ValidationRuleInterface
    {
        return $this->rules[$name]
            ?? throw new InvalidArgumentException("Ismeretlen validációs szabály: {$name}.");
    }
}
