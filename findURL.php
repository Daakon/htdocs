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

        $str = str_replace("HTTP", "http", $str);
        $str = str_replace("Http", "http", $str);
        $str = str_replace("hTTp", "http", $str);
        $str = str_replace("hTtp", "http", $str);
        $str = str_replace("htTp", "http", $str);
        $str = str_replace("httP", "http", $str);


        // get click here value for links
        // if link has http parse_url will return a host name
        $url_info = parse_url($str);
        $clickHere = $url_info['host'];//hostname

        // detect urls with ONLY a .com and NO http and NO www and NO anchr
        $str = preg_replace_callback('#(\s|^)((https?://)?(\w|-)+(\.[a-z]{2,3})+(\:[0-9]+)?(?:/[^\s]*)?)(?=\s|\b)#is',
            create_function('$m, $clickHere', 'if (!preg_match("#^(https?://)#", $m[2])) return $m[1]."<a href=\"http://".$m[2]."\" target=\"blank\">Click Here</a>"; else return $m[1]."<a href=\"".$m[2]."\" target=\"blank\">Click Here</a>";'),
            $str);


        // if link does not have http, we must find the link
        // then dig the hostname out with a function
        if (strlen($clickHere) == 0) {
            $link = preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $str, $matches);
            $link = $matches[0];
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
        // if post contains a url, get the title
        preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $str, $matches);
        if (!empty($matches[0])) {
            // get full URL
            $link = $matches[0];
            $favicon = get_favicon($link."favicon.ico");
            $string = $str;
            $title = get_title($link);
            // get domain.com
            $hostFullName = get_domain($link);
            // get only host name 'domain'
            $host = __extractHostName($link);
            // if no link, title will contain host name
            if (empty($link)) {
                $title = $host;
            }
            // if page has no title, don't build title link
            if (strlen($title) > 1) {
                // check if web page has an image to add to the link
                $content = file_get_contents($link);
                if (preg_match("/<img.*src=\"(.*)\"/", $content, $images)) {
                    $image = $images[0];
                }
                // extract the src for that image
                // to make sure it has a full path
                $srcPattern = '/src="([^"]*)"/';
                preg_match($srcPattern, $image, $Imatches);
                $src = $Imatches[1];
                // if there is a full path image
                if (strlen($src) > 0) {
                    if (strstr($src, "http://") || strstr($src, "https://")) {
                        // do nothing to the image variable
                    }
                    else {
                        // return nothing, cannot guess image path on someone else's server
                        // too risky of returning a broken image.
                        $image = '';
                    }
                }

                // add link
                $titleLink = '<a href="' . $link . '" target="_blank">' . $title . '</a>';
                // style the title & add webpage image to link
                $titleLink = '<span style="padding-right:5px;margin-top:10px;max-width:100%">' . $titleLink . '</span>
                <a href="' . $link . '" target="_blank">' . $image . '</a>';
                // remove special characters
                $titleLink = mysql_real_escape_string($titleLink);
                // trim white space in title
                $titleLink = trim($titleLink);
                // remove excessive white space in title
                $titleLink = preg_replace('~>\s+<~', '><', $titleLink);
                // remove excessive line breaks in title
                $titleLink = cleanBrTags($titleLink);

                if (strstr($titleLink, "Search")) {
                    $titleLink = str_replace("<div", "");
                }

                // clean up rouge link text
                $cleanLink = explode('</a>%', $str);
                $garb = $cleanLink[1];
                if (strlen($garb) > 1) {
                    $str = $cleanLink[0];
                }
                if (strstr($str, "</a>/")) {
                    $str = str_replace('</a>/', '</a>', $str);
                }

                $titleLink = $favicon.' '. $titleLink;
                return $str . '<br/><br/>' . $titleLink;

            }
        }
        $str = cleanBrTags($str);
        $str = trim($str);

        return $str;
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

function get_favicon($faviconLink){
        $favicon = "<img src = '".$faviconLink."' height='50' width='50' onerror=\"this.src='./images/internet_icon.png'\"/>";
        $favicon = mysql_real_escape_string($favicon);
        return $favicon;
}

?>