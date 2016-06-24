<?php
function makeLinks($str)
{
    if (strlen($str) == 0) {
        // do nothing
        return $str;
    } elseif (strstr($str, "<a href")) {
        $str = preg_replace('/(([\w!#$%&\'*+\-\/=?^`{|}~]|\\\\\\\\|\\\\?"|\\\\ )+\.)*([\w!#$%&\'*+\-\/=?^`{|}~]|\\\\\\\\|\\\\?"|\\\\ )+@((\w+[\.-])*[a-zA-Z]{2,}|\[(\d{1,3}\.){3}\d{1,3}\])/', '<a href="mailto:$0">$0</a>', $str);
        // has formatted links don't do anything
        return $str;
    } else {
        $str = preg_replace('/(([\w!#$%&\'*+\-\/=?^`{|}~]|\\\\\\\\|\\\\?"|\\\\ )+\.)*([\w!#$%&\'*+\-\/=?^`{|}~]|\\\\\\\\|\\\\?"|\\\\ )+@((\w+[\.-])*[a-zA-Z]{2,}|\[(\d{1,3}\.){3}\d{1,3}\])/', '<a href="mailto:$0">$0</a>', $str);
        // ************ do string prepping *************

        // make all versions of hyperlink lower case
        $str = str_replace("HTTP", "http", $str);
        $str = str_replace("Http", "http", $str);
        $str = str_replace("hTTp", "http", $str);
        $str = str_replace("hTtp", "http", $str);
        $str = str_replace("htTp", "http", $str);
        $str = str_replace("httP", "http", $str);
        $str = str_replace("hTtp", "http", $str);

        // if top domain ends a sentence, remove period
        // peroid will keep a domain from being recognized
        $str = str_replace("com.", "com", $str);
        $str = str_replace("net.", "net", $str);
        $str = str_replace("org.", "org", $str);

        preg_match("/[^\/]+$/", $str, $fileNameMatch);
        $fileName = $fileNameMatch[0];
        $fileName = str_replace(".", "", $fileName);

        // remove special characters in query string that will cause a rouge link to be built
        if (strstr($str, "?") && strstr($str, "+") && strstr($str, "%")) {
            $str = str_replace("+", "&", $str);
            $str = str_replace("!", "&", $str);
            $str = str_replace("%", "&", $str);
}
        // get click here value for links
        // if link has http parse_url will return a host name
        $url_info = parse_url($str);
        $clickHere = $url_info['host'];//hostname

        // detect urls with ONLY a .com and NO http and NO www and NO anchr
        $str = preg_replace_callback('#(\s|^)((https?://)?(\w|-)+(\.[a-z]{2,3})+(\:[0-9]+)?(?:/[^\s]*)?)(?=\s|\b)#is',
            create_function('$m, $clickHere', 'if (!preg_match("#^(https?://)#", $m[2])) return $m[1]."<a href=\"http://".$m[2]."\" target=\"blank\">Click Here</a>"; else return $m[1]."<a href=\"".$m[2]."\" target=\"blank\">Click Here</a>";'),
            $str);


        preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $str, $matches);
        $link = $matches[0];

        // if link does not have http, we must find the link
        // then dig the hostname out with a function
        if (strlen($clickHere) == 0) {

            $clickHere = get_string_between($link, "www.", ".com");
            if (strlen($clickHere) == 0) {
                // this means the prior function found a link with no http or www
                $clickHere = get_string_between($link, "http://", ".com");
            }
        }

        // handle sub domains
        if (preg_match('/^(.*)\.[^.]+\./', $str, $subDomains)) {
            if (strstr($str, "http")) { }
            else {
                if (strstr($str, ".com") || strstr($str, ".net") || strstr($str, ".org")) {
                    // get everything before the first . in the sub domain address
                    $subDomain = $subDomains[0];
                    $domainArray = array();
                    $domainArray = explode(' ', $subDomain);
                    // get the last element which is the first part of the sub domain
                    $trueDomain = end($domainArray);
                    // get all the text before the sub-domain
                    $newFirstPart = array_slice($domainArray, 0, -1);
                    // create a string instance of all the text before the sub domain
                    $firstPart = implode(' ', $newFirstPart);
                    // add http to the actual sub-domain and rebuild everything like it was
                    $newDomain = "$firstPart http://$trueDomain";
                    // replace the old text with the new http version
                    $str = str_replace($subDomain, $newDomain, $str);
                    // make the sub domain the click-able part
                    $trueDomainHyperlink = str_replace('.', '', $trueDomain);
                    $trueDomainHyperlink = str_replace('www', '', $trueDomain);
                    $clickHere = $trueDomainHyperlink;
                }
            }
        }

        $clickHere = str_replace('www.', '', $clickHere);
        $clickHere = str_replace('.net', '', $clickHere);
        $clickHere = str_replace('.com', '', $clickHere);
        $clickHere = str_replace('.org', '', $clickHere);

        if ($clickHere[0] == '.') {
            $clickHere = str_replace('.', '', $clickHere);
        }
        $str = str_replace("Click Here", $clickHere, $str);

        // detect urls WITH a www but NO http and NO anchor
        if (strstr($str, "http") || strstr($str, "https")) {
            $str = preg_replace('$(https?://[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', ' <a href="$1" target="_blank">' . $clickHere . '</a> ', $str . " ");
        }
        else {
            $str = preg_replace('$(www\.[a-z0-9_./?=&#-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$1"  target="_blank">' . $clickHere . '</a> ', $str . " ");
        }

        $title = pageTitle($link);

        $title = mysql_real_escape_string($title);

        $str = cleanBrTags($str);
        $str = trim($title) . ' '. trim($str);

        return $str;
    }
}

function pageTitle($page_url)
{
    $read_page=file_get_contents($page_url);
    preg_match("/<title.*?>[\n\r\s]*(.*)[\n\r\s]*<\/title>/", $read_page, $page_title);
    if (isset($page_title[1]))
    {
        if ($page_title[1] == '')
        {
            return $page_url;
        }
        $page_title = $page_title[1];
        return trim($page_title);
    }
    else
    {
        return $page_url;
    }
}

function __extractHostName($url)
{
    // gets i.e google
    $domain = parse_url($url , PHP_URL_HOST);
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $list)) {
        return substr($list['domain'], 0,strpos($list['domain'], "."));
    }
    return false;
}
function get_domain($url)
{
    // gets i.e google.com
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        return $regs['domain'];
    }
    return false;
}
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function cleanBrTags($txt)
{
    $txt=preg_replace("{(<br[\\s]*(>|\/>)\s*){2,}}i", "<br /><br />", $txt);
    $txt=preg_replace("{(<br[\\s]*(>|\/>)\s*)}i", "<br />", $txt);

    return $txt;
}

function remove_last_instance($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

?>