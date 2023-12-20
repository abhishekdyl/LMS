<?php
get_header(); //3016
?>
<style>

.content-wrapper  {
    display : flex;
    align-items : center;
    flex-direction: column;
    
}

.content-wrapper h2{
    font-size : 32px;
    font-weight : 500;
}

.content-wrapper form .form-wrapper {
    display: flex;
    justify-content: center;
    padding: 30px;
    border: 1px solid skyblue;
    border-radius: 15px;
    width: 60%;
    line-height: 1.5;
    margin : 0 auto;
    padding : 15px;
     
}

.content-wrapper .content-peragraph  {
    max-width: 60%;
    line-height: 2;
    padding : 15px;
}

.content-wrapper .content-peragraph p,
.content-wrapper form .form-wrapper label{
    font-size : 24px;
}

.content-wrapper form .form-wrapper input{
    width: 50px;
    height: 30px;
    margin-right: 15px;
}

.literacy-test {
    font-size : 20px;
    margin : 20px 0px;
}


@media(max-width : 768px) {
    .content-wrapper .content-peragraph {
        max-width : 100%;
    }

    .content-wrapper form .form-wrapper{
        width : 90%;
        margin : 0px 15px;
    }

    .literacy-test {
        padding : 15px;
    }
    
    .content-wrapper form .form-wrapper input{
        width: 100px;
        height: 35px;
    }

    .disabled{
        pointer-events: none;
    }

</style>
<div class="content-wrapper">
    <h2>Digital Device Requirement</h2>
    <div class="content-peragraph">
        <p>
            There are certain requirements you have to fulfill in order to be admitted to One Planet College and start Learning. Make sure that you meet the minimum requirements before starting your application process.
        </p>
    </div>
    <form>
        <div class="form-wrapper">
            <input type="checkbox" id="checkBox" name="" />
            <label for="checkBox" name="">By proceeding I confirm that I understand the conditions of One Planet College online   program to fulfill my responsibilities.</label>
        </div>
    </form>


    <div class="literacy-test"> 
        <?php
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            // 'meta_value' => 'application_form.php'
            'meta_value' => 'department-list-template.php'
        ));
        foreach ($pages as $page) {
          $page->ID;
        }
        echo 'Please <a href="'.get_permalink($page->ID).'" id="view"  style="pointer-events: none;" >Click here >> </a>to take Digital Literacy Test.';
        ?>
        
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>

    $(function(){
       $("#checkBox").click(function () {
            
        if ($(this).is(":checked")) {
                $("#view").css("pointer-events", "unset");
            }else{
                $("#view").css("pointer-events", "none");
            }
        });
    });
</script>

<?php

get_footer();
?>