<?php
function category()
{
    session_start();
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (strstr($url, "learn_more")) {
        echo '<option value = "All">All</option>';
    }
    echo '<option value = "Art & Photography">Art & Photography</option>';
    echo '<option value = "Automotive">Automotive</option>';
    echo '<option value = "Business">Business</option>';
    echo '<option value = "Community">Community</option>';
    echo '<option value = "Cosmetology">Cosmetology</option>';
    echo '<option value = "Education">Education</option>';
    echo '<option value = "Entertainment">Entertainment</option>';
    echo '<option value = "Events">Events</option>';
    echo '<option value = "Fashion & Modeling">Fashion & Modeling</option>';
    echo '<option value = "Finance">Finance</option>';
    echo '<option value = "Fitness">Fitness</option>';
    echo '<option value = "Gaming">Gaming</option>';
    echo '<option value = "Household">Household</option>';
    echo '<option value = "Legal">Legal</option>';
    echo '<option value = "Literature">Literature</option>';
    echo '<option value = "Media">Media</option>';
    echo '<option value = "Night Life">Night Life</option>';
    echo '<option value = "Realty">Realty</option>';
    echo '<option value = "Retail">Retail</option>';
    echo '<option value = "Restaurant">Restaurant</option>';
    echo '<option value = "Sports">Sports</option>';
    echo '<option value = "Technology">Technology</option>';
    echo '<option value = "Wellness">Wellness</option>';
}

function age() {
    echo '<option value = "18">18</option>';
    echo '<option value = "19">19</option>';
    echo '<option value = "20">20</option>';
    echo '<option value = "21">21</option>';
    echo '<option value = "22">22</option>';
    echo '<option value = "23">23</option>';
    echo '<option value = "24">24</option>';
    echo '<option value = "25">25</option>';
    echo '<option value = "26">26</option>';
    echo '<option value = "27">27</option>';
    echo '<option value = "28">28</option>';
    echo '<option value = "29">29</option>';
    echo '<option value = "30">30</option>';
    echo '<option value = "31">31</option>';
    echo '<option value = "32">32</option>';
    echo '<option value = "33">33</option>';
    echo '<option value = "34">34</option>';
    echo '<option value = "35">35</option>';
    echo '<option value = "36">36</option>';
    echo '<option value = "37">37</option>';
    echo '<option value = "38">38</option>';
    echo '<option value = "39">39</option>';
    echo '<option value = "40">40</option>';
    echo '<option value = "41">41</option>';
    echo '<option value = "42">42</option>';
    echo '<option value = "43">43</option>';
    echo '<option value = "44">44</option>';
    echo '<option value = "45">45</option>';
    echo '<option value = "46">46</option>';
    echo '<option value = "47">47</option>';
    echo '<option value = "48">48</option>';
    echo '<option value = "49">49</option>';
    echo '<option value = "50">50</option>';
    echo '<option value = "51">51</option>';
    echo '<option value = "52">52</option>';
    echo '<option value = "53">53</option>';
    echo '<option value = "54">54</option>';
    echo '<option value = "55">55</option>';
    echo '<option value = "56">56</option>';
    echo '<option value = "57">57</option>';
    echo '<option value = "58">58</option>';
    echo '<option value = "59">59</option>';
    echo '<option value = "60">60</option>';
    echo '<option value = "61">61</option>';
    echo '<option value = "62">62</option>';
    echo '<option value = "63">63</option>';
    echo '<option value = "64">64</option>';
    echo '<option value = "65">65</option>';
    echo '<option value = "66">66</option>';
    echo '<option value = "67">67</option>';
    echo '<option value = "68">68</option>';
    echo '<option value = "69">69</option>';
    echo '<option value = "70">70</option>';
    echo '<option value = "71">71</option>';
    echo '<option value = "72">72</option>';
    echo '<option value = "73">73</option>';
    echo '<option value = "74">74</option>';
    echo '<option value = "75">75</option>';
    echo '<option value = "76">76</option>';
    echo '<option value = "77">77</option>';
    echo '<option value = "78">78</option>';
    echo '<option value = "79">79</option>';
    echo '<option value = "80">80</option>';
    echo '<option value = "81">81</option>';
    echo '<option value = "82">82</option>';
    echo '<option value = "83">83</option>';
    echo '<option value = "84">84</option>';
    echo '<option value = "85">85</option>';
    echo '<option value = "86">86</option>';
    echo '<option value = "87">87</option>';
    echo '<option value = "88">88</option>';
    echo '<option value = "89">89</option>';
    echo '<option value = "90">90</option>';

}

$keywords = "
Automotive, Art,Books, Business, Business,
Community, Cosmetology, Culinary,
Design,
Education, Employment,
Fashion, Finance, Fitness,
Gaming,
Marketing, Media, Modeling, Music,
Night Life,
Photography,
Realty,
Sports,
Technology, Theatre
";

/*function interestGlyphs($interest) {

    $path = "/post-glyphs/";

    if ($interest == "Art") {
        */?><!--<img src="<?php /*echo $path */?>art.png" class="icon" alt="Photos/Video"/><?/*
    }

    elseif ($interest == "Announcement") {
        */?><img src="<?php /*echo $path */?>announcement.jpeg" class="icon" alt="Photos/Video"/><?/*
    }

    elseif ($interest == "Comedy") {
        */?><img src="<?php /*echo $path */?>comedy.jpg" class="icon" alt="Photos/Video"/><?/*
    }

    elseif ($interest == "Event") {
        */?><img src="<?php /*echo $path */?>event.png" class="icon" alt="Photos/Video"/><?/*
    }
    elseif ($interest == "Fitness") {
        */?><img src="<?php /*echo $path */?>fitness.png" class="icon" alt="Photos/Video"/><?/*
    }
    elseif ($interest == "Food & Drink") {
        */?><img src="<?php /*echo $path */?>food-drink.svg" class="icon" alt="Photos/Video"/><?/*
    }

    elseif ($interest == "Miscellaneous") {
        */?><img src="<?php /*echo $path */?>miscellaneous.jpg" class="icon" alt="Photos/Video"/><?/*
    }

    elseif ($interest == "Night Life") {
        */?><img src="<?php /*echo $path */?>night-life.png" class="icon" alt="Photos/Video"/><?/*
    }
    elseif ($interest == "Social") {
        */?><img src="<?php /*echo $path */?>social.png" class="icon" alt="Photos/Video"/>--><?/*
    }
}*/