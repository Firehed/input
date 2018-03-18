Input
=====
An input validation framework with a boring name

[![Build Status](https://travis-ci.org/Firehed/input.svg?branch=master)](https://travis-ci.org/Firehed/input)
[![Coverage Status](https://coveralls.io/repos/github/Firehed/input/badge.svg?branch=master)](https://coveralls.io/github/Firehed/input?branch=master)

Concept
-----
Input validation is an important task in any web application, but remains an
extremely tedious task.  This replaces intermingled checks with a proper data
structure that clearly defines the required and optional inputs.

The design reolves around the idea of API-driven design, where each API
endpoint is its own object, however does not explicitly require this format
- it is capable of validating for any object that defines the input
requirements. What it cannot easily handle is the common pattern of large
controlelrs that are respnsible for many URLs, since each URL has its own
validation requirements. It's certainly possible to structure your code in
a way to make this work, but that is liable to become more complicated than the
benefit it provides.


Data handling steps
-----
Raw input is transformed into safe data in two primary steps:

* Parsing
* Validation

Parsing is responsible for transforming the raw input string into an associative
array. If your application is structured to do so, this step can be skipped
entirely.

Validation is the most useful part of the library - taking a defined set of
optional and required parameters and their types, and comparing the input
values to the spec. The implementation prevents invalid data from being
propagated entirely; it is not possible to create a `SafeInput` object (which
your application will use) from invalid data!

Upon completion of this process, a `SafeInput` object is returned that contains
data in accordance with the spec defined by the object implementing
`ValidationInterface` (missing optional values are null).

Because this library exists to provide trustable data, it will actively prevent
you from second-guessing it; for example, using `isset` or `empty` on the data
structure will throw an exception. It is the author's experience that acting
unable to trust your validated data is an anti-pattern and a code smell; if you
insist on doing so, this is not the right tool for you. Forcing trust like this
tends to prevent documentation from driting apart from reality.

Example
-----

A basic example follows:

`some_controller_file.php`

```php
<?php
// This would be in its own file
use Firehed\Input\Interfaces\ValidationInterface;

use Firehed\Input\Containers\SafeInput;
use Firehed\Input\Objects as O;
class Endpoint
    implements ValidationInterface {

    public function getOptionalInputs() {
        return [
            'bar' => new O\Text(),
            'baz' => (new O\Text())->setDefaultValue('my baz'),
        ];
    }

    public function getRequiredInputs() {
        return [
            'foo' => new O\Text(),
        ];
    }

    public function execute(SafeInput $i) {
        // ... do some magic
        // $i['foo'] will be a string
        // $i['bar'] will be a string or null, since it was optional
        // $i['baz'] will be a string or 'my baz', since it was an optional with a default value
    }
}
```

`index.php`

```php
<?php
// This is the core of your Front Controller

use Firehed\Input\Containers\RawInput;
use Firehed\Input\Parsers\URLEncoded;

// The endpoint should be detrmined by your router
$endpoint = new Endpoint();

// The parser should be determined by the Content-Type header
$parser = new URLEncoded();


try {
    $input = (new RawInput("foo=world"))
        ->parse($parser)
        ->validate($endpoint);
    $endpoint->execute($input);
} catch (Firehed\Input\Exceptions\InputException $e) {
    // The input contained invalid data
} catch (Exception $e) {
    // Do any logging, error responses, etc.
    echo $e;
}
```

Development
-----
PHP7 support (STH, return types, etc) has been added in the 2.0 line. The 1.0 line will remain compatible with PHP5.
