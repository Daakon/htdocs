<?php

function makeLinks($str)
{
    if (strlen($str) == 0) {
        // do nothing
        return $str;
    } elseif (strstr($str, "<a href")) {
        // has formatted links don't do anything
        return $str;
    } else {
        // detect urls with ONLY a .com and NO http and NO www and NO anchr
        $str = preg_replace_callback('#(\s|^)((https?://)?(\w|-)+(\.[a-z]{2,3})+(\:[0-9]+)?(?:/[^\s]*)?)(?=\s|\b)#is',
            create_function('$m', 'if (!preg_match("#^(https?://)#", $m[2])) return $m[1]."<a href=\"http://".$m[2]."\" target=\"blank\">".$m[2]."</a>"; else return $m[1]."<a href=\"".$m[2]."\" target=\"blank\">".$m[2]."</a>";'),
            $str);

        // detect urls WITH a www but NO http and NO anchor
        $str = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $str . " ");
        $str = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1"  target="_blank">$1</a> ', $str . " ");
        return $str;
    }
}

?>