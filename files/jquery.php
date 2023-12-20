<script>   
    $("body").on("click",".source",function(){
        var theedge = $(this).attr('data-bs-target'); // get attribute data
        if (theedge == "#"){
            var url="<?php echo $row['source_url'];?>";
            console.log('abcd',url);
            window.open(url,'_blank');
        }
    });
<script>   