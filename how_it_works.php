<?php
require 'connect.php';
require 'html_functions.php';
require 'mediaPath.php';
get_head_files();
?>




<body>

<?php
get_header()
?>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">

        <div class="row">
        <div class="how-it-works col-xs-12 col-md-5 col-md-offset-1">
       <img src = "<?php echo $images ?>female-singer.jpg" class="how-it-works-img" />
            </div>
        <div class="col-xs-12 col-md-6">
            <h2>Videos</h2>
        Rapportbook allows people to post videos showcasing their talent in Roll Call.
        Video is the ultimate way to display your talent to the world. Upload up to 5 minutes
            of a fun video showing everyone the talent you've been given. Let our community engage
            with your content by approving it, commenting on it and direct messaging you to give you more props!
        </div>
       </div>

        <!---------------------------------------------------------------->

        <div class="row">
      <div class="how-it-works col-xs-12 col-md-5 col-md-offset-1"> 
          <img src = "<?php echo $images ?>dance.jpg" class="how-it-works-img" /> 
      </div> 
            <div class="col-xs-12 col-md-6"> 
                <h2>Photos</h2> 
                It's nothing better than enjoying a great photo from a phenomenal performance.
                Still shots  can be just as climatic as video. When you get that great shot of a fabulous performance, post it 
                with a caption in Roll Call so everyone can get a sense and feel of what it was like to  be there.
                 </div> 
       </div>

           <!-------------------------------------------------------------->

        <div class="row">

                 <div class="how-it-works col-xs-12 col-md-5 col-md-offset-1"> 
                     <img src = "<?php echo $images ?>watching-video.jpeg" class="how-it-works-img" align="left" /> 
                 </div>
              <div class="col-xs-12 col-md-6"> 
                <h2>Engagement</h2> 
                At the end of the day, when you share your talent, whether through photo or video, you want 
                someone to engage with your content. This is what our community is all about. Once you post  something,
                our community will be delighted to comment on your work, approve it and  direct message you.
                A person may even want to share more intimate information, like contact info to perform, or just share it with someone  who would enjoy your talent just as much.
                You can always take it a step further and text your profile  to someone so they can check out your entire body of work;
                and the cool thing is, they don't even have to be signed  up on our site to see it. With the tools we've built, you will be able to build 
                crazy rapport with people, and after that, who knows.
                  </div>
    </div>
        <!--------------------------------------------------------------------->

</div>
     </div>
</body>