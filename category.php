<?php
function category()
{
    echo '<option value = "Art">Art</option>';
    echo '<option value = "Alternative">Alternative</option>';
    echo '<option value = "Announcement">Announcement</option>';
    echo '<option value = "Business">Business</option>';
    echo '<option value = "Christian">Christian</option>';
    echo '<option value = "Classical">Classical</option>';
    echo '<option value = "Comedy">Comedy</option>';
    echo '<option value = "Cosmetology">Cosmetology</option>';
    echo '<option value = "Country">Country</option>';
    echo '<option value = "Culinary">Culinary</option>';
    echo '<option value = "Design">Design</option>';
    echo '<option value = "Dance">Dance</option>';
    echo '<option value = "Education">Education</option>';
    echo '<option value = "Event">Event</option>';
    echo '<option value = "Fashion">Fashion</option>';
    echo '<option value = "Fitness">Fitness</option>';
    echo '<option value = "Literature">Literature</option>';
    echo '<option value = "Magic">Magic</option>';
    echo '<option value = "Miscellaneous">Miscellaneous</option>';
    echo '<option value = "Model">Model</option>';
    echo '<option value = "Movie">Movie</option>';
    echo '<option value = "Night Life">Night Life</option>';
    echo '<option value = "Photography">Photography</option>';
    echo '<option value = "Pop">Pop</option>';
    echo '<option value = "Question">Question</option>';
    echo '<option value = "R&B">R&B</option>';
    echo '<option value = "Rap">Rap</option>';
    echo '<option value = "Recreation">Recreation</option>';
    echo '<option value = "Social">Social</option>';
    echo '<option value = "Sports">Sports</option>';
    echo '<option value = "Technology">Technology</option>';
    echo '<option value = "Theatrical">Theatrical</option>';
    echo '<option value = "TV">TV</option>';
    echo '<option value = "Video Game">Video Game</option>';
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

function interestGlyphs($interest) {

    $path = "/post-glyphs/";

    if ($interest == "Art") {
        ?><img src="<?php echo $path ?>art.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Alternative") {
        ?><img src="<?php echo $path ?>alternative.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Announcement") {
        ?><img src="<?php echo $path ?>announcement.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Business") {
        ?><img src="<?php echo $path ?>business.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Christian") {
        ?><img src="<?php echo $path ?>christian.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Classical") {
        ?><img src="<?php echo $path ?>classical.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Comedy") {
        ?><img src="<?php echo $path ?>comedy.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Cosmetology") {
        ?><img src="<?php echo $path ?>cosmetology.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Country") {
        ?><img src="<?php echo $path ?>country.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Culinary") {
        ?><img src="<?php echo $path ?>culinary.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Design") {
        ?><img src="<?php echo $path ?>design.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Dance") {
        ?><img src="<?php echo $path ?>dance.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Education") {
        ?><img src="<?php echo $path ?>education.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Event") {
        ?><img src="<?php echo $path ?>event.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Fashion") {
        ?><img src="<?php echo $path ?>fashion.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Movie") {
        ?><img src="<?php echo $path ?>film.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Fitness") {
        ?><img src="<?php echo $path ?>fitness.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Literature") {
        ?><img src="<?php echo $path ?>literature.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Magic") {
        ?><img src="<?php echo $path ?>magic.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Miscellaneous") {
        ?><img src="<?php echo $path ?>miscellaneous.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Model") {
        ?><img src="<?php echo $path ?>model.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Night Life") {
        ?><img src="<?php echo $path ?>night-life.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Photography") {
         ?><img src="<?php echo $path ?>photo.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Pop") {
        ?><img src="<?php echo $path ?>pop.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Question") {
        ?><img src="<?php echo $path ?>question-mark.jpeg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "R&B") {
        ?><img src="<?php echo $path ?>r&b.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Rap") {
        ?><img src="<?php echo $path ?>rap.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Recreation") {
        ?><img src="<?php echo $path ?>recreation.jpg" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Social") {
        ?><img src="<?php echo $path ?>social.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Sports") {
        ?><img src="<?php echo $path ?>sports.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Technology") {
        ?><img src="<?php echo $path ?>technology.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Theatrical") {
        ?><img src="<?php echo $path ?>theatrical.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "TV") {
        ?><img src="<?php echo $path ?>tv.png" class="icon" alt="Photos/Video"/><?
    }
    elseif ($interest == "Video Game") {
        ?><img src="<?php echo $path ?>video-game.png" class="icon" alt="Photos/Video"/><?
    }
}