<?php /* Template Name: User Register */ ?>
<?php get_header();?>
<script>
   jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex')
    //console.log("$('#content:first-child')",jQuery('#content:first-child'));
</script>
<!-- <h1>Hello</h1> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/style.css" rel="stylesheet">

    <div class="row">
        <div class="col-12 col-lg-6 col-md-6">
            <div class="login-background">
                <div class="wrapper-content">
                    <img src="http://122.176.46.118/learnoneplanet/wp-content/uploads/2023/03/Picture__2.png" alt="" class="img-fluid" />
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-md-6" style="margin-top: 100px;">
            <div class="form-container">
                <div class="form-wrap">
                    <h1 class="display-1 fw-bold mb-5">Register</h1>
                    <form method="post">
                            <div class="wrap">
                                <div class="floating-label-group">
                                    <input type="text" id="fname" class="form-control" autocomplete="off" autofocus required />
                                    <label class="floating-label">First Name</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="text" id="lname" class="form-control" autocomplete="off" autofocus required />
                                    <label class="floating-label">Last Name</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="text" id="username" class="form-control" autocomplete="off" autofocus required />
                                    <label class="floating-label">Username</label>
                                </div>
                                <div class="floating-label-group">
                                    <input type="email" id="email" class="form-control" autocomplete="off" autofocus required />
                                    <label class="floating-label">E-mail</label>
                                </div>
                                <input type="submit" value="Submit" class="w-100"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
       $(function () {

            $('form').on('submit', function (e) {
                e.preventDefault();
            var formData = {
                    fname: $("#fname").val(),
                    lname: $("#lname").val(),
                    username: $("#username").val(),
                    email: $("#email").val(),
                };
                $.ajax({
                    type: 'post',
                    url: '/learnoneplanet/wp-content/themes/zakra/ajax.php',
                    data: formData, //sand data on url
                    success: function (responseData) { //return responseData (anyname)
                        console.log('formData',responseData);
                    }
                });

            });

        });
    </script>
<?php get_footer();?>
