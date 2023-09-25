hello
<div class="accordion-content">
    <?php if( have_rows('faq') ): ?>
    <ul>
    <?php 
    $i=0;
    while( have_rows('faq') ): the_row();?>
        <li>
            <!-- <div class="li-container <?php if($i ==0){echo 'active';} ?>"> -->
            <div class="li-container">
                <div class="title">
                    <strong><?=get_sub_field('title');?></strong>
                    <div class="plus-icon"><span></span><span></span></div>
                </div>
                <div class="content">
                    <div class="content-container">
                    <?=get_sub_field('content');?>
                    </div>
                </div>
            </div>
        </li>
    <?php 
    $i++;
    endwhile; ?>
    </ul>
    <?php endif; ?>
</div>



<script type="text/javascript"> 

jQuery(document).ready(function($){ 
    var check_click = true;
    $(".accordion-content > ul > li > .li-container > .title").bind("click touchend", function(event){
        if(check_click){
            check_click = false;
            if($(this).parent().hasClass('active')){
            $(this).parent().removeClass('active');
            }else{
                $(".accordion-content > ul > li > .li-container").removeClass('active');
            $(this).parent().addClass('active');
            }

            setTimeout(function(){
                check_click = true;
            }, 200);

        }


    });



});
</script>