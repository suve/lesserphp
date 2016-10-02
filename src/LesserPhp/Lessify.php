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
 *
 * Will not work yet. Is dead since lessphp 0.3.0
 */
class Lessify extends \LesserPhp\Compiler
{

    /**
     *
     */
    public function dump()
    {
        print_r($this->env);
    }

    /**
     * @param null $str
     */
    public function parse($str = null)
    {
        $this->prepareParser($str ? $str : $this->buffer);
        while (false !== $this->parseChunk()) {
            ;
        }

        $root = new \LesserPhp\Nodecounter(null);

        // attempt to preserve some of the block order
        $order = [];

        $visitedTags = [];
        foreach (end($this->env) as $name => $block) {
            if (!$this->isBlock($name, $block)) {
                continue;
            }
            if (isset($visitedTags[$name])) {
                continue;
            }

            foreach ($block['__tags'] as $t) {
                $visitedTags[$t] = true;
            }

            // skip those with more than 1
            if (count($block['__tags']) == 1) {
                $p = new \LesserPhp\Tagparse(end($block['__tags']));
                $path = $p->parse();
                $root->addBlock($path, $block);
                $order[] = ['compressed', $path, $block];
                continue;
            } else {
                $common = null;
                $paths = [];
                foreach ($block['__tags'] as $rawtag) {
                    $p = new \LesserPhp\Tagparse($rawtag);
                    $paths[] = $path = $p->parse();
                    if (is_null($common)) {
                        $common = $path;
                    } else {
                        $new_common = [];
                        foreach ($path as $tag) {
                            $head = array_shift($common);
                            if ($tag == $head) {
                                $new_common[] = $head;
                            } else {
                                break;
                            }
                        }
                        $common = $new_common;
                        if (empty($common)) {
                            // nothing in common
                            break;
                        }
                    }
                }

                if (!empty($common)) {
                    $new_paths = [];
                    foreach ($paths as $p) {
                        $new_paths[] = array_slice($p, count($common));
                    }
                    $block['__tags'] = $new_paths;
                    $root->addToNode($common, $block);
                    $order[] = ['compressed', $common, $block];
                    continue;
                }

            }

            $order[] = ['none', $block['__tags'], $block];
        }


        $compressed = $root->children;
        foreach ($order as $item) {
            list($type, $tags, $block) = $item;
            if ($type === 'compressed') {
                $top = \LesserPhp\Tagparse::compileTag(reset($tags));
                if (isset($compressed[$top])) {
                    $compressed[$top]->compile($this);
                    unset($compressed[$top]);
                }
            } else {
                echo $this->indent(implode(', ', $tags) . ' {');
                $this->indentLevel++;
                \LesserPhp\Nodecounter::compileProperties($this, $block);
                $this->indentLevel--;
                echo $this->indent('}');
            }
        }
    }
}
