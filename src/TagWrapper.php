<?php

namespace ATEKA;


class TagWrapper
{
    public static function TagPre($content)
    {
        $content = '<pre>' . $content . '</pre>';
        return $content;
    }
}
