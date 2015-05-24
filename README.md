Input
=====
An input validation framework with a boring name

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
Raw input is transformed into safe data in three primary steps:

* Parsing
* Sanitization
* Validation

Parsing is responsible for transforming the raw input string into an associative
array. If your application is structured to do so, this step can be skipped
entirely.

Sanitization is a semi-optional step that can modify the data before
access or storage. However, it's generally accepted that sanitization should be
done in a context-sensitive manner, so it is recommended to skip the step here
and perform it as-needed in the output side.

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

    <?php
    // This would be in its own file
    use Firehed\Input\Interfaces\ValidationInterface;
    use Firehed\Input\Interfaces\SanitizerProviderInterface;

    use Firehed\Input\Containers\SafeInput;
    use Firehed\Input\Objects as O;
    class Endpoint
        implements ValidationInterface, SanitizerProviderInterface {

        public function getOptionalInputs() {
            return [
                'bar' => new O\Text(),
            ];
        }

        public function getRequiredInputs() {
            return [
                'foo' => new O\Text(),
            ];
        }

        // It's strongly recommended to NOT handle data sanitization here: use prepared
        // statements (likely handled by an ORM) to protect against SQLI, and use
        // context-sensitive sanitization on the output side to guard against XSS, etc.
        public function getSanitizationFilters() {
            return [];
        }

        public function execute(SafeInput $i) {
            // ... do some magic
            // $i['foo'] will be a string
            // $i['bar'] will be a string or null, since it was optional
        }
    }

`index.php`

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
            ->sanitize($endpoint)
            ->validate($endpoint);
        $endpoint->execute($input);
    } catch (Firehed\Input\Exceptions\InputException $e) {
        // The input contained invalid data
    } catch (Exception $e) {
        // Do any logging, error responses, etc.
        echo $e;
    }



Coming soon
-----

* Non-`NULL` default values for optional parameters
* Full PHP7 and/or HackLang support
