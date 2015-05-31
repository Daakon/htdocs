<?php
require 'connect.php';
$interests;
$state;
$age;

function getInterests($ID) {
    // returns an array of member interests
    $sql = "SELECT Interests FROM Profile WHERE Member_ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $interests = $row['Interests'];
    return $interests;
}

function getState($ID) {
    // returns member state
    $sql = "SELECT CurrentState FROM Profile WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $state = $row['CurrentState'];
    return $state;
}

function getAge($ID) {
    // returns member age
    $sql = "SELECT DOB FROM Members WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $birthDate = $row['DOB'];

    $age = date_diff(date_create($birthDate), date_create('today'))->y;
    $format = 'Y-m-j G:i:s';
    $date = date($format);
    return $age;
}

function getAds($category, $age, $state, $interests) {
    $interests = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $interests, -1, PREG_SPLIT_NO_EMPTY);

    $interest1 = $interests[0];
    $interest2 = $interests[1];
    $interest3 = $interests[2];
    $interest4 = $interests[3];
    $interest5 = $interests[4];

    $ads = "SELECT DISTINCT Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Posts.ID As PostID,
    Posts.Post As Post,
    Posts.Category As Category,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Profile.Member_ID
    And Posts.IsDeleted = 0
    AND Posts.Category = 'Sponsored'
    And (Posts.AgeStart <= $age || Posts.AgeStart = 0)
    And (Posts.AgeEnd <= $age || Posts.AgeEnd = 0)
    And (Posts.AdState = '$state' || Posts.AdState = '')
    And (LOWER(Posts.Interests) LIKE '%$interest1%' ||
    LOWER(Posts.Interests) LIKE '%$interest2%' ||
    LOWER(Posts.Interests) LIKE '%$interest3' ||
    LOWER(Posts.Interests) LIKE '%$interest4%' ||
    LOWER(Posts.Interests) LIKE '%$interest4' ||
    Posts.Interests = '')
    And (Posts.AdCategory = '$category' || Posts.AdCategory = '')
    And (Posts.TalentFeed = 1)
    And (CURRENT_DATE() < Posts.AdEnd)
    ";

     return $ads;
}

function getRightColumnAds($category, $age, $state, $interests) {

    $rightColumnAds = "SELECT DISTINCT
    Post
    FROM Posts
    WHERE
    Posts.IsDeleted = 0
    AND Posts.Category = 'Sponsored'
    And (Posts.AgeStart <= $age || Posts.AgeStart = 0)
    And (Posts.AgeEnd <= $age || Posts.AgeEnd = 0)
    And (Posts.AdState = '$state' || Posts.AdState = '')
    And (Posts.Interests LIKE '%$interests%' || Posts.Interests = '')
    And (Posts.AdCategory = '$category' || Posts.AdCategory = '')
    And (Posts.RightColumn = 1)
    And (CURRENT_DATE() < Posts.AdEnd)
    Order By Posts.ID DESC LIMIT 3";

return $rightColumnAds;
}
?>