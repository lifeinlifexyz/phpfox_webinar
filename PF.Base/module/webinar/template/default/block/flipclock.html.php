<?php
    $aWebinar = $this->getVar('aWebinar');
?>
<h1 style="margin-bottom: 35px;">{phrase var='webinar.remained_before_start_of_the_webinar'}</h1>
<div class="your-clock"></div>
{literal}
<script type="text/javascript">
    $Behavior.onLoadFlipClock = function(){
        var clock = $('.your-clock').FlipClock({/literal}<?php echo ($aWebinar['start_time']-time());?>{literal}, {
            countdown: true,
            callbacks:{
                stop:function(){
                    window.location.href="";
                }
            }
        });
    }
</script>
{/literal}
