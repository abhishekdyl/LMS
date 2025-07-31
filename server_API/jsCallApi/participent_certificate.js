$(document).ready(function(){
    $("#page").append('<div id="customdev"><div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><img class="loader" style="display:none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 999;" src="/local/user_registration/assets/imgpsh_fullsize_anim.gif" /><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Certificates</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div id="mod_content" class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div></div></div></div></div>');
    $(document).on('click','.certificateuploadlink', function() {
        var userid = $(this).data('userid');
        console.log('aaaaaaaaaa',userid);
        $.ajax({
            "url": `${M.cfg.wwwroot}/local/user_registration/api/certificateapi.php`,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({"wsfunction":"getContent","wsargs":userid}),
            // "data": {"userid":userid},
            success: function (data, textStatus, jqXHR) {
                $('#mod_content').html(data.data);
                // console.log('res--',data);
            }
        });
    });
    $(document).on('change','#certif',function(){
        $(".loader").show();
        var temp_id = $(this).val();
        var userid = $('#uid').val();
        var args = {
            "userid":userid,
            "temp_id":temp_id
        }   
        if(temp_id != 0){
            $.ajax({
                "url": `${M.cfg.wwwroot}/local/user_registration/api/certificateapi.php`,
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({"wsfunction":"assign_certificate","wsargs":args}),
                // "data": {"userid":userid},
                success: function (data, textStatus, jqXHR) {
                    // $('#mod_content').html(data.data);
                    if(data.status == 1){
                        $('#exampleModalLong').modal('hide');
                         $(".loader").show();
                        // $('#exampleModalLong').hide();
                        // window.location.reload();
                    }
                    // console.log('res--',data);
                }
            });
        }
    });

});


   