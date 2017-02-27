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
class Easyparse
{

    protected $buffer;
    protected $count;

    /**
     * Easyparse constructor.
     *
     * @param $str
     */
    public function __construct($str)
    {
        $this->count = 0;
        $this->buffer = trim($str);
    }

    /**
     * @param null $where
     *
     * @return bool|int
     */
    public function seek($where = null)
    {
        if ($where === null) {
            return $this->count;
        } else {
            $this->count = $where;
        }

        return true;
    }

    /**
     * @param $what
     *
     * @return string
     */
    public function preg_quote($what)
    {
        return preg_quote($what, '/');
    }

    /**
     * @param      $regex
     * @param      $out
     * @param bool $eatWhitespace
     *
     * @return bool
     */
    public function match($regex, &$out, $eatWhitespace = true)
    {
        $r = '/' . $regex . ($eatWhitespace ? '\s*' : '') . '/Ais';
        if (preg_match($r, $this->buffer, $out, null, $this->count)) {
            $this->count += strlen($out[0]);

            return true;
        }

        return false;
    }

    /**
     * @param      $what
     * @param bool $eatWhitespace
     *
     * @return bool
     */
    public function literal($what, $eatWhitespace = true)
    {
        // this is here mainly prevent notice from { } string accessor
        if ($this->count >= strlen($this->buffer)) {
            return false;
        }

        // shortcut on single letter
        if (!$eatWhitespace && strlen($what) === 1) {
            if ($this->buffer{$this->count} == $what) {
                $this->count++;

                return true;
            } else {
                return false;
            }
        }

        return $this->match($this->preg_quote($what), $m, $eatWhitespace);
    }
}
