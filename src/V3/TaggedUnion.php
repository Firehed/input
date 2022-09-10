<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

use function array_key_exists;
use function is_array;
use function is_object;

/**
 * A tagged union is a data structure that uses a tag field to indicate what
 * other data is associated with it. There are numerous other names for this
 * structure: discriminated union, sum type, etc.
 *
 * This validator expects a dictionary-like input (e.g. object or associative
 * array), and given the presence of the tag key, will ensure that the
 * remaining data adheres to the expected structure.
 *
 * The name of the tag key should be provided in the constructor's first
 * parameter, and the validators indexed by the tag value in the second.
 *
 * E.g. for data that looks like:
 *
 * ```
 * type MyInput =
 *   | { type: "int", int: int }
 *   | { type: "float", float: float }
 *   | { type: "specificstring", value: "a" | "b" }
 * ```
 *
 * The validator would be constructed like:
 * ```php
 * $validator = new TaggedUnion('type', [
 *     'int' => new Dict(['int' => new Integer()]),
 *     'float' => new Dict(['float' => new Float()]),
 *     'specificstring' => new Dict(['value' => new OneOf(['a', 'b'])]),
 * ]);
 * ```
 */
class TaggedUnion implements Validator
{
    /**
     * @param array<string, Validator> $validators
     */
    public function __construct(private string $tagKey, private array $validators)
    {
    }

    public function validate(mixed $input): Result
    {
        // Not dict-like
        if (!is_array($input) && !is_object($input)) {
            return Result::error();
        }
        $input = (array) $input;
        // Tag missing
        if (!array_key_exists($this->tagKey, $input)) {
            return Result::error();
        }
        $tag = $input[$this->tagKey];
        // Tag outside of expected data domain
        if (!array_key_exists($tag, $this->validators)) {
            return Result::error();
        }

        $validator = $this->validators[$tag];
        // Make a copy
        $data = $input;
        unset($data[$this->tagKey]);
        $wrapped = $validator->validate($data);
        // If error, return error?
        // If ok, inject tag back in and re-wrap?
        // There's not really a great way to represent this in a completely
        // type-safe way...
    }
}
