<?php
namespace Croogo\Core\Test\TestCase;

use Croogo\Core\CroogoJson;
use Croogo\Core\TestSuite\CroogoTestCase;

class CroogoJsonTest extends CroogoTestCase
{

    public $fixtures = [
//		'plugin.croogo/settings.setting',
    ];

/**
 * testStringify
 */
    public function testStringify()
    {
        $options = 0;
        if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
            $options |= JSON_NUMERIC_CHECK;
        }
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $options |= JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        }

        $data = [
            'foo' => 'bar',
            'spam' => 'eggs',
        ];
        $expected = <<<END
{
\s+"foo": "bar",
\s+"spam": "eggs"
}
END;
        $result = CroogoJson::stringify($data, $options);
        $this->assertRegExp($expected, $result);

        $data = [
            'name' => 'rchavik/sites',
            'install_count' => 10,
            'numeric_list' => [1, 2, 3],
            'nested' => [
                'hello' => 'world',
                'value' => 20,
            ],
            'combination' => [
                'spam', 'eggs',
                'hello' => 'world',
            ],
        ];
        $expected = <<<END
{
\s+"name": "rchavik\/sites",
\s+"install_count": 10,
\s+"numeric_list": \[
\s+1,
\s+2,
\s+3
\s+\],
\s+"nested": {
\s+"hello": "world",
\s+"value": 20
\s+},
\s+"combination": {
\s+"0": "spam",
\s+"1": "eggs",
\s+"hello": "world"
\s+}
}
END;
        $result = CroogoJson::stringify($data, $options);
        $this->assertRegExp($expected, $result);
    }
}
