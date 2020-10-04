<?php

namespace Croogo\Install;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;
use PhpParser\Node\Expr\ArrayItem;

/**
 * Replace database config values from config file
 */
final class DbConfigReplacer extends NodeVisitorAbstract {

    private $config = [];

    public function __construct($config) {
        $this->config = $config;
    }

    public function enterNode(Node $node) {
        $replaceKeys = ['driver', 'host', 'port', 'username', 'password', 'database'];
        if ($node instanceof ArrayItem) {
            if ($node->key && $node->key->value == 'Datasources') {
                foreach ($node->value->items as $datasource) {
                    if ($datasource->key->value === 'default') {
                        foreach ($datasource->value->items as $item) {
                            if (in_array($item->key->value, $replaceKeys) && !empty($this->config[$item->key->value])) {
                                $item->value->value = $this->config[$item->key->value];
                            }
                        }
                    }
                }
            }
        }
        return $node;
    }
}

final class DatasourceConfigUpdater {

    /**
     * Update database configuration in config file
     *
     * @param string $filename Config absolute filename
     * @param array $config Configuration array
     * @return int|false Number of bytes written or false on failure
     */
    public static function update($filename, $config)
    {
        $lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $parser = new Parser\Php7($lexer);
        $contents = file_get_contents($filename);
        $traverserClone = new NodeTraverser();
        $traverserClone->addVisitor(new NodeVisitor\CloningVisitor());
        $oldStmts = $parser->parse($contents);
        $oldTokens = $lexer->getTokens();
        $newStmts = $traverserClone->traverse($oldStmts);

        $traverser = new NodeTraverser();
        $dbConfigReplacer = new DbConfigReplacer($config);
        $traverser->addVisitor($dbConfigReplacer);
        $newStmts = $traverser->traverse($newStmts);

        $prettyPrinter = new PrettyPrinter\Standard();
        $contents = $prettyPrinter->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
        $return = file_put_contents($filename, $contents);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        return $return;
    }

}
