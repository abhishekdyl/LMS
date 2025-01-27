define(['jquery', 'core/ajax', 'core/str', 'core/config', 'core/templates', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/fragment'], function ($, AJAX, str, mdlcfg, templates, notification, ModalFactory, ModalEvents, Fragment) {

 var usersTable = {

        init: function (asgid) {  

           $('body').on('click', '.introtoggle', function(e){   

              e.preventDefault();

              $("ul.ulclassone").toggle();

              $("ul.ulclasstwo").css("display", "none");

              $("ul.ulclassthree").css("display", "none");



           });

           $('body').on('click', '.maintoogle', function(e){   

              e.preventDefault();

              $("ul.ulclasstwo").toggle();

              $("ul.ulclassthree").css("display", "none");

              $("ul.ulclassone").css("display", "none");

           });

           $('body').on('click', '.selecttoogle', function(e){   

              e.preventDefault();

              $("ul.ulclassthree").toggle();

              $("ul.ulclassone").css("display", "none");              

              $("ul.ulclasstwo").css("display", "none");

           });





          $('body').on('click', '.introfeedback', function(){          

                           

                var modal = ModalFactory.create({

                    title: "Add new feedback",

                    body: '<div class="formfeedback"><form><input type="hidden" class = "feedtype" name="feedbacktype" value="1" /><textarea name="feedbacktext" class="insertfeedback"></textarea><button class ="savefeedback" type="button" value="Submit">Submit</button></form></div>',

                    large: true,

                    footer: '',

                }).then(function (modal) {

                    modal.show();

                    modal.getRoot().addClass('createtenantuser');

                    modal.getRoot().on(ModalEvents.hidden, function() {

                        modal.destroy();

                    });

                    return modal;

                });          

        

            

          

          });

          $('body').on('click', '.ulclassone li .fbname', function(){

            if($(this).attr('attr')!='addnew'){

             var textnew = $(this).text();

              var feedbacktext = ' <span class="introductry">'+textnew+'</span>';

              if($('#id_assignfeedbackcomments_editoreditable').find('p').length > 0){

                 $('#id_assignfeedbackcomments_editoreditable').find('p').append(feedbacktext);

                 $('#id_assignfeedbackcomments_editor').find('p').append(feedbacktext);

               }

              else{

                 $('#id_assignfeedbackcomments_editoreditable').append('<p>'+feedbacktext+'</p>');

                 $('#id_assignfeedbackcomments_editor').append('<p>'+feedbacktext+'</p>');

              } 
              $('#id_assignfeedbackcomments_editoreditable').focus();      

            }

          });

          

          $('body').on('click', '.mainfeedback', function(){           

              

                var modal = ModalFactory.create({

                    title: "Add new feedback",

                    body: '<div class="formfeedback"><form><input type="hidden" class = "feedtype" name="feedbacktype" value="2" /><textarea name="feedbacktext" class="insertfeedback"></textarea><button class ="savefeedback" type="button" value="Submit">Submit</button></form></div>',

                    large: true,

                    footer: '',

                }).then(function (modal) {

                    modal.show();

                    modal.getRoot().addClass('createtenantuser');

                    modal.getRoot().on(ModalEvents.hidden, function() {

                        modal.destroy();

                    });

                    return modal;

                });

           

           

         

                

           

          });

          $('body').on('click', '.ulclasstwo li .fbname', function(){

          if($(this).attr('attr')!='addnew'){

              var textnew2 = $(this).text();

              var feedbacktext = ' <span class="mainfed">'+textnew2+'</span>';               

              if($('#id_assignfeedbackcomments_editoreditable').find('p').length > 0){

                  $('#id_assignfeedbackcomments_editoreditable').find('p').append(feedbacktext);

                  $('#id_assignfeedbackcomments_editor').find('p').append(feedbacktext);

              } else{

                  $('#id_assignfeedbackcomments_editoreditable').append('<p>'+feedbacktext+'</p>');

                  $('#id_assignfeedbackcomments_editor').append('<p>'+feedbacktext+'</p>');

              }   
              $('#id_assignfeedbackcomments_editoreditable').focus();
            }

          });



           $('body').on('click', '.finalfeedback', function(){

           

              

                var modal = ModalFactory.create({

                    title: "Add new feedback",

                    body: '<div class="formfeedback"><form><input type="hidden" class = "feedtype" name="feedbacktype" value="3" /><textarea name="feedbacktext" class="insertfeedback"></textarea><button class ="savefeedback" type="button" value="Submit">Submit</button></form></div>',

                    large: true,

                    footer: '',

                }).then(function (modal) {

                    modal.show();

                    modal.getRoot().addClass('createtenantuser');

                    modal.getRoot().on(ModalEvents.hidden, function() {

                        modal.destroy();

                    });

                    return modal;

                });           

          

               

          

          });

          $('body').on('click', '.ulclassthree li .fbname', function(){

          if($(this).attr('attr')!='addnew'){

              var textnew3 = $(this).text();

              var feedbacktext = ' <span class="finalfed">'+textnew3+'</span>';               

              if($('#id_assignfeedbackcomments_editoreditable').find('p').length > 0){

                  $('#id_assignfeedbackcomments_editoreditable').find('p').append(feedbacktext);

                  $('#id_assignfeedbackcomments_editor').find('p').append(feedbacktext);

              } else {

                  $('#id_assignfeedbackcomments_editoreditable').append('<p>'+feedbacktext+'</p>');

                  $('#id_assignfeedbackcomments_editor').append('<p>'+feedbacktext+'</p>');

              }   
              $('#id_assignfeedbackcomments_editoreditable').focus();
            }

          });



            $(document).on('click', '.savefeedback', function () {

                var feedback = $('.insertfeedback').val().trim();

                var type = $('.feedtype').val();

                if (feedback == "") {              

                    alert('Please enter feedback!');

                    return false;

                }

                var promise1 = AJAX.call([{

                    methodname: 'mod_assign_insertfeedback',

                    args: {

                        feedback: feedback,

                        type: type,

                        asgid: asgid

                    }

                }]);

          

                promise1[0].done(function (json) {

                    var data = JSON.parse(json);                   

                    var sutype = "success";

                    if (data.result == true) {

                        sutype = "success";                       

                    } 

                    $('.formfeedback').html(data.message);

                    if(type==1){

                        $(".ulclassone li").eq(0).before($(data.htmltext));

                       

                    }

                    if(type==2){

                        $(".ulclasstwo li").eq(0).before($(data.htmltext));

                    }

                    if(type==3){

                        $(".ulclassthree li").eq(0).before($(data.htmltext));

                    }



                });  

                     

            }); 





            $(document).on('click', '.delete_feedback', function (e) {

                e.preventDefault();

                var id = $(this).attr("data-id");           

                var promise1 = AJAX.call([{

                    methodname: 'mod_assign_deletefeedback',

                    args: {

                        id: id,                           

                    }

                }]);

                promise1[0].done(function (json) {

                    var data = JSON.parse(json);                   

                    var sutype = "success";

                    if (data.result == true) {

                       sutype = "success";                       

                    } 

                   $('.formfeedback').html(data.message);

                });                

            });  



             $(document).on('click', '.delete_feedback', function (e) {

                e.preventDefault();

                $(this).parent().parent().remove();          

            });  







            $(document).on('click', '.edit_feedback', function (e) {

                e.preventDefault();

                var id = $(this).attr("data-id");

                var feedbackvalue = $(this).parent().parent().text();

                var modal = ModalFactory.create({

                    title: "Update feedback",

                    body: '<div class="formfeedback"><form><input type="hidden" class = "feedtype" name="feedbacktype" value="1" /><textarea name="feedbacktext" class="updfeedback">'+feedbackvalue+'</textarea><button class ="updatefeedback" type="button" value="Submit" data-id="'+id+'">Update</button></form></div>',

                    large: true,

                    footer: '',

                }).then(function (modal) {

                    modal.show();

                    modal.getRoot().addClass('createtenantuser');

                    modal.getRoot().on(ModalEvents.hidden, function() {

                        modal.destroy();

                    });

                    return modal;

                });              

            });



            $(document).on('click', '.updatefeedback', function (e) {

                e.preventDefault();

                var id = $(this).attr("data-id"); 

                var feedback = $('.updfeedback').val();      

                var promise2 = AJAX.call([{

                    methodname: 'mod_assign_updatefeedback',

                    args: {

                        id: id,

                        feedback: feedback,                           

                    }

                }]);

                promise2[0].done(function (json) {

                    var data = JSON.parse(json);                   

                    var sutype = "success";

                    if (data.result == true) {

                       sutype = "success";                       

                    } 

                    $('.formfeedback').html(data.message);

                    $('li[data-id='+id+']').find('.fbname').text(feedback);

                });                

            });             

        }

 };

 return usersTable;



});