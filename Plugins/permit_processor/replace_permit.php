<?php
ob_start();
session_start();
if (!isset($_SESSION['usersid'])) {
    $_SESSION['usersid'] = array();
}
if (!isset($_SESSION['repusersid'])) {
    $_SESSION['repusersid'] = array();
}
// echo "<pre>-----replace------";
// print_r($_SESSION['usersid']);
// print_r($_SESSION['repusersid']);
// echo "</pre>";


function replace_permit()
{
    $repuserajax = plugins_url() . "/permit_processor/repajax.php";
    $plugingpath = plugins_url() . "/permit_processor/permit_processor_ajax.php";
?>



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            overflow-x: hidden;
        }


        .wrapper-conete {
            padding-left: 60px;
            padding-right: 60px;
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
            cursor: pointer;
        }

        

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
            color: #f53b3b;
            font-size: 16px;
        }

        .right-col {
            display: flex;
            justify-content: end;
            margin-right: 20px;
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

        button.bt-1,
        button.bt-2 {
            margin: 10px;
            width: auto;
            border: none;
            border-radius: 10px;
            padding: 9px;
            background-color: #dea333;
            color: #fff;
        }

        button.bt-3 {
            border: none;
            background-color: green;
            width: auto;
            margin: 10px;
            border-radius: 10px;
            padding: 9px;
            color: #fff;
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
            white-space: nowrap;
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
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #dea333;
            background-color: #dea333;
        }

        .title-parent+small {
            margin-left: 30px;
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

        /* span badge styling */



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
                <li class="stepbtnrep current ">Step4 :Replace Permit</li>
                <li class="stepbtnexp">Step5: Export</li>
            </ul>
        </div>



        <div class="header-title-btn w-100">

            <div class="left-col col-sm-6">
                <span class="mb-2">Replace Permit</span>
            </div>

            <div class="right-col col-sm-6">
                <button class="bt-2" id="btnExport">Moving to Printing Export</button>
                <button class="bt-3 btnNextpag " onclick="action_permit_unassign()">Continue to Next Step</button>
            </div>

        </div>

        <hr style="width: 99%;">

        <div class="dex table-responsive">
            <section id="table-section" class="license w-100">

                <h5>License Type:</h5>
                <div class="badge-parent d-flex mb-5">
                    <span class="badge btnclass active">Class 12</span>
                    <span class="badge btnclass ">Class 13</span>
                </div>


                <table id="headerTable" class="table table-striped table-hover class_12">
                    <thead>
                        <th style="width: 20%;">
                            <div class="title-parent d-flex"><input type="checkbox" style="opacity: 0 !important;" id="checkAll" />Name</div>
                        </th>
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
                        if ((!empty($newuser_metaa['permit_issued_option_no'][0])) && (!empty($newuser_metaa['permit_number'][0]))) {
                            // $_SESSION['repusersid'][$useid] = 1 ;
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
                            if ($age >= 21) {
                                echo '<tr class="trdata class12">
                            <td>
                                <div class="title-parent d-flex">
                                    <input type="checkbox" value="' . $useid . '" ' . (isset($_SESSION['repusersid'][$useid]) ? 'checked' : '') . ' id="">
                                    <h6>' . $newuser_metaa["first_name"][0] . ' ' . $newuser_metaa["last_name"][0] . '</h6>
                                    <span class="badge rep">Replace</span>
                                </div>
                                <small>' . $newuser_metaa["billing_email"][0] . '</small>
                            </td>
                            <td>' . $newuser_metaa['permit_number'][0] . '</td>
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
                                    <input type="checkbox" value="' . $useid . '" ' . (isset($_SESSION['repusersid'][$useid]) ? 'checked' : '') . ' id="">
                                    <h6>' . $newuser_metaa["first_name"][0] . ' ' . $newuser_metaa["last_name"][0] . '</h6>
                                    <span class="badge rep">Replace</span>
                                </div>
                                <small>' . $newuser_metaa["billing_email"][0] . '</small>
                            </td>
                            <td>' . $newuser_metaa['permit_number'][0] . '</td>
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
                            }
                        }
                    }


                    ?>

                </table>

            </section>
        </div>
    </div>

    <input type="hidden" id="totalselected" value=" <?php echo sizeof($_SESSION['repusersid']); ?> " />
    <input type="hidden" id="totaladdedids" value=" <?php if($_SESSION['repusersid']){ echo implode(",", array_keys($_SESSION['repusersid'])); }  ?> " />

    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript">

        $(document).ready(function() {
        // $(function () {
            
            $('input:checkbox').prop('checked', true);
            var yourArray = [];
            $('input:checkbox').prop('checked', true).each(function(){
                yourArray.push($(this).val());
            });
            console.log('ddddd',yourArray);
            $.ajax({
                type: "POST",
                url: '<?php echo $repuserajax; ?>',
                data: { userid : yourArray, checked: $('#checkAll').is(":checked") },
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
                    url: '<?php echo $repuserajax; ?>',
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
            $(".btnclass").click(function() {
                $(this).closest('span').addClass("active");
                $(this).siblings().removeClass("active");
                $("#headerTable").toggleClass("class_12 class_13")
                // $(this).addClass('active');
            });
        });


        function action_permit_unassign() {
            if($('#totaladdedids').val() != ''){
                // console.log('if',$('#totaladdedids').val());
                jQuery.ajax({
                    type: "POST",
                    url: '<?php echo $plugingpath; ?>',
                    data: {
                        action: 'action_permit_unassign',
                        userId: $('#totaladdedids').val().split(",")
                    },
                    success: function(response) {
                        var dataa = JSON.parse(response);
                        console.log('data-------- :', dataa[0].userid);
                        if (dataa) {
                            // alert('We are working for the export page ....');
                            $(".stepbtnrep").addClass("active");
                            window.location.href = '/wp-admin/admin.php?page=exportpermit';
                        }
                    }
                });
            
            }else{
            // console.log('else',$('#totaladdedids').val());
            $(".stepbtnrep").addClass("active");
            window.location.href = '/wp-admin/admin.php?page=exportpermit';
            }
        }

        class csvExport {
            constructor(table, header = true) {
                this.table = table;
                this.rows = Array.from(table.querySelectorAll("tr"));
                if (!header && this.rows[0].querySelectorAll("th").length) {
                this.rows.shift();
                }
                // console.log(this.rows);
                // console.log(this._longestRow());
            }

            exportCsv() {
                const lines = [];
                const ncols = this._longestRow();
                for (const row of this.rows) {
                let line = "";
                for (let i = 0; i < ncols; i++) {
                    if (row.children[i] !== undefined) {
                    line += csvExport.safeData(row.children[i]);
                    }
                    line += i !== ncols - 1 ? "," : "";
                }
                lines.push(line);
                }
                //console.log(lines);
                return lines.join("\n");
            }
            _longestRow() {
                return this.rows.reduce((length, row) => (row.childElementCount > length ? row.childElementCount : length), 0);
            }
            static safeData(td) {
                let data = td.textContent.trim().replace('Replace', '<br>').replace(/\s+/g, " ").replace(' <br> ', '\n');
                //Replace all double quote to two double quotes
                data = data.replace(/"/g, `""`);
                //Replace , and \n to double quotes
                data = /[",\n"]/.test(data) ? `"${data}"` : data;
                return data;
            }
        }


        const btnExport = document.querySelector("#btnExport");
        const tableElement = document.querySelector("#headerTable");


        btnExport.addEventListener("click", () => {
            var d = new Date();
            var mymonth= d.getMonth()+1;
            if(mymonth < 10){
            mymonth = "0"+mymonth;
            }
            var mydate = d.getDate();
            if(mydate <10){
            mydate = "0" + mydate;
            }
            var outputFile= "permit_process"+d.getFullYear()+mymonth+mydate;

            const obj = new csvExport(tableElement);
            const csvData = obj.exportCsv();
            const blob = new Blob([csvData], { type: "text/csv" });
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = outputFile+".csv";
            a.click();

            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 500);
        });

    </script>

<?php
};
