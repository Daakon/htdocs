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

        // if post contains a url, get the title
        preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $str, $matches);
        if (!empty($matches[0])) {
            $link = $matches[0];
            $string = $str;
            preg_match('|<a.+?href\="(.+?)".*?>(.+?)</a>|i', $string, $match);
            $url = $match[1];
            $title = get_title($url);


            // get only host name
            $host = __extractName($link);

            // if no link, title will contain host name
            if (empty($link)) {
                $title = $host;
            }

            $imageLink = null;
            // check if web page has an image to add to the link
            $content=file_get_contents($link);
            if (preg_match("/<img.*src=\"(.*)\"/", $content, $images))
            {
                $image = $images[0];
            }

            // get favicon
            $favicon = '<img src="http://www.'.$host.'.com/favicon.ico" height="20" width="20" />';

            // add link
            $titleLink = '<a href="' . $link . '" target="_blank">' . $favicon.' '.$title . '</a>';

            // style the title & add webpage image to link
            $titleLink = '<span style="background:#f6f7f8;padding-right:5px;margin-top:10px;max-width:100%">' . $titleLink . '</span><br/>
            <a href="' . $link . '" target="_blank">' . $image. '</a><br/>';

            // remove special characters
            $titleLink = mysql_real_escape_string($titleLink);

            return $str . '<br/><br/>'. $titleLink.'<br/><br/>';
        }

        return $str . '<br/><br/>';
    }
}

function get_title($url){
    $str = file_get_contents($url);
    if(strlen($str)>0){
        $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
        preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
        return $title[1];
    }
}

function __extractName($url)
{
    $domain = parse_url($url , PHP_URL_HOST);
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $list)) {
        return substr($list['domain'], 0,strpos($list['domain'], "."));
    }
    return false;
}
?>