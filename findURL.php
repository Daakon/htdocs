<?php

function makeLinks($str) {
    if (strlen($str) == 0) {
        // do nothing
        return $str;
    }
    elseif (strstr($str, "<a href")) {
        // has formatted links don't do anything
        return $str;
    }
    else {
        $str = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">$1</a> ', $str." ");
        $str = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1"  target="_blank">$1</a> ', $str." ");
        return $str;
    }
}
?>