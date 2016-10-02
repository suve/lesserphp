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
class Tagparse extends \LesserPhp\Easyparse
{

    static private $combinators;
    static private $match_opts;

    /**
     * @return array
     */
    public function parse()
    {
        if (empty(self::$combinators)) {
            self::$combinators = '(' . implode('|', array_map([$this, 'preg_quote'],
                    ['+', '>', '~'])) . ')';
            self::$match_opts = '(' . implode('|', array_map([$this, 'preg_quote'],
                    ['=', '~=', '|=', '$=', '*='])) . ')';
        }

        // crush whitespace
        $this->buffer = preg_replace('/\s+/', ' ', $this->buffer) . ' ';

        $tags = [];
        while ($this->tag($t)) {
            $tags[] = $t;
        }

        return $tags;
    }

    /**
     * @param $string
     *
     * @return string
     */
    static public function compileString($string)
    {
        list(, $delim, $str) = $string;
        $str = str_replace($delim, "\\" . $delim, $str);
        $str = str_replace("\n", "\\\n", $str);

        return $delim . $str . $delim;
    }

    /**
     * @param $paths
     *
     * @return string
     */
    static public function compilePaths($paths)
    {
        return implode(', ', array_map(['self', 'compilePath'], $paths));
    }

    // array of tags
    /**
     * @param $path
     *
     * @return string
     */
    static public function compilePath($path)
    {
        return implode(' ', array_map(['self', 'compileTag'], $path));
    }


    /**
     * @param $tag
     *
     * @return string
     */
    static public function compileTag($tag)
    {
        ob_start();
        if (isset($tag['comb'])) {
            echo $tag['comb'] . " ";
        }
        if (isset($tag['front'])) {
            echo $tag['front'];
        }
        if (isset($tag['attr'])) {
            echo '[' . $tag['attr'];
            if (isset($tag['op'])) {
                echo $tag['op'] . $tag['op_value'];
            }
            echo ']';
        }

        return ob_get_clean();
    }

    /**
     * @param $out
     *
     * @return bool
     */
    public function string(&$out)
    {
        $s = $this->seek();

        if ($this->literal('"')) {
            $delim = '"';
        } elseif ($this->literal("'")) {
            $delim = "'";
        } else {
            return false;
        }

        while (true) {
            // step through letters looking for either end or escape
            $buff = "";
            $escapeNext = false;
            $finished = false;
            for ($i = $this->count; $i < strlen($this->buffer); $i++) {
                $char = $this->buffer[$i];
                switch ($char) {
                    case $delim:
                        if ($escapeNext) {
                            $buff .= $char;
                            $escapeNext = false;
                            break;
                        }
                        $finished = true;
                        break 2;
                    case "\\":
                        if ($escapeNext) {
                            $buff .= $char;
                            $escapeNext = false;
                        } else {
                            $escapeNext = true;
                        }
                        break;
                    case "\n":
                        if (!$escapeNext) {
                            break 3;
                        }

                        $buff .= $char;
                        $escapeNext = false;
                        break;
                    default:
                        if ($escapeNext) {
                            $buff .= "\\";
                            $escapeNext = false;
                        }
                        $buff .= $char;
                }
            }
            if (!$finished) {
                break;
            }
            $out = ['string', $delim, $buff];
            $this->seek($i + 1);

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * @param $out
     *
     * @return bool
     */
    public function tag(&$out)
    {
        $s = $this->seek();
        $tag = [];
        if ($this->combinator($op)) {
            $tag['comb'] = $op;
        }

        if (!$this->match('(.*?)( |$|\[|' . self::$combinators . ')', $match)) {
            $this->seek($s);

            return false;
        }

        if (!empty($match[3])) {
            // give back combinator
            $this->count -= strlen($match[3]);
        }

        if (!empty($match[1])) {
            $tag['front'] = $match[1];
        }

        if ($match[2] === '[') {
            if ($this->ident($i)) {
                $tag['attr'] = $i;

                if ($this->match(self::$match_opts, $m) && $this->value($v)) {
                    $tag['op'] = $m[1];
                    $tag['op_value'] = $v;
                }

                if ($this->literal(']')) {
                    $out = $tag;

                    return true;
                }
            }
        } elseif (isset($tag['front'])) {
            $out = $tag;

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * @param $out
     *
     * @return bool
     */
    public function ident(&$out)
    {
        // [-]?{nmstart}{nmchar}*
        // nmstart: [_a-z]|{nonascii}|{escape}
        // nmchar: [_a-z0-9-]|{nonascii}|{escape}
        if ($this->match('(-?[_a-z][_\w]*)', $m)) {
            $out = $m[1];

            return true;
        }

        return false;
    }

    /**
     * @param $out
     *
     * @return bool
     */
    public function value(&$out)
    {
        if ($this->string($str)) {
            $out = self::compileString($str);

            return true;
        } elseif ($this->ident($id)) {
            $out = $id;

            return true;
        }

        return false;
    }


    /**
     * @param $op
     *
     * @return bool
     */
    public function combinator(&$op)
    {
        if ($this->match(self::$combinators, $m)) {
            $op = $m[1];

            return true;
        }

        return false;
    }
}
