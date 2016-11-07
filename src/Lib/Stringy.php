<?php

namespace Mamba\Lib;

use Stringy\Stringy as S;

class Stringy extends S
{
    /**
     * @param int $flags
     * @return S
     */
    public function deepHtmlDecode($flags = ENT_COMPAT)
    {
        $str = $this->htmlDecode()->htmlDecode()->htmlDecode();

        return static::create($str, $this->encoding);
    }
}