<?php
ob_start();
session_start();
if (!isset($_SESSION['usersid'])) {
    $_SESSION['usersid'] = array();
}

function user_assign_permit()
{
    $plugingpath = plugins_url() . "/permit_processor/permit_processor_ajax.php";
    $assignpath = plugins_url() . "/permit_processor/ajax.php";
    global $wpdb;
    $sql = 'SELECT * FROM ' . $wpdb->users . ' Where ID>1  ORDER BY ID DESC ';
    $users = $wpdb->get_results($sql);
    date_default_timezone_set('America/Los_Angeles');
    date_default_timezone_get();
?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <style>

        .dt-buttonss span {
                padding: 12px;
                margin-top: 18px;
                background-color: #FEF4DB !important;
                border-radius: 10px;
                border-radius: 5px;
                background-color: #efd29c;
                color: #dea333 !important;
            }

        #wpfooter p {
            display: none;
        }


        table.table thead th i {
            color: #dea333;
            margin-right: 10px;
        }

        table.table thead th {
            color: #aaa;
            font-size: 14px;
            font-weight: 100;
            font-weight: normal;
            color: #6d6b6b;
            padding: 20px;
            border-right: 1px solid #efebeb;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            align-self: center;
            padding: 20px;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #dea333;
            background-color: #dea333;
            margin-right: 10px !important;
        }

        .permitstatus {
            color: #b72b38;
        }

        .assign-new,
        .unassign-new,
        .reassign-new {
            padding: 10px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            width: 130px !important;
            text-align: center !important;
            color: #fff !important;
        }

        .dt-button {
            background-color: #f9bb00 !important;
            border-color: #46b8da !important;
        }

        .reassign-new:hover {
            background-color: #5bc0de !important;
            border-color: #46b8da !important;
        }

        #prod_cat_id {
            /*  color: #eb1111; */
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 10px 0;
            width: 50%;
            /*  background: #ff000017; */
            padding: 5px 0px 5px 5px;
        }

        #msg2 {
            color: #dc3545
        }

        #msg1 {
            color: #dc3545
        }

        .page_height {
            margin-bottom: 10px;
        }

        .dbfield {
            padding: 6px 10px;
            box-shadow: 0 0 5px 0 #ddd;
            border: 1px solid #ddd;
            color: #333;
            font-size: 14px;
        }

        form#testimonal {
            padding-top: 20px;
        }

        i.fa.fa-eye.btn-success {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        i.fa.fa-eye-slash.btn-danger {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        a.btn-gray {
            padding: 10px;
            background-color: #f5902b;
            border-radius: 4px;
            cursor: pointer;
        }

        a.btn-info {
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100px;
            display: block;
            text-align: center;
        }

        .btn-yellow {
            background-color: #d39e00;
            border-color: #c69500;
        }

        .btn-red {
            background-color: #bd2130;
            border-color: #b21f2d;
        }

        .btn-green {
            background-color: #5a6268;
            border-color: #545b62;
        }

        select.form_width {
            max-width: 100%;
        }

        .ui-datepicker td .ui-state-default {
            padding: 4px 6px !important;
        }

        .table_responsive1 {
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .btncsv {
            width: 100px;
            height: 30px;
            margin: 15px 0 0 0;
            text-align: center;
            line-height: 30px;
            transition: 0.5s;
        }

        .btncsv:hover {
            background: #fff !important;
            color: #555 !important;
        }

        .ui-datepicker select.ui-datepicker-month,
        .ui-datepicker select.ui-datepicker-year {
            color: #000 !important;
        }

        .row {
            width: 100%;
            margin: 0 auto;
        }

        .block {
            width: 300px;
            display: inline-block;
        }

        .but_search {
            width: 150px;
            border-radius: 3px;
            background: #ffb900;
        }

        .button {
            background: #fff;
            border: 1px solid #e4081233;
        }

        .ui-datepicker td.ui-datepicker-week-end {
            background-color: #fff !important;
            border: 1px solid #fff !important;
        }

        .ui-datepicker td {
            border: 0 !important;
            padding: 0 !important;
        }

        .enrol_select_btn {
            display: block;
        }

        .margin_top {
            margin-top: 27px !important;
            display: inline-block;
        }

        .mybtn {
            border: 2px solid #f9bb00 !important;
            background: #f9bb00 !important;
            padding: 5px !important;
            border: none !important;
            font-size: 15px !important;
            text-align: center;
            color: #fff;
            line-height: unset !important;
            min-height: unset !important;

        }

        .mybtn a {
            color: #fff !important;
        }

        .wrapper_enrollment input {
            width: 100%;
        }

        a.paginate_button {
            padding: 5px 8px;
            border: 1px solid #337ab7;
            margin-left: -1px;
            cursor: pointer;
        }

        .title_name {
            display: contents;
        }

        a.paginate_button.current {
            color: #fff;
            background: #337ab7;
        }


        /* Start progress bar styling */

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

        /* .multi-step-bar li:first-child:after {
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


        .multi-step-bar li:first-child::before {
            content: '';
            width: 100%;
            height: 2px;
            background: #898989;
            position: absolute;
            left: -50%;
            top: 15px;
            z-index: -1;

        } */

        .multi-step-bar li.current:before{
            border: 10px solid #dea333;
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
            border: 10px solid #aaaaaa42;
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



        .wrapper-conete {
            padding-left: 60px;
            padding-right: 60px;
        }



        .btnNextpage {
            padding: 16px 20px !important;
            border-radius: 13px !important;
            cursor: pointer;
        }

        .header-title-btn {
            /* display: flex; */
            align-items: center;
            margin-bottom: 20px;
            margin-top: 20px;
            overflow-x: hidden;
        }

        hr {
            border-top: 1px solid #000;
        }

        input#datepickerto,
        input#datepickerfrom {
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            font-size: 18px;
        }


        .pex-1 {
            width: 25%;
        }

        .pex-1 span {
            font-weight: bold;
        }

        input#permit_assign_date {
            padding: 20px;
            border-radius: 10px;
            font-size: 19px;
        }

        select#permit_issue,
        select#std_class {
            width: 98%;
            padding: 5px;
            border-radius: 10px;
            font-size: medium;
        }


        input.but_search {
            margin: 0 !important;
            width: auto;
            padding: 10px 20px !important;
            border-radius: 10px;
        }

        .dt-buttonss {
            margin: 10px 0;
            padding: 10px 0;
        }

        button.dt-button {
            background-color: #FEF4DB !important;
            background-color: #f1e1c39e;
            padding: 10px !important;
            width: auto !important;
            color: #dea333 !important;
            border-radius: 10px !important;
            border-color: #fef4db !important;
            font-size: 14px !important;
        }



        /* span badge styling */

         span.badge.rep {
            padding: 4px;
            width: 63px;
            height: 25px;
            color: #e06e6e;
            background-color: #fdfde4;
            border-radius: 10px;
        }


        span.badge.new {
            background-color: #d0f3d0 !important;
            color: green !important;
        }

        span.badge.up {
            background-color: #afdef1;
            color: #0b90c7;
        }

        span.badge {
            height: 20px;
            vertical-align: middle;
            margin: 5px;
        }
 
        .dt-buttonss span button {
            border: none;
            background: none;
        }

        /* span badge styling */




        /* START MEDIA */

        @media (max-width:1222px) {
            form.row.g-3 {
                flex-direction: column;
            }

            .pex-1 {
                width: 100%;
            }

            .pex-1 .row {
                flex-direction: column;
            }

            form.row.g-3 .col-md-2 {
                width: 100%;
                padding: 0;
            }

            .wp-core-ui select {
                max-width: 100%;
            }

            .header-title-btn .col-md-6 {
                width: 100% !important;
                text-align: left !important;
                margin: 20px;
            }

            .multi-step-bar {
                width: 100% !important;
                padding: 0;
            }

            .multi-step-bar li {

                width: 33.65%;

            }
        }



        @media (max-width:657px) {
            .wrapper-conete {
                padding-left: 20px;
                padding-right: 20px;
            }

            .multi-step-bar li {
                width: 33.65%;
                font-size: 10px;
            }
        }



        @media (max-width:412px) {
            .wrapper-conete {
                padding-left: 10px;
                padding-right: 10px;
            }
        }


        /* End MEDIA */
    </style>


    <div class="wrapper-conete">

        <div class="progresstion-clip">
            <ul class="multi-step-bar">
                <li class="stepbtnslist current">Step1 :Select Students</li>
                <li class="stepbtnnew ">Step2 :Assign New Permit</li>
                <li class="stepbtnup">Step3 :Upgrade Permit</li>
                <li class="stepbtnrep">Step4 :Replace Permit</li>
                <li class="stepbtnexp">Step5: Export</li>
            </ul>
        </div>

        <div class="container-fluid header-title-btn  mt-4 p-0" style="padding-left: 20px !important; padding-right:20px !important;">
            <div class="row mt-4 ">
                <div class="col-md-6 p-0">
                    <div class="row">
                        <div class="col-md-12 p-0">
                            <div class="title">
                                <?php
                                $totaluser = "SELECT user_id FROM wp_learndash_user_activity WHERE activity_type='quiz' AND activity_status=1 GROUP BY user_id ORDER BY activity_id DESC";
                                $totalcount = $wpdb->get_results($totaluser);
                                $totalcountst = count($totalcount);


                                ?>
                                <h3 class="title_name">Student List</h3> <span class="bg-warning p-2 badge rounded-pill"> <?php echo $totalcountst; ?> student</span>
                            </div>
                        </div>
                        <div class="col-md-12 p-0">
                            <div class="subtitlee m-2">Filter and select students you want to process and assign permit.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-white text-decoration-none bg-success px-3 py-2 rounded btnNextpage" id="btn">Continue to Next Step</span>
                </div>
            </div>
            <hr style="width: 99%;">
        </div>

        <div class="container-fluid  mt-4">
            <div class="row">
                <div class="col">
                    <form class="row g-3 justify-content-between " method="POST">
                        <div class="pex-1">
                            <div class="row justify-content-between">

                                <!-- <span for="From mb-5">Student Number</span><br> -->

                                <div class="col-lg-6 p-1">
                                    <label for="date_to">FROM</label>
                                    <!-- <input type="text" class="form-control" name="from" id="datepickerfrom" placeholder="From" value="<?php // if(!empty($_POST['from'])){echo $_POST['from']; }
                                                                                                                                            ?>"> -->
                                    <input type="text" class="form-control enrol_input" name="date_to" id="datepickerfrom" placeholder="From" value="<?php if (!empty($_POST['date_to'])) {echo $_POST['date_to'];} ?>">
                                </div>
                                <div class="col-lg-6 p-1">
                                    <label for="date_from">TO</label>
                                    <!-- <input type="text" class="form-control" name="to" id="datepickerto" placeholder="To" value="<?php // if(!empty($_POST['to'])){echo $_POST['to']; }
                                                                                                                                        ?>"> -->
                                    <input type="text" class="form-control enrol_input" name="date_from" id="datepickerto" placeholder="To" value="<?php if (!empty($_POST['date_from'])) {echo $_POST['date_from'];} ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="date">Permit Assigned Date</label>
                            <input type="text" class="form-control" name="permit_assign_date" id="permit_assign_date" placeholder="Select Date" value="<?php if (!empty($_POST['permit_assign_date'])) {echo $_POST['permit_assign_date'];} ?>">
                        </div>

                        <div class="col-md-2 ">
                            <div class="wrapper_enrollment"><label class="enrol_field">Permit Issue </label>
                                <select class="enrol_select_btn enrol_input" id="permit_issue" name="permit">
                                    <option <?php if (!empty($_POST['permit']) && $_POST['permit'] == '1') {
                                                echo "selected";
                                            } ?> value="1"> Select One </option>
                                    <option <?php if (!empty($_POST['permit']) && $_POST['permit'] == 'permit_issued_option_yes') {
                                                echo "selected";
                                            } ?> value="permit_issued_option_yes"> Yes </option>
                                    <option <?php if (!empty($_POST['permit']) && $_POST['permit'] == 'permit_issued_option_no') {
                                                echo "selected";
                                            } ?> value="permit_issued_option_no"> No </option>
                                    <option <?php if (!empty($_POST['permit']) && $_POST['permit'] == 'null') {
                                                echo "selected";
                                            } ?> value="null"> NULL </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="wrapper_enrollment"><label class="enrol_field">Class </label>
                                <select class="enrol_select_btn enrol_input" id="std_class" name="std_class">
                                    <option <?php if (!empty($_POST['std_class']) && $_POST['std_class'] == '1') {
                                                echo "selected";
                                            } ?> value="1"> Select One </option>
                                    <option <?php if (!empty($_POST['std_class']) && $_POST['std_class'] == 'class_12') {
                                                echo "selected";
                                            } ?> value="class_12"> class 12 </option>
                                    <option <?php if (!empty($_POST['std_class']) && $_POST['std_class'] == 'class_13') {
                                                echo "selected";
                                            } ?> value="class_13"> Class 13</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <br />
                            <!-- <a href="" class="bg-warning text-decoration-none p-2 rounded text-white mt-3">Apply Filter</a> -->
                            <p><input class="but_search margin_top mybtn" type='submit' name='but_search' value='Apply Filter'></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="process_list mt-1 mb-2"> <span id="totalselecteds"> <?php echo sizeof($_SESSION["usersid"]); ?></span>/

<?php  echo $totalcountst; ?>


         Add selected users to processing list: </div>


        <?php
        $btnrec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND activity_status=1 GROUP BY user_id ORDER BY activity_id DESC";
        $btnRecords = $wpdb->get_results($btnrec_query);
        $ncount = 0;
        $Ucount = 0;
        $Rcount = 0;
        foreach ($btnRecords as $eRecords) {
            $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
            $getTitle = $wpdb->get_row($QurcourseTitle);
            if ($getTitle) {
                $eRecords->user_id;
                $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
                $newusers = $wpdb->get_results($newsql);
                $newcount = 0;
                $UPcount = 0;
                $recount = 0;
                foreach ($newusers as $newuser) {

                    $newuser_meta = get_userdata($newuser->ID);
                    if (!empty($newuser_meta->permit_number) && (!empty($newuser_meta->permit_issued_option_yes))) {
                        $userDob = $newuser_meta->date_of_birth;
                        $udob = date('Y-m-d', strtotime($userDob));
                        $dob = new DateTime($udob);
                        $now = new DateTime();
                        $difference = $now->diff($dob);
                        $age = $difference->y;
                        if ((substr($newuser_meta->permit_number, 0, 2) == '13') && ($age >= 21)) {
                            $UPcount++;
                        }
                    };

                    if (!empty($newuser_meta->permit_number) && (!empty($newuser_meta->permit_issued_option_no))) {
                        $recount++;
                    }
                    if (empty($newuser_meta->permit_number)) {
                        $newcount++;
                    };
                }
                $Rcount = ($Rcount + $recount);
                $Ucount = ($Ucount + $UPcount);
                $ncount = ($ncount + $newcount);
            }
        }

        ?>

        <div class="dt-buttonss">
            <form method="post" >
                <span><button type="submit" name="submit" value="new" >Assign New Permit (<?php echo $ncount; ?>)</button></span>
                <span><button type="submit" name="submit" value="replace" >Replace Permit (<?php echo $Rcount; ?>)</button></span>
                <span><button type="submit" name="submit" value="upgrade" >Upgrade Permit (<?php echo $Ucount; ?>)</button></span>
            </form>
        </div>

        <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <div class="table-responsive">
            <table class="table table-bordered mt-5" id="example">
                <thead>
                    <tr class="text-center table-active">
                        <th scope="col">
                            <div class="title-parent d-flex"><input type="checkbox" id="checkAll" />
                                <h6>Name</h6>
                            </div>
                        </th>
                        <th scope="col">Permit</th>
                        <th scope="col">Permit Assigned</th>
                        <th scope="col">Permit Status</th>
                        <th scope="col">Sex</th>
                        <th scope="col">DOB</th>
                        <th scope="col">Ht</th>
                        <th scope="col">Wt</th>
                        <th scope="col">Exp</th>
                        <th scope="col">Address</th>
                        <th scope="col">City</th>
                        <th scope="col">State</th>
                        <th scope="col">Postcode</th>
                        <th scope="col">Student Number</th>
                        <th scope="col">License type</th>
                    </tr>
                </thead>


                <?php

                function date_compare_asc($element1, $element2)
                {
                    $datetime1 = strtotime($element1->dob);
                    $datetime2 = strtotime($element2->dob);
                    return ($datetime1) - ($datetime2);
                }

                function date_compare_desc($element1, $element2)
                {
                    $datetime1 = strtotime($element1->dob);
                    $datetime2 = strtotime($element2->dob);
                    return ($datetime2) - ($datetime1);
                }

                ////////////////////////////////////////////////////////////////////////////////////////////////
                if (isset($_POST['but_search'])) {
                    $permit = $_POST['permit'];
                    if ($permit == "") {
                        $permit = 1;
                    } else {
                        $permit = $_POST['permit'];
                    }
                    $std_class = $_POST['std_class'];

                    $complete = " AND activity_status=1";
                    $fromDate = strtotime($_POST['date_to']);
                    $endDate = strtotime($_POST['date_from']);
                    if ($fromDate == "" or $endDate == "") {
                        $datefltr = "";
                    } else {
                        $datefltr = " AND activity_completed between " . $fromDate . " AND " . $endDate;
                    }

                    $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status=1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                    $newRecordss = $wpdb->get_results($rec_query);

                    if ($permit == 1) {

                        if ($std_class == 1) {
                            // $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status=1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            // $newRecordss = $wpdb->get_results($rec_query);
                            foreach ($newRecordss as $key => $newRecordsss) {
                                $newarray[$key] = $newRecordsss;

                            }
                            $newRecordss = $newarray;
                        } else if ($std_class != 1) {

                            $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status=1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            $newRecordss = $wpdb->get_results($rec_query);
                            $newarray = array();
                            foreach ($newRecordss as $key => $newRecordsss) {
                                if ($std_class == 'class_12') {
                                    $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newRecordsss->user_id . "'");
                                    $userDob = $student_age->meta_value;
                                    $udob = date('Y-m-d', strtotime($userDob));
                                    $dob = new DateTime($udob);
                                    $now = new DateTime();
                                    $difference = $now->diff($dob);
                                    $age = $difference->y;
                                    if ($age >= 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_users WHERE ID ='" . $student_id . "'";
                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (!empty($eRecordss)) {
                                            $newarray[$key] = $newRecordsss;
                                        }
                                    }
                                } else if ($std_class == 'class_13') {
                                    $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newRecordsss->user_id . "'");
                                    $userDob = $student_age->meta_value;
                                    $udob = date('Y-m-d', strtotime($userDob));
                                    $dob = new DateTime($udob);
                                    $now = new DateTime();
                                    $difference = $now->diff($dob);
                                    $age = $difference->y;
                                    if ($age < 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_users WHERE ID ='" . $student_id . "'";
                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (!empty($eRecordss)) {
                                            $newarray[$key] = $newRecordsss;
                                        }
                                    }
                                }
                            }
                            $newRecordss = $newarray;
                        }
                    } elseif ($permit == "permit_issued_option_yes" or $permit == "permit_issued_option_no") {
                        if ($std_class == 1) {
                            $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status=1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            $newRecordss = $wpdb->get_results($rec_query);
                            foreach ($newRecordss as $key => $newRecordsss) {
                                $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $newRecordsss->user_id . "'";
                                $eRecordss = $wpdb->get_results($premQuer);
                                if (empty($eRecordss)) {
                                    unset($newRecordss[$key]);
                                }
                            }
                        } else if ($std_class != 1) {
                            $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status=1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            $newRecordss = $wpdb->get_results($rec_query);
                            $newarray1 = array();
                            foreach ($newRecordss as $key => $newRecordsss) {
                                $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newRecordsss->user_id . "'");
                                $userDob = $student_age->meta_value;
                                $udob = date('Y-m-d', strtotime($userDob));
                                $dob = new DateTime($udob);
                                $now = new DateTime();
                                $difference = $now->diff($dob);
                                $age = $difference->y;
                                if ($std_class == 'class_12') {
                                    if ($age >= 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $student_id . "'";
                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (!empty($eRecordss)) {
                                            $newarray1[$key] = $newRecordsss;
                                        }
                                    }
                                } else if ($std_class == 'class_13') {
                                    if ($age < 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE '" . $permit . "' AND user_id='" . $student_id . "'";
                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (!empty($eRecordss)) {
                                            $newarray1[$key] = $newRecordsss;
                                        }
                                    }
                                }
                            }
                            $newRecordss = $newarray1;
                        }
                    } else if ($permit == "null") {
                        if ($std_class == 1) {
                            $rec_query1 = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status = 1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            $newRecordss = $wpdb->get_results($rec_query1);
                            foreach ($newRecordss as $key => $newRecordsss) {
                                $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $newRecordsss->user_id . "'";
                                $eRecordss = $wpdb->get_results($premQuer);
                                if (!empty($eRecordss)) {
                                    unset($newRecordss[$key]);
                                }
                            }
                        } else if ($std_class != 1) {
                            $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz'  AND activity_status = 1 " . $datefltr . "" . $complete . " GROUP BY user_id ORDER BY activity_id DESC";
                            $newRecordss = $wpdb->get_results($rec_query);
                            $newarray1 = array();
                            foreach ($newRecordss as $key => $newRecordsss) {
                                $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newRecordsss->user_id . "'");
                                $userDob = $student_age->meta_value;
                                $udob = date('Y-m-d', strtotime($userDob));
                                $dob = new DateTime($udob);
                                $now = new DateTime();
                                $difference = $now->diff($dob);
                                $age = $difference->y;
                                if ($std_class == 'class_12') {
                                    if ($age >= 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $student_id . "'";

                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (empty($eRecordss)) {
                                            $newarray1[$key] = $newRecordsss;
                                        }
                                    }
                                } else if ($std_class == 'class_13') {
                                    if ($age < 21) {
                                        $student_id = $student_age->user_id;
                                        $premQuer = "SELECT * From wp_usermeta WHERE meta_key LIKE 'permit_issued_option%'  AND user_id='" . $student_id . "'";

                                        $eRecordss = $wpdb->get_results($premQuer);
                                        if (empty($eRecordss)) {
                                            $newarray1[$key] = $newRecordsss;
                                        }
                                    }
                                }
                            }
                            $newRecordss = $newarray1;
                        }
                        //die;       
                    }

                    // echo "<pre>";
                    // print_r($_POST);
                    // echo "===============";
                    // print_r($newRecordss);
                    // echo "<pre>";
                    // die;
                     $countdata =  sizeof($newRecordss);
                    $_SESSION['countuser']=$countdata;
                    foreach ($newRecordss as $eRecords) {
                        $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
                        $getTitle = $wpdb->get_row($QurcourseTitle);
                        if ($getTitle) {
                            $eRecords->user_id;
                            $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
                            $newusers = $wpdb->get_results($newsql);
                            foreach ($newusers as $newuser) {

                                $newuser_meta = get_userdata($newuser->ID);
                                $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_meta->permit_number . '');
                                if (!empty($permitdate)) {
                                    $newuser_meta->permitdate = $permitdate->assigntime;
                                    // echo "<pre>------000------";
                                    // print_r($permitdate);			
                                    // echo $newuser_meta->permitdate;
                                    // echo "</pre>";
                                }
                                if (isset($_POST['permit_assign_date']) && !empty($_POST['permit_assign_date'])) {
                                    $permit_assign_date = strtotime($_POST['permit_assign_date']);
                                    $permit_assign_date_end = strtotime("+1 day", $permit_assign_date);
                                    if (empty($newuser_meta->permitdate) || $newuser_meta->permitdate < $permit_assign_date || $newuser_meta->permitdate >= $permit_assign_date_end) {
                                        continue;
                                    }
                                }
                                $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newuser->ID . "'");
                                $userDob = $student_age->meta_value;
                                $udob = date('Y-m-d', strtotime($userDob));
                                $dob = new DateTime($udob);
                                $now = new DateTime();
                                $difference = $now->diff($dob);
                                $age = $difference->y;
                                if ($age) { ?>
                                    <tr <?php //echo $countdata."ss"; 
                                        ?>>
                                        <td>
                                            <div class="title-parent d-flex">
                                                <input name="select_all[]" value="<?php echo $newuser->ID; ?>" <?php echo isset($_SESSION['usersid'][$newuser->ID]) ? 'checked' : ''; ?> class="assignpermit" type="checkbox" />
                                                <?php

                                                echo "<h6>" . $newuser_meta->first_name . " " . $newuser_meta->last_name . "</h6>";
                                                if (!empty($newuser_meta->permit_number)) {
                                                    $mynumber = $newuser_meta->permit_number;
                                                    $get_pernumber = substr($mynumber, 0, 2);
                                                    if ($get_pernumber == '13' && $age >= 21 ) {
                                                ?>
                                                        <span class="label badge up">Upgrade</span>
                                                        <?php
                                                    } else {
                                                        if (!empty($newuser_meta->permit_issued_option_yes)) {
                                                        ?>
                                                            <!-- <a class="btn-info btn-green">Assign</i></a> -->
                                                        <?php
                                                        } else if (!empty($newuser_meta->permit_issued_option_no)) {
                                                        ?>
                                                            <span class="label badge rep">Replace</span>
                                                        <?php
                                                        } else { ?>
                                                            <span class="label badge rep">Replace</span>
                                                    <?php }
                                                    }
                                                } else { ?>
                                                    <span class="label badge new">New</span>
                                                <?php } ?>
                                                <div class="permitstatus"></div>
                                            </div>
                                        </td>

                                        <td><?php if (!empty($newuser_meta->permit_number)) {echo $newuser_meta->permit_number;} ?></td>
                                        <td><?php if (!empty($newuser_meta->permitdate)) {echo date("d/m/Y h:i A", $newuser_meta->permitdate);} else {echo "";}  ?></td>
                                        <td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";} ?></td>

                                        <td><?php if (!empty($newuser_meta->user_gender)) {
                                                $gdr = trim($newuser_meta->user_gender, " ");
                                                $gen = str_split($gdr);
                                                echo $gen[0];
                                            } ?></td>
                                        <?php if (!empty($newuser_meta->date_of_birth)) {
                                            $dateValue = strtotime($newuser_meta->date_of_birth);
                                            echo "<td>" . date("d/m/Y h:i A", $dateValue) . "</td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        ?>
                                        <td><?php
                                            if (gettype($newuser_meta->height_ft_opt) == "integer") {
                                                $ht_ft = round($newuser_meta->height_ft_opt);
                                            } else {
                                                $ht_ft = $newuser_meta->height_ft_opt;
                                            }
                                            if (gettype($newuser_meta->height_in_opt) == "integer") {
                                                $ht_in = round($newuser_meta->height_in_opt);
                                            } else {
                                                $ht_in = $newuser_meta->height_in_opt;
                                            }
                                            echo $ht_ft . "'" . $ht_in . '"'; ?></td>

                                        <td><?php if (gettype($newuser_meta->weight_lbs) == "integer") {
                                                echo round($newuser_meta->weight_lbs);
                                            } else {
                                                echo $newuser_meta->weight_lbs;
                                            } ?></td>
                                        <td><?php
                                            $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='" . $eRecords->course_id . "' ORDER BY activity_id DESC LIMIT  0,1";
                                            $corsCmplt = $wpdb->get_row($Qccmplt);
                                            if (!empty($corsCmplt)) {
                                                $quizcomplete = date('Y-m-d', $corsCmplt->activity_completed);
                                                $date = strtotime($quizcomplete . '+61 month');
                                                echo date('m/d/Y', $date);
                                            } ?></td>

                                        <td><?php if (!empty($newuser_meta->billing_address_1)) {
                                                echo strtoupper($newuser_meta->billing_address_1);
                                            }
                                            if (!empty($newuser_meta->billing_address_2)) {
                                                echo strtoupper($newuser_meta->billing_address_1 . ',' . $newuser_meta->billing_address_2);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_city)) {
                                                echo strtoupper($newuser_meta->billing_city);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_state)) {
                                                echo strtoupper($newuser_meta->billing_state);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_postcode)) {
                                                echo $newuser_meta->billing_postcode;
                                            } ?></td>
                                        <td><?php echo strtoupper($newuser->ID); ?></td>
                                        <td><?php $userDob1 = $newuser_meta->date_of_birth;
                                            $udob = date('Y-m-d', strtotime($userDob1));
                                            $dob1 = new DateTime($udob);
                                            $now1 = new DateTime();
                                            $difference1 = $now1->diff($dob1);
                                            $age1 = $difference1->y;
                                            if ($age1 < 17) {
                                                echo "Under age";
                                            } else if ($age1 > 21) {
                                                echo "Class 12";
                                            } else if ($age1 > 17 or $age1 < 21) {
                                                echo "Class 13";
                                            }
                                            ?></td>

                                    </tr>
                                <?php


                                }
                            }
                        }
                    }
                }else if(isset($_POST['submit'])){
                    $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND activity_status=1 GROUP BY user_id ORDER BY activity_id DESC";
                    $newRecordss = $wpdb->get_results($rec_query);
                    foreach ($newRecordss as $eRecords) {
                        $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
                        $getTitle = $wpdb->get_row($QurcourseTitle);
                        if ($getTitle) {
                            $eRecords->user_id;
                            $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
                            $newusers = $wpdb->get_results($newsql);
                            foreach ($newusers as $newuser) {		
                                $newuser_meta = get_userdata($newuser->ID);
                                $user_DOB = $newuser_meta->date_of_birth;
                                $udob = date('Y-m-d',strtotime($user_DOB));
                                $dob = new DateTime($udob);
                                $now = new DateTime();
                                $difference = $now->diff($dob);
                                $age = $difference->y;
                                // echo "<pre>------00000------";			
                                // echo $newuser_meta->first_name."####";			
                                // print_r ($newuser_meta);
                                // echo "</pre>";

                                //--------------- New -------------
                                if (empty($newuser_meta->permit_number) && $_POST['submit'] == 'new' ) {
                                    $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_meta->permit_number . '');
                                    if (!empty($permitdate)) {
                                        $newuser_meta->permitdate = $permitdate->assigntime;
                                    }
                                    $message = '<span class="label badge new">New</span>';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="title-parent d-flex">
                                                <input name="select_all[]" value="<?php echo $newuser->ID; ?>" <?php echo isset($_SESSION['usersid'][$newuser->ID]) ? 'checked' : ''; ?> class="assignpermit" type="checkbox" />
                                                <?php
                                                echo $newuser_meta->first_name . " " . $newuser_meta->last_name;
                                                echo $message; ?>                                               
                                            </div>
                                        </td>
                                        
                                        <td><?php if (!empty($newuser_meta->permit_number)) { echo $newuser_meta->permit_number;} ?></td>
                                        <td><?php if (!empty($newuser_meta->permitdate)) {echo date("d/m/Y h:i A", $newuser_meta->permitdate);} else { echo "";}  ?></td>
                                        <td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";} ?></td>

                                        <td><?php if (!empty($newuser_meta->user_gender)) {
                                                $gdr = trim($newuser_meta->user_gender, " ");
                                                $gen = str_split($gdr);
                                                echo $gen[0];
                                            } ?></td>
                                        <?php if (!empty($newuser_meta->date_of_birth)) {$dateValue = strtotime($newuser_meta->date_of_birth);echo "<td>" . date("d/m/Y h:i A", $dateValue) . "</td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        ?>
                                        <td><?php
                                            if (gettype($newuser_meta->height_ft_opt) == "integer") {
                                                $ht_ft = round($newuser_meta->height_ft_opt);
                                            } else {
                                                $ht_ft = $newuser_meta->height_ft_opt;
                                            }
                                            if (gettype($newuser_meta->height_in_opt) == "integer") {
                                                $ht_in = round($newuser_meta->height_in_opt);
                                            } else {
                                                $ht_in = $newuser_meta->height_in_opt;
                                            }
                                            echo $ht_ft . "'" . $ht_in . '"'; ?></td>

                                        <td><?php if (gettype($newuser_meta->weight_lbs) == "integer") {
                                                echo round($newuser_meta->weight_lbs);
                                            } else {
                                                echo $newuser_meta->weight_lbs;
                                            } ?></td>
                                        <td><?php
                                            $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='" . $eRecords->course_id . "' ORDER BY activity_id DESC LIMIT  0,1";
                                            $corsCmplt = $wpdb->get_row($Qccmplt);
                                            if (!empty($corsCmplt)) {
                                                $quizcomplete = date('Y-m-d', $corsCmplt->activity_completed);
                                                $date = strtotime($quizcomplete . '+61 month');
                                                echo date('m/d/Y', $date);
                                            } ?></td>

                                        <td><?php if (!empty($newuser_meta->billing_address_1)) {
                                                echo strtoupper($newuser_meta->billing_address_1);
                                            }
                                            if (!empty($newuser_meta->billing_address_2)) {
                                                echo strtoupper($newuser_meta->billing_address_1 . ',' . $newuser_meta->billing_address_2);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_city)) {
                                                echo strtoupper($newuser_meta->billing_city);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_state)) {
                                                echo strtoupper($newuser_meta->billing_state);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_postcode)) {
                                                echo $newuser_meta->billing_postcode;
                                            } ?></td>
                                        <td><?php echo strtoupper($newuser->ID); ?></td>
                                        <td><?php $userDob1 = $newuser_meta->date_of_birth;
                                            $udob = date('Y-m-d', strtotime($userDob1));
                                            $dob1 = new DateTime($udob);
                                            $now1 = new DateTime();
                                            $difference1 = $now1->diff($dob1);
                                            $age1 = $difference1->y;
                                            if ($age1 < 17) {
                                                echo "Under age";
                                            } else if ($age1 > 21) {
                                                echo "Class 12";
                                            } else if ($age1 > 17 or $age1 < 21) {
                                                echo "Class 13";
                                            }
                                            ?></td>

                                    </tr>
                        <?php   }

                        // ---------------replace-------------------

                        if ((!empty($newuser_meta->permit_issued_option_no)) && (!empty($newuser_meta->permit_number)) && $_POST['submit'] == 'replace' ) {

                            $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_meta->permit_number . '');
                            if (!empty($permitdate)) {
                                $newuser_meta->permitdate = $permitdate->assigntime;
                            }
                            $message = '<span class="label badge rep">Replace</span>';
                            ?>
                            <tr>
                                <td>
                                    <div class="title-parent d-flex">
                                        <input name="select_all[]" value="<?php echo $newuser->ID; ?>" <?php echo isset($_SESSION['usersid'][$newuser->ID]) ? 'checked' : ''; ?> class="assignpermit" type="checkbox" />
                                        <?php
                                        echo $newuser_meta->first_name . " " . $newuser_meta->last_name;
                                        echo $message; ?>                                               
                                    </div>
                                </td>
                                
                                <td><?php if (!empty($newuser_meta->permit_number)) { echo $newuser_meta->permit_number;} ?></td>
                                <td><?php if (!empty($newuser_meta->permitdate)) {echo date("d/m/Y h:i A", $newuser_meta->permitdate);} else { echo "";}  ?></td>
                                <td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";} ?></td>
                        
                                <td><?php if (!empty($newuser_meta->user_gender)) {
                                        $gdr = trim($newuser_meta->user_gender, " ");
                                        $gen = str_split($gdr);
                                        echo $gen[0];
                                    } ?></td>
                                <?php if (!empty($newuser_meta->date_of_birth)) {$dateValue = strtotime($newuser_meta->date_of_birth);echo "<td>" . date("d/m/Y h:i A", $dateValue) . "</td>";
                                } else {
                                    echo "<td></td>";
                                }
                                ?>
                                <td><?php
                                    if (gettype($newuser_meta->height_ft_opt) == "integer") {
                                        $ht_ft = round($newuser_meta->height_ft_opt);
                                    } else {
                                        $ht_ft = $newuser_meta->height_ft_opt;
                                    }
                                    if (gettype($newuser_meta->height_in_opt) == "integer") {
                                        $ht_in = round($newuser_meta->height_in_opt);
                                    } else {
                                        $ht_in = $newuser_meta->height_in_opt;
                                    }
                                    echo $ht_ft . "'" . $ht_in . '"'; ?></td>
                        
                                <td><?php if (gettype($newuser_meta->weight_lbs) == "integer") {
                                        echo round($newuser_meta->weight_lbs);
                                    } else {
                                        echo $newuser_meta->weight_lbs;
                                    } ?></td>
                                <td><?php
                                    $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='" . $eRecords->course_id . "' ORDER BY activity_id DESC LIMIT  0,1";
                                    $corsCmplt = $wpdb->get_row($Qccmplt);
                                    if (!empty($corsCmplt)) {
                                        $quizcomplete = date('Y-m-d', $corsCmplt->activity_completed);
                                        $date = strtotime($quizcomplete . '+61 month');
                                        echo date('m/d/Y', $date);
                                    } ?></td>
                        
                                <td><?php if (!empty($newuser_meta->billing_address_1)) {
                                        echo strtoupper($newuser_meta->billing_address_1);
                                    }
                                    if (!empty($newuser_meta->billing_address_2)) {
                                        echo strtoupper($newuser_meta->billing_address_1 . ',' . $newuser_meta->billing_address_2);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_city)) {
                                        echo strtoupper($newuser_meta->billing_city);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_state)) {
                                        echo strtoupper($newuser_meta->billing_state);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_postcode)) {
                                        echo $newuser_meta->billing_postcode;
                                    } ?></td>
                                <td><?php echo strtoupper($newuser->ID); ?></td>
                                <td><?php $userDob1 = $newuser_meta->date_of_birth;
                                    $udob = date('Y-m-d', strtotime($userDob1));
                                    $dob1 = new DateTime($udob);
                                    $now1 = new DateTime();
                                    $difference1 = $now1->diff($dob1);
                                    $age1 = $difference1->y;
                                    if ($age1 < 17) {
                                        echo "Under age";
                                    } else if ($age1 > 21) {
                                        echo "Class 12";
                                    } else if ($age1 > 17 or $age1 < 21) {
                                        echo "Class 13";
                                    }
                                    ?></td>
                        
                            </tr>
                        <?php   }

                        // ----------------upgrade------------------

                        $mynumber = $newuser_meta->permit_number;
                        $get_pernumber = substr($mynumber, 0, 2);
                        if ($age >= 21 && $get_pernumber=='13' && $_POST['submit'] == 'upgrade') {

                            $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_meta->permit_number . '');
                            if (!empty($permitdate)) {
                                $newuser_meta->permitdate = $permitdate->assigntime;
                            }
                            $message = '<span class="label badge up">Upgrade</span>';
                            ?>
                            <tr>
                                <td>
                                    <div class="title-parent d-flex">
                                        <input name="select_all[]" value="<?php echo $newuser->ID; ?>" <?php echo isset($_SESSION['usersid'][$newuser->ID]) ? 'checked' : ''; ?> class="assignpermit" type="checkbox" />
                                        <?php
                                        echo $newuser_meta->first_name . " " . $newuser_meta->last_name;
                                        echo $message; ?>                                               
                                    </div>
                                </td>
                                
                                <td><?php if (!empty($newuser_meta->permit_number)) { echo $newuser_meta->permit_number;} ?></td>
                                <td><?php if (!empty($newuser_meta->permitdate)) {echo date("d/m/Y h:i A", $newuser_meta->permitdate);} else { echo "";}  ?></td>
                                <td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";} ?></td>

                                <td><?php if (!empty($newuser_meta->user_gender)) {
                                        $gdr = trim($newuser_meta->user_gender, " ");
                                        $gen = str_split($gdr);
                                        echo $gen[0];
                                    } ?></td>
                                <?php if (!empty($newuser_meta->date_of_birth)) {$dateValue = strtotime($newuser_meta->date_of_birth);echo "<td>" . date("d/m/Y h:i A", $dateValue) . "</td>";
                                } else {
                                    echo "<td></td>";
                                }
                                ?>
                                <td><?php
                                    if (gettype($newuser_meta->height_ft_opt) == "integer") {
                                        $ht_ft = round($newuser_meta->height_ft_opt);
                                    } else {
                                        $ht_ft = $newuser_meta->height_ft_opt;
                                    }
                                    if (gettype($newuser_meta->height_in_opt) == "integer") {
                                        $ht_in = round($newuser_meta->height_in_opt);
                                    } else {
                                        $ht_in = $newuser_meta->height_in_opt;
                                    }
                                    echo $ht_ft . "'" . $ht_in . '"'; ?></td>

                                <td><?php if (gettype($newuser_meta->weight_lbs) == "integer") {
                                        echo round($newuser_meta->weight_lbs);
                                    } else {
                                        echo $newuser_meta->weight_lbs;
                                    } ?></td>
                                <td><?php
                                    $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='" . $eRecords->course_id . "' ORDER BY activity_id DESC LIMIT  0,1";
                                    $corsCmplt = $wpdb->get_row($Qccmplt);
                                    if (!empty($corsCmplt)) {
                                        $quizcomplete = date('Y-m-d', $corsCmplt->activity_completed);
                                        $date = strtotime($quizcomplete . '+61 month');
                                        echo date('m/d/Y', $date);
                                    } ?></td>

                                <td><?php if (!empty($newuser_meta->billing_address_1)) {
                                        echo strtoupper($newuser_meta->billing_address_1);
                                    }
                                    if (!empty($newuser_meta->billing_address_2)) {
                                        echo strtoupper($newuser_meta->billing_address_1 . ',' . $newuser_meta->billing_address_2);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_city)) {
                                        echo strtoupper($newuser_meta->billing_city);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_state)) {
                                        echo strtoupper($newuser_meta->billing_state);
                                    } ?></td>
                                <td><?php if (!empty($newuser_meta->billing_postcode)) {
                                        echo $newuser_meta->billing_postcode;
                                    } ?></td>
                                <td><?php echo strtoupper($newuser->ID); ?></td>
                                <td><?php $userDob1 = $newuser_meta->date_of_birth;
                                    $udob = date('Y-m-d', strtotime($userDob1));
                                    $dob1 = new DateTime($udob);
                                    $now1 = new DateTime();
                                    $difference1 = $now1->diff($dob1);
                                    $age1 = $difference1->y;
                                    if ($age1 < 17) {
                                        echo "Under age";
                                    } else if ($age1 > 21) {
                                        echo "Class 12";
                                    } else if ($age1 > 17 or $age1 < 21) {
                                        echo "Class 13";
                                    }
                                    ?></td>

                            </tr>
                        <?php   }

                                
                            }
                        }
                    }

                }else {
                    $rec_query = "SELECT * FROM wp_learndash_user_activity WHERE activity_type='quiz' AND activity_status=1 GROUP BY user_id ORDER BY activity_id DESC";
                    $newRecordss = $wpdb->get_results($rec_query);
                    foreach ($newRecordss as $eRecords) {
                        $QurcourseTitle = "SELECT * FROM wp_posts WHERE ID=$eRecords->course_id";
                        $getTitle = $wpdb->get_row($QurcourseTitle);
                        if ($getTitle) {
                            $eRecords->user_id;
                            $newsql = "SELECT * FROM wp_users WHERE ID=$eRecords->user_id AND ID>1";
                            $newusers = $wpdb->get_results($newsql);
                            foreach ($newusers as $newuser) {
                                // die;			

                                $newuser_meta = get_userdata($newuser->ID);
                                $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_meta->permit_number . '');
                                if (!empty($permitdate)) {
                                    $newuser_meta->permitdate = $permitdate->assigntime;
                                }
                                $student_age = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta where meta_key= 'date_of_birth' and user_id='" . $newuser->ID . "'");
                                $userDob = $student_age->meta_value;
                                $udob = date('Y-m-d', strtotime($userDob));
                                $dob = new DateTime($udob);
                                $now = new DateTime();
                                $difference = $now->diff($dob);
                                $age = $difference->y;
                                if ($age) { ?>
                                    <tr>
                                        <td>
                                            <div class="title-parent d-flex">
                                                <input name="select_all[]" value="<?php echo $newuser->ID; ?>" <?php echo isset($_SESSION['usersid'][$newuser->ID]) ? 'checked' : ''; ?> class="assignpermit" type="checkbox" />
                                                <?php
                                                echo $newuser_meta->first_name . " " . $newuser_meta->last_name;
                                              
                                                if (!empty($newuser_meta->permit_number)) {
                                                    $mynumber = $newuser_meta->permit_number;
                                                    $get_pernumber = substr($mynumber, 0, 2);
                                                    if ($get_pernumber == '13' && $age >= 21 ) {
                                                ?>    <span class="label badge up">Upgrade</span>
                                                        <?php
                                                    } else {
                                                        if (!empty($newuser_meta->permit_issued_option_yes)) {
                                                        ?>
                                                            <!-- <a class="btn-info btn-green">Assign</i></a> -->
                                                        <?php
                                                        } else if (!empty($newuser_meta->permit_issued_option_no)) {
                                                        ?>
                                                            <span class="label badge rep">Replace</span>
                                                        <?php
                                                        }else { ?>
                                                            <span class="label badge rep">Replace</span>
                                                    <?php }
                                                    }
                                                } else { ?>
                                                     <span class="label badge new">New</span>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        
                                        <td><?php if (!empty($newuser_meta->permit_number)) { echo $newuser_meta->permit_number;} ?></td>
                                        <td><?php if (!empty($newuser_meta->permitdate)) {echo date("d/m/Y h:i A", $newuser_meta->permitdate);} else { echo "";}  ?></td>
                                        <td><?php if(!empty($newuser_meta->permit_issued_option_yes)){ echo "Yes"; } elseif(!empty($newuser_meta->permit_issued_option_no)) { echo "No";} ?></td>

                                        <td><?php if (!empty($newuser_meta->user_gender)) {
                                                $gdr = trim($newuser_meta->user_gender, " ");
                                                $gen = str_split($gdr);
                                                echo $gen[0];
                                            } ?></td>
                                        <?php if (!empty($newuser_meta->date_of_birth)) {$dateValue = strtotime($newuser_meta->date_of_birth);echo "<td>" . date("d/m/Y h:i A", $dateValue) . "</td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        ?>
                                        <td><?php
                                            if (gettype($newuser_meta->height_ft_opt) == "integer") {
                                                $ht_ft = round($newuser_meta->height_ft_opt);
                                            } else {
                                                $ht_ft = $newuser_meta->height_ft_opt;
                                            }
                                            if (gettype($newuser_meta->height_in_opt) == "integer") {
                                                $ht_in = round($newuser_meta->height_in_opt);
                                            } else {
                                                $ht_in = $newuser_meta->height_in_opt;
                                            }
                                            echo $ht_ft . "'" . $ht_in . '"'; ?></td>

                                        <td><?php if (gettype($newuser_meta->weight_lbs) == "integer") {
                                                echo round($newuser_meta->weight_lbs);
                                            } else {
                                                echo $newuser_meta->weight_lbs;
                                            } ?></td>
                                        <td><?php
                                            $Qccmplt = "SELECT * FROM wp_learndash_user_activity WHERE user_id='" . $newuser->ID . "' AND activity_type='quiz' AND activity_status=1 AND course_id ='" . $eRecords->course_id . "' ORDER BY activity_id DESC LIMIT  0,1";
                                            $corsCmplt = $wpdb->get_row($Qccmplt);
                                            if (!empty($corsCmplt)) {
                                                $quizcomplete = date('Y-m-d', $corsCmplt->activity_completed);
                                                $date = strtotime($quizcomplete . '+61 month');
                                                echo date('m/d/Y', $date);
                                            } ?></td>

                                        <td><?php if (!empty($newuser_meta->billing_address_1)) {
                                                echo strtoupper($newuser_meta->billing_address_1);
                                            }
                                            if (!empty($newuser_meta->billing_address_2)) {
                                                echo strtoupper($newuser_meta->billing_address_1 . ',' . $newuser_meta->billing_address_2);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_city)) {
                                                echo strtoupper($newuser_meta->billing_city);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_state)) {
                                                echo strtoupper($newuser_meta->billing_state);
                                            } ?></td>
                                        <td><?php if (!empty($newuser_meta->billing_postcode)) {
                                                echo $newuser_meta->billing_postcode;
                                            } ?></td>
                                        <td><?php echo strtoupper($newuser->ID); ?></td>
                                        <td><?php $userDob1 = $newuser_meta->date_of_birth;
                                            $udob = date('Y-m-d', strtotime($userDob1));
                                            $dob1 = new DateTime($udob);
                                            $now1 = new DateTime();
                                            $difference1 = $now1->diff($dob1);
                                            $age1 = $difference1->y;
                                            if ($age1 < 17) {
                                                echo "Under age";
                                            } else if ($age1 > 21) {
                                                echo "Class 12";
                                            } else if ($age1 > 17 or $age1 < 21) {
                                                echo "Class 13";
                                            }
                                            ?></td>

                                    </tr>
                        <?php   }
                            }
                        }
                    }
                } ?>
            </table>
        </div>
    </div>

    <input type="hidden" id="totalselected" value="<?php echo sizeof($_SESSION["usersid"]); ?>" />


    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/dataTables.select.min.js"></script>

    <script>
        // var datatable1= null;
        $(document).on("click", '.btnfilter', function() {
            console.log('datatable1-------------------------------------');
        });

        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
            var yourArray = [];
            $('input:checkbox').not(this).prop('checked', this.checked).each(function(){
                yourArray.push($(this).val());
            });
            // console.log('ddddd',yourArray);
            $.ajax({
                type: "POST",
                url: '<?php echo $assignpath; ?>',
                data: { userid : yourArray, checked: $(this).is(":checked") },
                success: function(response) {
                    // console.log('response1111', response);
                    var resp = $.parseJSON(response);
                    console.log('resppppppppp', resp);
                    var toodata = resp.totaladded;
                    $('#totalselected').val(resp.totaladded);
                    $('#totalselecteds').html(toodata);
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
                    url: '<?php echo $assignpath; ?>',
                    data: args,
                    success: function(response) {
                        console.log('response', response);
                        var resp = $.parseJSON(response);
                        console.log('resp', resp);
                        var toodata = resp.totaladded;
                        $('#totalselected').val(resp.totaladded);
                        $('#totalselecteds').html(toodata);
    
                    }
                });
            }

        });
        $(".btnNextpage").click(function() {
            var totaladded = $('#totalselected').val();
            console.log('aaaaaaa:-------',totaladded);
            if (totaladded > 0) {
                $(".stepbtnslist").addClass("active");
                // alert('Now you can access this page');
                var webpath = window.location.hostname;
                window.location.href = '/wp-admin/admin.php?page=assignewpermit';
            } else {
                alert('Please select the users');
            }
        });


        function action_permit_unassign(id) {
            jQuery.ajax({
                type: "POST",
                url: '<?php echo $plugingpath; ?>',
                data: {
                    action: 'action_permit_unassign',
                    userId: id
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    data.forEach(function(ele, index) {
                        if (ele.status == 1) {
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('a').removeClass('btn-red').addClass('btn-blue').text('AssignNew').attr('onclick', `action_permit_assign_new(${ele.userid})`);
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(3).text(ele.permit_number);
                        } else {
                            document.getElementById('prod_cat_id').innerHTML = "records not updated";
                        }
                        // console.log('element',ele);
                    });
                    //console.log('data :',data);
                }
            });
        }

        function action_permit_assign_new(id) {
            //alert(id);
            jQuery.ajax({
                type: "POST",
                url: '<?php echo $plugingpath; ?>',
                data: {
                    action: 'action_permit_assign_new',
                    userId: id
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    data.forEach(function(ele, index) {
                        $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html("");
                        if (ele.status == 1) {
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('a').removeClass('btn-blue').addClass('btn-green').text('Assign');
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(3).text(ele.permit_number);
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(4).text(ele.permit_status);
                        } else if (data == 0) {
                            document.getElementById('prod_cat_id').innerHTML = "permit number not issue";
                        } else {
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html(ele.status);
                        }
                        //console.log(`index: ${index}, element: `,ele);

                    });
                    //console.log('data :',data);
                }
            });
        }

        function action_permit_reassign(id) {
            jQuery.ajax({
                type: "POST",
                url: '<?php echo $plugingpath; ?>',
                data: {
                    action: 'action_permit_reassign',
                    userId: id
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    data.forEach(function(ele, index) {
                        $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html("");
                        if (ele.status == 1) {
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('a').removeClass('btn-yellow').addClass('btn-green').text('Assign');
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(3).text(ele.permit_number);
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(4).text(ele.permit_status);
                        } else if (ele.status == 0) {
                            document.getElementById('prod_cat_id').innerHTML = "permit number not issue";

                        } else {
                            $('#example tbody').find('tr').find(`input[value="${ele.userid}"]`).closest('tr').find('td').eq(2).find(".permitstatus").html(ele.status);
                        }
                        console.log('element', ele);
                    });
                    console.log('data :', data);
                }
            });
        }
        $(document).ready(function() {
            // datatable1 = $('#example').DataTable({
            $('#example').DataTable({
                'paging': true,
                'scrollY': '500px',
                "columnDefs": [{
                    'targets': [0],
                    'orderable': false
                }],
                // add buttom
                dom: 'Blfrtip',
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                select: true
                //end

            });
        });
    </script>


    <script type='text/javascript'>
        jQuery(document).ready(function() {
            jQuery(".clndr-date").click(function() {
                //alert('hiiii');
                var datepickerFrom = document.getElementById("datepickerfrom").value;
                var datepickerTo = document.getElementById("datepickerto").value;
                var permit_assign_date = document.getElementById("permit_assign_date").value;
                // alert(datepickerFrom+'#'+datepickerTo);
            });

            jQuery("#datepickerto").datepicker({
                changeMonth: true,
                changeYear: true
            });

            jQuery("#datepickerfrom").datepicker({
                changeMonth: true,
                changeYear: true
            });

            jQuery("#permit_assign_date").datepicker({
                changeMonth: true,
                changeYear: true
            });

            //console.log("HELLO")
            function exportTableToCSV($table, filename) {
                var $headers = $table.find('tr:has(th)'),
                    $rows = $table.find('tr:has(td)')
                    ,
                    tmpColDelim = String.fromCharCode(11) // vertical tab character
                    ,
                    tmpRowDelim = String.fromCharCode(0) // null character
                    ,
                    colDelim = '","',
                    rowDelim = '"\r\n"';

                // Grab text from table into CSV formatted string
                var csv = '"';
                csv += formatRows($headers.map(grabRow));
                csv += rowDelim;
                csv += formatRows($rows.map(grabRow)) + '"';

                // Data URI
                var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

                $(this)
                    .attr({
                        'download': filename,
                        'href': csvData
                        //,'target' : '_blank' //if you want it to open in a new window
                    });

                //------------------------------------------------------------
                // Helper Functions 
                //------------------------------------------------------------
                // Format the output so it has the appropriate delimiters
                function formatRows(rows) {
                    console.log('rows', rows);
                    return rows.get().join(tmpRowDelim)
                        .split(tmpRowDelim).join(rowDelim)
                        .split(tmpColDelim).join(colDelim);
                }
                // Grab and format a row from the table
                function grabRow(i, row) {

                    var $row = $(row);

                    console.log('rowwwww', $row);
                    //for some reason $cols = $row.find('td') || $row.find('th') won't work...
                    var $cols = $row.find('td');
                    if (!$cols.length) $cols = $row.find('th');

                    return $cols.map(grabCol)
                        .get().join(tmpColDelim);
                }
                // Grab and format a column from the table 
                function grabCol(j, col) {
                    var $col = $(col),
                        $text = $col.text();

                    return $text.replace('"', '""'); // escape double quotes


                }
            }
            // This must be a hyperlink
            $("#export").click(function(event) {
                // var outputFile = 'export'
                //var outputFile = window.prompt("What do you want to name your output file") || 'export';
                var d = new Date();
                var mymonth = d.getMonth() + 1;
                if (mymonth < 10) {
                    mymonth = "0" + mymonth;
                }
                var mydate = d.getDate();
                if (mydate < 10) {
                    mydate = "0" + mydate;
                }
                //1ALERT20200225
                //yyyymmdd
                var outputFile = "permit_process" + d.getFullYear() + mymonth + mydate;
                outputFile = outputFile.replace('.csv', '') + '.csv'
                exportTableToCSV.apply(this, [$('#dvData .dataTable'), outputFile]);
                // IF CSV, don't do event.preventDefault() or return false
                // We actually need this to be a typical hyperlink  
            });
        });
    </script>

<?php  } ?>