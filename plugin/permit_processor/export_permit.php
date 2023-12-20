<?php
ob_start();
session_start();
if (!isset($_SESSION['usersid'])) {
    $_SESSION['usersid'] = array();
}
if(!isset($_SESSION['expuserid'])){
    $_SESSION['expuserid'] = array();
}
echo "<pre>";
print_r($_SESSION['userid']);
print_r($_SESSION['expuserid']);
echo "</pre>";


require_once("wslexport.php"); 

if (isset($_POST["wslexp"])) {
    wslexport();
};
if (isset($_POST["printexp"])) {
    printexport();
};
function export_permit()
{
    $unsetajax = plugins_url() . "/permit_processor/unsetajax.php";
    // $wslexport = plugins_url() . "/permit_processor/wslexport.php";
    $expajax = plugins_url() . "/permit_processor/expoajax.php";
?>



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <style>
        /* Start progress bar Styling */


        .multi-step-bar {
            overflow: hidden;
            counter-reset: step;
            width: 92%;
            margin: 40px auto;
            display: flex;
            justify-content: center;
        }

        .multi-step-bar li {
            text-align: center;
            list-style-type: none;
            color: #363636;
            text-transform: CAPITALIZE;
            width: 16.65%;
            float: left;
            position: relative;
            font-weight: 600;
        }

        .multi-step-bar li:before {
            content: counter(step);
            counter-increment: step;
            width: 30px;
            line-height: 30px;
            display: block;
            font-size: 12px;
            color: #dea333;
            background: #F2F4F7;
            border-radius: 50%;
            margin: 0 auto 5px auto;
            -webkit-box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
            -moz-box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
            box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
        }

        .multi-step-bar li:after {
            content: '';
            width: 100%;
            height: 2px;
            background: #898989;
            position: absolute;
            left: -50%;
            top: 15px;
            z-index: -1;
        }

        .multi-step-bar li:first-child:after {
            content: none;
        }


        .multi-step-bar li.active:before {
            background: #dea333;
            color: white;
            content: "\2713";
            border: none;

        }

        .multi-step-bar li:before {
            content: " ";
            counter-increment: step;
            width: 30px;
            line-height: 30px;
            display: block;
            font-size: 12px;
            color: #dea333;
            background: #ffffff;
            border: 10px solid #dea333;
            border-radius: 50%;
            margin: 0 auto 5px auto;
            -webkit-box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
            -moz-box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
            box-shadow: 0 6px 20px 0 rgba(69, 90, 100, 0.15);
            height: 30px;
        }

        .multi-step-bar li.active+li:after {
            background: #dea333;
        }



        /* End progress bar Styling */

        body {
            overflow-x: hidden;
        }

        .wrapper-conete {
            padding-left: 60px;
            padding-right: 60px;
        }


        .header-title-btn {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            overflow-x: hidden;
            margin-top: 20px;
        }

        .left-col>span {
            font-size: 20px;
            font-weight: 600;
        }

        .left-col p {
            font-size: 16px;
        }

        .right-col {
            display: flex;
            justify-content: end;
        }

        button.nav-link {
            width: 280px;
        }

        button.nav-link.active {
            background-color: #FEF4DB !important;
            color: #DEAC33 !important;
            font-weight: 700;
        }


        button.nav-link {
            width: 197px;
            border: 1px solid #aaa !important;
            padding-bottom: 10px;
            color: #000 !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            font-size: 13px;
        }

        button#home-tab {
            border-top-right-radius: 0px !important;
            border-bottom-right-radius: 0px !important;
        }


        button#profile-tab {
            border-top-left-radius: 0px !important;
            border-bottom-left-radius: 0px !important;
        }

        .right-col .rex {
            width: 60px;
            margin: 10px;
            padding: 5px 7px;
            border: 1px solid #aaa;
            border-radius: 10px;
            text-align: center;
            align-self: center;
        }

        .right-col .rex i {
            font-size: 20px;
            font-weight: bolder;
        }


        .histot {
            padding: 9px 18px;
            width: 56px;
            height: 39px;
            border: 1px solid;
            border-radius: 10px;
            vertical-align: middle;
            position: relative;
        }

        button.bt-1,
        button.bt-2 {
            margin: 22px;
            width: auto;
            border: none;
            border-radius: 10px;
            padding: 9px;
            background-color: #dea333;
            color: #fff;
        }

        button.bt-3 {
            border: none;
            background-color: red;
            width: auto;
            margin: 22px;
            border-radius: 10px;
            padding: 9px;
            color: #fff;
        }

        ul#myTab {
            border: none;
        }



        section#table-section h5 {
            color: #4e4d4d;
            font-size: 16px;
            padding: 5px;
        }

        table.table thead th {
            font-weight: normal;
            color: #6d6b6b;
            padding: 20px;
        }

        table.table.table-striped.table-hover {
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        }


        table.table thead th {
            font-weight: normal;
            color: #6d6b6b;
            padding: 20px;
            border-right: 1px solid #efebeb;
            /* border-radius: 20px; */
            font-size: 12px;
        }


        .title-parent h6 {
            margin-bottom: 0;
        }

        small {
            color: #aaa;
            font-size: 14px;
        }

        table.table td {
            border-right: 1px solid #dfdbdb;
        }

        table.table thead th i {
            color: #dea333;
            margin-right: 10px;
        }


        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            align-self: center;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #dea333;
            background-color: #dea333;
        }

        input[type="checkbox"]#checkAll{
            display: inline-block;
        }

        .title-parent+small {
            margin-left: 30px;
        }


        input[type=checkbox]:checked::before {
            filter: brightness(9.0);
        }

        .title-parent h6 a {
            color: #000;
        }

        .pop-report-history {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 60px;
            right: 0;
            background-color: #fff;
            transition: all .5s;
            /* height: 100vh; */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all .5s;
        }


        span.badge.btnclass.active {
            background-color: #f3ddb4;
            color: #dea333;
            padding: 12px;
            margin: 10px;
            width: 100px;
            height: 42px;
            vertical-align: middle;
        }


        span.badge.btnclass {
            color: #aaa;
            padding: 12px;
            margin: 10px;
            width: 100px;
            height: 42px;
            vertical-align: middle;
        }


        /* span badge styling */

        span.badge.rep {
            padding: 4px;
            width: 60px;
            height: 28px;
            color: #e06e6e;
            background-color: #fdfde4;
            border-radius: 10px;
        }

        span.badge.new {
            background-color: #d0f3d0 !important;
            color: green !important;
            height: 24px;
        }

        span.badge.up {
            background-color: #afdef1;
            color: #0b90c7;
        }

        /* span badge styling */
        .right-col {
            display: flex;
            justify-content: end;
            align-items: center;
        }

        /* MEDIA */

        @media (max-width:1686px) {
            button.bt-3 {
                font-size: 14px;
            }
        }

        .trdata {
            display: none;
        }

        #headerTable.class_12 .class12,
        #headerTable.class_13 .class13 {
            display: table-row;
        }



        /* Start MEDIA */


        @media (max-width:1026px) {
            .wrapper-conete {
                padding-left: 20px;
                padding-right: 20px;
            }
        }


        @media (max-width:1222px) {
            .header-title-btn {
                flex-direction: column;
                align-items: baseline;
            }

            button.bt-1 {
                white-space: nowrap;
            }

            button.bt-2 {
                white-space: nowrap;
            }

            button.bt-3 {
                white-space: nowrap;
            }
        }

        @media (max-width:767px) {
            .multi-step-bar li {
                width: 32.65%;
            }

            .wrapper-conete {
                padding-left: 20px;
                padding-right: 20px;
            }

            .right-col {
                display: flex;
                flex-direction: column;
                align-items: baseline;
            }

            .right-col .rex {
                align-self: baseline;
            }
        }



        @media (max-width:430px) {
            button#home-tab {
                border-top-right-radius: 10px !important;
                border-bottom-right-radius: 10px !important;
            }

            button#profile-tab {
                border-top-left-radius: 10px !important;
                border-bottom-left-radius: 10px !important;
            }
        }


        @media (max-width:412px) {
            .multi-step-bar li {
                font-size: 11px;
                margin: 7px;
            }

            .multi-step-bar {
                width: 100%;
                padding: 0;
            }
        }


        /* End MEDIA */
    </style>


    <div class="wrapper-conete">

        <div class="progresstion-clip">
            <ul class="multi-step-bar">
                <li class="stepbtnslist active">Step1 :Select Students</li>
                <li class="stepbtnnew active">Step2 :Assign New Permit</li>
                <li class="stepbtnup active ">Step3 :Upgrade Permit</li>
                <li class="stepbtnrep active">Step4 :Replace Permit</li>
                <li class="stepbtnexp current">Step5: Export</li>
            </ul>
        </div>

        <div class="header-title-btn w-100">

            <div class="left-col col-sm-6">
                <span class="mb-2">Export</span>
                <p>Select the formate you want to export the list (Printing).</p>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Export For Printing</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                            Export For WSLCB Report</button>
                    </li>
                </ul>
            </div>

            <div class="right-col col-sm-6">
                <div class="histot"><i class="fa-sharp fa-solid fa-clock-rotate-left"></i></div>
                <form method="POST">
                    <button class="bt-2" id="btnExport" name="printexp" >Export for Printing </button>
                    <button type="submit" class="bt-2 d-none" name="wslexp" >Export WSLCB Reports </button>
                </form>
                <button class="bt-3 backToStudent" >Clear & Back to Student List</button>
            </div> 

        </div>

        <hr style="width: 100%;">

        <div class="dex table-responsive">
            <section id="table-section" class="license w-100">

                <h5>License Type:</h5>
                <div class="badge-parent d-flex mb-5">
                    <span class="badge btnclass active">Class 12</span>
                    <span class="badge btnclass">Class 13</span>
                </div>


                <table id="headerTable" class="table table-striped table-hover class_12">
                    <thead>
                        <th style="width: 20%;"><input type="checkbox" id="checkAll" />Name</th>
                        <th>Permit</th>
                        <th>Sex</th>
                        <th>DOB</th>
                        <th>Ht</th>
                        <th>Wt</th>
                        <th>Adress & Phone</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zip Code</th>
                        <th>Passed Exam</th>
                        <th>Student Number</th>
                        <th>License Type</th>
                    </thead>

                    <?php

                    foreach ($_SESSION['usersid'] as $useid => $value) {
                        $newuser_metaa = get_user_meta($useid);
                        // if((!empty($newuser_metaa['permit_issued_option_no'][0])) && (!empty($newuser_metaa['permit_number'][0]))){
                        //  echo "<pre>-------------";
                        //  print_r($newuser_metaa);
                        //  echo "</pre>";
                        $ht_ft = round($newuser_metaa['height_ft_opt'][0]);
                        $ht_in = round($newuser_metaa['height_in_opt'][0]);
                        $height = $ht_ft . "'" . $ht_in;
                        $udob = date('Y-m-d', strtotime($newuser_metaa['date_of_birth'][0]));
                        $dob = new DateTime($udob);
                        $now = new DateTime();
                        $difference = $now->diff($dob);
                        $age = $difference->y;
                        $mynumber = $newuser_metaa['permit_number'][0];
                        $get_pernumber = substr($mynumber, 0, 2);
                        if ($age >= 21) {
                                                   
                            echo '<tr class="trdata class12">
                            <td>
                                <div class="title-parent d-flex">
                                    <input type="checkbox" value="'. $useid .'" ' . (isset($_SESSION['expuserid'][$useid]) ? 'checked' : '') .' id="">
                                    <h6>' . $newuser_metaa["first_name"][0] . ' ' . $newuser_metaa["last_name"][0] . '</h6>';

                                    if ((!empty($newuser_metaa['permit_issued_option_no'][0])) && (!empty($newuser_metaa['permit_number'][0]))) {
                                        echo '<span class="badge rep">Replace</span>';
                                    } else if (empty($newuser_metaa['permit_number'][0])) {
                                        echo '<span class="badge new">New</span>';
                                    }else if ($get_pernumber == '13'){
                                        echo '<span class="badge up">Upgrade</span>';
                                    } 

                            echo '</div>
                                <small>' . $newuser_metaa["billing_email"][0] . '</small>
                            </td>
                            <td>' . ($newuser_metaa['permit_number'][0]) . '</td>
                            <td>' . (($newuser_metaa['user_gender'][0] == "Male") ? "M" : "F") . '</td>
                            <td>' . date("d/m/Y", strtotime($newuser_metaa['date_of_birth'][0])) . '</td>
                            <td>' . $height . '"</td>
                            <td>' . $newuser_metaa['weight_lbs'][0] . '</td>
                            <td>
                                ' . $newuser_metaa['billing_address_1'][0] . '<br>
                                <small>' . $newuser_metaa['billing_phone'][0] . '</small>
                            </td>
                            <td>' . $newuser_metaa['billing_city'][0] . '</td>
                            <td>' . $newuser_metaa['billing_state'][0] . '</td>
                            <td>' . $newuser_metaa['billing_postcode'][0] . '</td>
                            <td>' . date("d/m/Y", $newuser_metaa['course_completed_900'][0]) . '</td>
                            <td>' . $useid . '</td>
                            <td> Class 12 </td>
                        </tr>';
                        } else if ($age > 17 || $age < 21) {
                            echo '<tr class="trdata class13">
                            <td>
                                <div class="title-parent d-flex">
                                    <input type="checkbox" value="' . $useid . '" ' . (isset($_SESSION['expuserid'][$useid]) ? 'checked' : '') . ' id="">
                                    <h6>' . $newuser_metaa["first_name"][0] . ' ' . $newuser_metaa["last_name"][0] . '</h6>';
                            if ((!empty($newuser_metaa['permit_issued_option_no'][0])) && (!empty($newuser_metaa['permit_number'][0]))) {
                                echo '<span class="badge rep">Replace</span>';
                            } else if (empty($newuser_metaa['permit_number'][0])) {
                                echo '<span class="badge new">New</span>';
                            }
                            echo '</div>
                                <small>' . $newuser_metaa["billing_email"][0] . '</small>
                            </td>
                            <td>' . ($newuser_metaa['permit_number'][0]) . '</td>
                            <td>' . (($newuser_metaa['user_gender'][0] == "Male") ? "M" : "F") . '</td>
                            <td>' . date("d/m/Y", strtotime($newuser_metaa['date_of_birth'][0])) . '</td>
                            <td>' . $height . '"</td>
                            <td>' . $newuser_metaa['weight_lbs'][0] . '</td>
                            <td>
                                ' . $newuser_metaa['billing_address_1'][0] . '<br>
                                <small>' . $newuser_metaa['billing_phone'][0] . '</small>
                            </td>
                            <td>' . $newuser_metaa['billing_city'][0] . '</td>
                            <td>' . $newuser_metaa['billing_state'][0] . '</td>
                            <td>' . $newuser_metaa['billing_postcode'][0] . '</td>
                            <td>' . date("d/m/Y", $newuser_metaa['course_completed_900'][0]) . '</td>
                            <td>' . $useid . '</td>
                            <td> Class 13 </td>
                        </tr>';
                        }
                    }

                    ?>

                </table>

            </section>
        </div>

    </div>

    <input type="text" id="totalselected" value=" <?php echo sizeof($_SESSION['expuserid']); ?> " />
    <input type="text" id="totaladdedids" value=" <?php echo implode(',', array_keys($_SESSION['expuserid'])); ?> " />

    <script src="//code.jquery.com/jquery-1.12.4.js"></script>

    <script>
        $(document).ready(function() {
            $(".btnclass").click(function() {
                $(this).closest('span').addClass("active");
                $(this).siblings().removeClass("active");
                $("#headerTable").toggleClass("class_12 class_13")
                // $(this).addClass('active');
            });
        });





      $(document).ready(function(){
       $("button#profile-tab").click(function(){
           $(".badge-parent").addClass('d-none');
           $("#headerTable input#checkAll").addClass('d-none');
           $(".title-parent input").addClass('d-none');
           $(".right-col button.bt-2").removeClass('d-none');
           $("button#btnExport").addClass('d-none');

          });   
       });




       $(document).ready(function(){
          $("button#home-tab").click(function(){
           $(".badge-parent").removeClass('d-none');
           $("#headerTable input#checkAll").removeClass('d-none');
           $(".title-parent input").removeClass('d-none');
           $(".right-col button.bt-2").addClass('d-none');
           $("button#btnExport").removeClass('d-none');
          });   
       });


       
       $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
            var yourArray = [];
            $('input:checkbox').not(this).prop('checked', this.checked).each(function(){
                yourArray.push($(this).val());
            });
            console.log('ddddd',yourArray);
            $.ajax({
                type: "POST",
                url: '<?php echo $expajax; ?>',
                data: { userid : yourArray, checked: $(this).is(":checked") },
                success: function(response) {
                    console.log('response1111', response);
                    var resp = $.parseJSON(response);
                    console.log('resppppppppp', resp);
                    $('#totalselected').val(resp.totaladded);
                    $('#totaladdedids').val(resp.totaladdedids.join(","));
                    console.log('resp', $('#totalselected').val());

                }
            });
           
        });


        $(document).on("change", '[type="checkbox"]', function() {
            var checked = $(this).is(":checked");
            var allcheckdata = $(this).val();
            if(allcheckdata != 'on'){
                var args = {
                    userid: $(this).val(),
                    checked: $(this).is(":checked")
                };
                console.log("changed", args);
                $.ajax({
                    type: "POST",
                    url: '<?php echo $expajax; ?>',
                    data: args,
                    success: function(response) {
                        console.log('response', response);
                        var resp = $.parseJSON(response);
                        console.log('resp', resp);
                        $('#totalselected').val(resp.totaladded);
                        $('#totaladdedids').val(resp.totaladdedids.join(","));
                        console.log('resp', $('#totalselected').val());

                    }
                });
            }

        });



        $(document).ready(function() {

            $(".backToStudent").click(function() {
                console.log('aaaaaaaaaaaaaaaaaaaaaa');
                $(".stepbtnexp").addClass("active");
                $.ajax({
                    type: "POST",
                    url: '<?php echo $unsetajax; ?>',
                    success: function(response) {
                        console.log('rese1111', response);
                    }
                });
                 <?php //unset($_SESSION['usersid']); ?>
                window.location.href = '/wp-admin/admin.php?page=assignpermit';
            });

            $(".histot").click(function() {
                $(".pop-report-history").removeClass("d-none");
            });

            $("button.bt-2.closer").click(function() {
                $(".pop-report-history").addClass("d-none");
            });

        });

    </script>

<?php
};
