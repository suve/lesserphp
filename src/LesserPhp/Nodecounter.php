<?php

namespace LesserPhp;

/**
 * lessify
 * Convert a css file into a less file
 * https://www.maswaba.de/lesserphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org
 *
 * Copyright 2013, Leaf Corcoran <leafot@gmail.com>
 * Copyright 2016, Marcus Schwarz <github@maswaba.de>
 * Licensed under MIT or GPLv3, see LICENSE
 * @package LesserPhp
 */
class Nodecounter
{

    private $count = 0;
    public $children = [];

    private $name;
    private $child_blocks;
    private $the_block;

    /**
     * Nodecounter constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param array $stack
     */
    public function dump(array $stack = null)
    {
        if (null === $stack) {
            $stack = [];
        }
        $stack[] = $this->getName();
        echo implode(' -> ', $stack) . " ($this->count)\n";
        foreach ($this->children as $child) {
            $child->dump($stack);
        }
    }

    /**
     * @param $c
     * @param $block
     */
    static public function compileProperties($c, $block)
    {
        foreach ($block as $name => $value) {
            if ($c->isProperty($name, $value)) {
                echo $c->compileProperty($name, $value) . "\n";
            }
        }
    }

    /**
     * @param      $c
     * @param array $path
     */
    public function compile($c, array $path = null)
    {
        if (null === $path) {
            $path = [];
        }
        $path[] = $this->name;

        $isVisible = null !== $this->the_block || null !== $this->child_blocks;

        if ($isVisible) {
            echo $c->indent(implode(' ', $path) . ' {');
            $c->indentLevel++;
            $path = [];

            if ($this->the_block) {
                self::compileProperties($c, $this->the_block);
            }

            if ($this->child_blocks) {
                foreach ($this->child_blocks as $block) {
                    echo $c->indent(\LesserPhp\Tagparse::compilePaths($block['__tags']) . ' {');
                    $c->indentLevel++;
                    self::compileProperties($c, $block);
                    $c->indentLevel--;
                    echo $c->indent('}');
                }
            }
        }

        // compile child nodes
        foreach ($this->children as $node) {
            $node->compile($c, $path);
        }

        if ($isVisible) {
            $c->indentLevel--;
            echo $c->indent('}');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            return "[root]";
        } else {
            return $this->name;
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getNode($name)
    {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Nodecounter($name);
        }

        return $this->children[$name];
    }

    /**
     * @param $path
     *
     * @return \LesserPhp\Nodecounter|mixed
     */
    public function findNode($path)
    {
        $current = $this;
        for ($i = 0; $i < count($path); $i++) {
            $t = \LesserPhp\Tagparse::compileTag($path[$i]);
            $current = $current->getNode($t);
        }

        return $current;
    }

    /**
     * @param $path
     * @param $block
     *
     * @throws \Exception
     */
    public function addBlock($path, $block)
    {
        $node = $this->findNode($path);
        if (null !== $node->the_block) {
            throw new \Exception("can this happen?");
        }

        unset($block['__tags']);
        $node->the_block = $block;
    }

    /**
     * @param $path
     * @param $block
     */
    public function addToNode($path, $block)
    {
        $node = $this->findNode($path);
        $node->child_blocks[] = $block;
    }
}
