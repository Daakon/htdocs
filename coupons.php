<?php
require 'imports.php';
get_head_files();
get_header();
?>
<style>
    #ds-coupon-logo {
        display:none;
    }

    #ds-coupon-location > a {
        display:none;
    }

    #ds-coupon-subfoot > a {
        display:none;
    }
</style>

<?php
$ip = $_SERVER['SERVER_ADDR'];
$key = 'dc5ff2626e3bfffd325504af3e81c54d26e1c6c0bf5312c2ce5ef30043d314f6';
$apiUrl = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";

$d = file_get_contents($apiUrl);
$data = json_decode($d , true);

/*
 * JSON Returned
{
"statusCode" : "OK",
"statusMessage" : "",
"ipAddress" : "74.125.45.100",
"countryCode" : "US",
"countryName" : "UNITED STATES",
"regionName" : "CALIFORNIA",
"cityName" : "MOUNTAIN VIEW",
"zipCode" : "94043",
"latitude" : "37.3956",
"longitude" : "-122.076",
"timeZone" : "-08:00"
}
*/

if(strlen($data['countryCode'])) {
    $info = array(
        'ip' => $data['ipAddress'],
        'country_code' => $data['countryCode'],
        'country_name' => $data['countryName'],
        'region_name' => $data['regionName'],
        'city' => $data['cityName'],
        'zip_code' => $data['zipCode'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'time_zone' => $data['timeZone'],
    );
}
$lat = $info['latitude'];
$lon = $info['longitude'];

?>




<div style="padding-left:5px;font-weight:500;margin-top:-20px;margin-bottom:10px;display:inline-block">
    <a href="/home" style="padding-right:10px;"><img src="/images/home.png" height="20" width="20"  /> Home</a>
    Location incorrect? <button onclick="getLocation()">Re-calculate Location</button>

</div>

<script>

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    function showPosition(position) {
        localStorage.setItem("lat", position.coords.latitude);
        localStorage.setItem("lon", position.coords.longitude);
        location.reload();
        //var lat = localStorage.getItem("lat");
        //var lon = localStorage.getItem("lon");
    }
</script>


<script type="text/javascript">
    var lat;
    var lon;
    if (localStorage.getItem("lat") === null) {
        lat = <?php echo $lat ?>;
        lon = <?php echo $lon ?>;
    }
    else {
        lat = localStorage.getItem("lat");
        lon = localStorage.getItem("lon");
    }

ls_adWidth=1900; ls_adHeight=1000; ls_radius="25"; ls_widgetType="aggregator";
ls_fp="fpu2Ii1G-LSNbwSlg-0S3t4OZK-py4tD9jm";
ls_categories=["all"]; ls_lat=lat; ls_lng=lon;

</script>

<script type="text/javascript" src="//wgt.dtswg.com/wdgt/loader.js">
</script>

<div id="ls-offers-widget"></div>


<div id="loadingDeals" align="center">
    <img src="/images/spinner.gif" height="50" width="50" />
</div>

<script type="text/javascript">

    (function(){
        var myDiv = document.getElementById("loadingDeals");

        var show = function(){
            myDiv.style.display = "block";
            setTimeout(hide, 5000);  // 5 seconds
        }

        var hide = function(){
            myDiv.style.display = "none";
        }

        show();
    })();

</script>