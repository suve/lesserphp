<?php

namespace LesserPhp\Formatter;

/**
 * lesserphp
 * https://www.maswaba.de/lesserphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org
 *
 * Copyright 2013, Leaf Corcoran <leafot@gmail.com>
 * Copyright 2016, Marcus Schwarz <github@maswaba.de>
 * Licensed under MIT or GPLv3, see LICENSE
 * @package LesserPhp
 */
class Classic implements FormatterInterface
{

    public $indentChar = '  ';

    public $break = "\n";
    public $open = ' {';
    public $close = '}';
    public $selectorSeparator = ', ';
    public $assignSeparator = ':';

    public $openSingle = ' { ';
    public $closeSingle = ' }';

    public $disableSingle = false;
    public $breakSelectors = false;

    public $compressColors = false;

    /**
     * Classic constructor.
     */
    public function __construct()
    {
        $this->indentLevel = 0;
    }

    /**
     * @param int $n
     *
     * @return string
     */
    public function indentStr($n = 0)
    {
        return str_repeat($this->indentChar, max($this->indentLevel + $n, 0));
    }

    /**
     * @param $name
     * @param $value
     *
     * @return string
     */
    public function property($name, $value)
    {
        return $name . $this->assignSeparator . $value . ';';
    }

    /**
     * @param $block
     *
     * @return bool
     */
    protected function isEmpty($block)
    {
        if (empty($block->lines)) {
            foreach ($block->children as $child) {
                if (!$this->isEmpty($child)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param $block
     */
    public function block($block)
    {
        if ($this->isEmpty($block)) {
            return;
        }

        $inner = $pre = $this->indentStr();

        $isSingle = !$this->disableSingle &&
            $block->type === null && count($block->lines) === 1;

        if (!empty($block->selectors)) {
            $this->indentLevel++;

            if ($this->breakSelectors) {
                $selectorSeparator = $this->selectorSeparator . $this->break . $pre;
            } else {
                $selectorSeparator = $this->selectorSeparator;
            }

            echo $pre .
                implode($selectorSeparator, $block->selectors);
            if ($isSingle) {
                echo $this->openSingle;
                $inner = '';
            } else {
                echo $this->open . $this->break;
                $inner = $this->indentStr();
            }

        }

        if (!empty($block->lines)) {
            $glue = $this->break . $inner;
            echo $inner . implode($glue, $block->lines);
            if (!$isSingle && !empty($block->children)) {
                echo $this->break;
            }
        }

        foreach ($block->children as $child) {
            $this->block($child);
        }

        if (!empty($block->selectors)) {
            if (!$isSingle && empty($block->children)) {
                echo $this->break;
            }

            if ($isSingle) {
                echo $this->closeSingle . $this->break;
            } else {
                echo $pre . $this->close . $this->break;
            }

            $this->indentLevel--;
        }
    }
}
