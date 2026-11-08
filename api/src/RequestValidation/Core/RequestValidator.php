<?php

declare(strict_types=1);

namespace ADS\TakeHome\RequestValidation\Core;

use DateTimeImmutable;
use LogicException;

/** Validates and casts request data using registered rules. */
class RequestValidator
{
    private readonly ValidationRuleResolverInterface $ruleRegistry;

    /**
     * @param array<string, list<string>> $rules
     */
    public function __construct(
        private readonly array $rules,
        ?ValidationRuleResolverInterface $ruleRegistry = null,
    ) {
        $this->ruleRegistry = $ruleRegistry ?? ValidationRuleRegistry::defaults();

        foreach ($this->rules as $fieldRules) {
            foreach ($fieldRules as $ruleDefinition) {
                [$name] = explode(':', $ruleDefinition, 2);
                $this->ruleRegistry->get($name);
            }
        }
    }

    /**
     * @param array<string, string|list<string>> $data
     * @return array{validated: array<string, string|list<string>|int|DateTimeImmutable>|null, errors: array<string, list<string>>}
     */
    public function validate(array $data): array
    {
        $errors = [];
        $validated = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $ruleDefinition) {
                [$name, $argument] = array_pad(explode(':', $ruleDefinition, 2), 2, null);
                $error = $this->ruleRegistry->get($name)->validate($value, $argument, $data);
                if ($error !== null) {
                    $errors[$field][] = $error;
                    // Match Laravel's bail behavior: stop after the first failing rule for a field.
                    break;
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $this->castValue($value, $fieldRules);
            }
        }

        return [
            'validated' => $errors === [] ? $validated : null,
            'errors' => $errors,
        ];
    }

    /**
     * @param string|list<string>|null $value
     * @param list<string> $fieldRules
     * @return string|list<string>|int|DateTimeImmutable
     */
    private function castValue(string|array|null $value, array $fieldRules): string|array|int|DateTimeImmutable
    {
        foreach ($fieldRules as $ruleDefinition) {
            [$name, $argument] = array_pad(explode(':', $ruleDefinition, 2), 2, null);
            $value = $this->ruleRegistry->get($name)->cast($value, $argument);
        }

        if ($value === null) {
            return '';
        }
        if (is_string($value) || is_array($value) || is_int($value) || $value instanceof DateTimeImmutable) {
            return $value;
        }

        throw new LogicException('A validációs szabály érvénytelen típusú értéket állított elő.');
    }
}
