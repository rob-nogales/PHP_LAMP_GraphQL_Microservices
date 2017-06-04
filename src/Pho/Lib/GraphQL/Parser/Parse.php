<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\GraphQL\Parser;

use Pho\Lib\GraphQL\Parser\Definitions\Node;
use Generator;
use GraphQL;

/**
 * Parses GraphQL schema into Pho Node Definition
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Parse extends AbstractDebuggable {

    private $parser;

    private $ast;

    /**
     * Constructor
     *
     * @param string $graphql The graphql file.
     * 
     * @throws  Exceptions\FileNotFoundException if the file can't be found.
     * @throws  Exceptions\MissingExtensionException if any of the LibGraphQLParser library or the GraphQL-Parser-PHP extension is missing.
     * @throws  Exceptions\InvalidSchemaException if the schema is invalid or corrupt.
     */
    public function __construct(string $graphql) 
    {
        if(!file_exists($graphql)) {
            throw new Exceptions\FileNotFoundException($graphql);
        }
        
        try {
            $this->parser = new GraphQL\Parser(); 
        }
        catch(\Exception $e) {
            throw new Exceptions\MissingExtensionException();
        }

        try {
            $this->ast = $this->parser->parse(file_get_contents($graphql)); 
        }
        catch(\Exception $e) {
            throw new Exceptions\InvalidSchemaException($e);
        }

        $this->def = $this->ast; // for Debuggable compatibility
    }

    /**
     * Returns the number of definitions in the GraphQL file.
     *
     * @return int The number of definitions.
     */
    public function count(): int
    {
        return count($this->ast["definitions"]);
    }

    /**
     * Yields the nodes.
     * 
     * Aka definitions in GraphQL lingo.
     *
     * @return \Generator
     */
    public function nodes(): Generator
    {
        foreach($this->ast["definitions"] as $definition) {
            yield new Node($definition);
        }
    }

    /**
     * Fetches a node with given key.
     *
     * @param int $n Key.
     * 
     * @return Node
     */
    public function node(int $n): Node
    {
        return new Node($this->ast["definitions"][$n]);
    }

}