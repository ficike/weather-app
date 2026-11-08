<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core;

interface ValidationRuleResolverInterface
{
    public function get(string $name): ValidationRuleInterface;
}
