
<?php
ob_start();
session_start();
if(!isset($_SESSION['one_planet']['quiz_status']) OR $_SESSION['one_planet']['quiz_status']==false){
    wp_redirect(get_page_link(1203));
    exit();
}

 get_header();//1187 ?>

<script>
    jQuery('#content').find(':first-child').removeClass('tg-container').addClass('tg-container-fluid');
    jQuery('#content').find(':first-child').removeClass('tg-container--flex');
</script>

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="http://122.176.46.118/learnoneplanet/wp-content/themes/zakra/assets/css/student-admission-form.css" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->

<style type="text/css">
    .instruction-form-wrap{
        display: none;
    }
    .instruction-form-wrap.form-active{
        display: block;
    }
    .btn-pre{
        display: none;
    }
    .btn-pre.active{
        display: block;
    }
    .input-error{
        color:red !important;
        border: 1px solid red !important;
    }
</style>
<section>
    <div class="px-2 px-lg-5 px-md-3">
        <div id="student-form">
            <div class="header">
                <h4><i class="fa fa-edit"></i> New Students Admission Form</h4>
            </div>
            <div class="body">
                <div class="row py-5">
                    <div class="col-12 col-lg-3">
                        <div class="instruction-wrap">
                            <div class="instruction-header">
                                <h5>
                                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Instruction
                                </h5>
                            </div>
                            <div class="instruction-body">
                                <div class="heading">
                                    <h6 class="text-center fw-bold">Fill in the space(s) or Select the appropriate box(es) or selections. *fields are required.</h6>
                                </div>
                                <div class="txt">
                                    <p class="text-center fw-bold">You must provide your address information on the fields provided. Once you fill all the required fields hit the countinue button.</p>
                                </div>
                                <hr />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="form-wrapper">
                            <form class="admission-form" id="admission-form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="error-msg">
                                                    
                                        </div>
                                    </div>
                                </div>
                            <!-- First Form -->
                            <div class="instruction-form-wrap form-active" form-step="1">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Admission information
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                   <!-- <form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">
                                                
                                                <label class="mb-2 fw-bold" id="study_level1">Study level..*</label><div id="msg_page" class="spn_star"></div>
                                                <select name="study_level" id="study_level" class="required">
                                                    <option value=""  selected>Graduate</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold">Admission type..*</label>
                                                <select name="admission_type" id="admission_type" class="required">
                                                    <option value="" selected>Admission Type</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold">Program</label>
                                                <select name="program" id="program">
                                                    <option value=""  selected>Program</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>

                                   <!-- </form>-->
                                </div>
                            </div>

                            <!-- Second Form -->

                            <div class="instruction-form-wrap " form-step="2">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Contact Address
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                    <!--<form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="coun_try">Country..*</label>
                                                <input type="text" class="form-control required" placeholder="Country" id="coun_try" name="country"  />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="c_ity">City..*</label>
                                                <input type="text" class="form-control required" placeholder="City" id="c_ity" name="city" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="sub_city">Sub City</label>
                                                <input type="text" class="form-control" placeholder="Sub City" id="sub_city" name="subcity" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold">Region</label>
                                                <select name="region">
                                                    <option value="0" disabled selected>Region</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="z_one">Zone</label>
                                                <input type="text" class="form-control" placeholder="Zone" id="z_one" name="zone" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="wore_da">Woreda</label>
                                                <input type="text" class="form-control" placeholder="Woreda" id="wore_da" name="wore_da" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="special_location">Special location</label>
                                                <input type="text" class="form-control" id="special_location" placeholder="Special Location" aria-describedby="specialLocationHelp" name="special_location" />
                                                <div id="specialLocationHelp" class="form-text pt-4">If you are a foreigner state your address here and etc... </div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="house_number">House number</label>
                                                <input type="text" class="form-control" placeholder="House Number" id="house_number" name="house_number" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="phone_number">Phone number</label>
                                                <input type="text" class="form-control" placeholder="Phone Number" id="phone_number" name="phone_number" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="second_phone_number">Second Phone number</label>
                                                <input type="text" class="form-control" placeholder="Second Phone Number" id="second_phone_number" name="alternate_phone_no" />
                                            </div>

                                        </div>
                                    <!--</form>-->
                                </div>
                            </div>


                            <!-- Third Form -->

                            <div class="instruction-form-wrap" form-step="3">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Emergency Contact Person Information
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                   <!-- <form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="contact_person_name">Contact Person Fullname..*</label>
                                                <input type="text" class="form-control required" id="contact_person_name" placeholder="Contact Person Fullname" name="contact_person_name" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="contact_Person_relationship">Contact Person Relationship..*</label>
                                                <input type="text" class="form-control required" id="contact_Person_relationship" placeholder="Contact Person Relationship" name="contact_Person_relationship" />
                                                <!-- <h5 class="text-danger fw-bold m-0 py-2">This field is required.*</h5> -->
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="contact_Person__number">Contact Person PhoneNumber..*</label>
                                                <input type="text" class="form-control required" id="contact_Person__number" placeholder="Contact Person PhoneNumber" name="contact_person__number" />
                                               <!--  <h5 class="text-danger fw-bold m-0 py-2">This field is required.*</h5> -->
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="contact_person_current_occupation">Contact Person Current Occupation</label>
                                                <input type="text" class="form-control" id="contact_person_current_occupation" placeholder="Contact Person Current Occupation" name="contact_person_current_occupation" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="name_current_employer">Name of Current Employer</label>
                                                <input type="text" class="form-control" id="name_current_employer" placeholder="Name of Current Employer" aria-describedby="nameEmployerHelp" name="name_current_employer" />
                                                <div id="nameEmployerHelp" class="form-text pt-4"> Contact person current employer name</div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="email_employer">Email of Employer</label>
                                                <input type="email" class="form-control" name="email_employer"id="email_employer" placeholder="Email of Employer" aria-describedby="emailEmployerHelp" />
                                                <div id="emailEmployerHelp" class="form-text pt-4"> Contact person current employer email</div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="employer_phone_number">Employer Phone Number</label>
                                                <input type="text" class="form-control" id="employer_phone_number" placeholder="Employer Phone Number" aria-describedby="employerPhoneNumberHelp" name="employer_phone_number" />
                                                <div id="employerPhoneNumberHelp" class="form-text pt-4"> Contact person current employer phone number</div>
                                            </div>

                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="po_box">Pobox</label>
                                                <input type="text" class="form-control" id="po_box" placeholder="Pobox" name="po_box" />
                                            </div>
                                        </div>
                                    <!--</form>-->
                                </div>
                            </div>

                            <!-- Fourth Form -->

                            <div class="instruction-form-wrap" form-step="4">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> High School Information
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                    <!--<form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">                                                                      
                                                <label class="mb-2 fw-bold" for="last_attended_high_school">Last Attended High School..*</label>
                                                <input type="text" class="form-control required" id="last_attended_high_school" placeholder="Last Attended High School" name="last_attended_high_school" />
                                                <!-- <h5 class="text-danger fw-bold m-0 py-2">This field is required.*</h5> -->
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="school_address">School Address..*</label>
                                                <input type="text" class="form-control required" id="school_address" placeholder="School Address" name="school_address" />

                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="grade_matric_result">Grade 10 Matric Result..*</label>
                                                <input type="text" class="form-control required" id="grade_matric_result" placeholder="Grade 10 Matric Result" name="grade_matric_result" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label for="exam-year" class="mb-2 fw-bold ">Grade 10 Exam Taken Year..*</label>
                                                <input type="text" id="exam-year" class="form-control datepicker required" name="exam_year">
                                                <div id="gradeExamTakenYearHelp" class="form-text pt-4">Select year in Gregorian calender</div>
                                            </div>                                                                         
                                        </div>
                                   <!-- </form>-->
                                </div>
                            </div>


                            <!-- Fifth Form -->

                            <div class="instruction-form-wrap" form-step="5">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Scan Document Up loading
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                    <!--<form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="grade_transcript">Grade 9, 10, 11, and 12 transcripts</label>
                                                    <input type="file" name="g9101112transscript[]" class="input-file">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="Grade 9, 10, 11, and 12 transcripts">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="grade_ten_certificare">Grade 10 matric Certificate</label>
                                                    <input type="file" name="g10mc" class="input-file">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="Grade 10 matric Certificate">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="grade_twevel_certificate">Grade 12 matric Certificate</label>
                                                    <input type="file" name="g12mc" class="input-file">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="Grade 12 matric Certificate">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="c_o_c">Certificate of Competency (COC)</label>
                                                    <input type="file" name="coc" class="input-file">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="Certificate of Competency (COC)">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="diploma_certificate">TVET/Diploma Certificate</label>
                                                    <input type="file" name="tvet" class="input-file">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="TVET/Diploma Certificate">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6 my-3">
                                                <div class="upload-file-wrap">
                                                    <label class="mb-2 fw-bold" for="degree_certificate">Undergraduate Degree Certificate..*</label>
                                                    <input type="file" name="ugdc" class="input-file required">
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control green-border" disabled placeholder="Undergraduate Degree Certificate">
                                                        <span class="input-group-btn">
                                                            <button class="upload-field btn btn-success" type="button">Upload File </button>
                                                        </span>
                                                    </div>
                                                   <!--  <h5 class="text-danger fw-bold m-0 py-2">This field is required.*</h5> -->
                                                </div>
                                            </div>
                                        </div>
                                   <!-- </form>-->
                                </div>
                            </div>

                            <!-- Sixth Form -->

                            <div class="instruction-form-wrap final" form-step="6">
                                <div class="instruction-form-header">
                                    <h5>
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Basic Information
                                    </h5>
                                </div>
                                <div class="instruction-form-body pb-5">
                                   <!-- <form class="I">-->
                                        <div class="row">
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="f_name">First Name..*</label>
                                                <input type="text" class="form-control required" id="f_name " placeholder="FIRST NAME" name="fname" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="m_name">Middle Name (Father Name)..*</label>
                                                <input type="text" class="form-control required" id="m_name" placeholder="MIDDLE NAME" name="mname" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="l_name">Last Name (Grandfather Name)..*</label>
                                                <input type="text" class="form-control required" id="l_name" placeholder="LAST NAME" name="lname" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="gander">Gander..*</label>
                                                <select class="form-select form-select-lg mb-3 required" name="gender">
                                                    <option selected value="">Gander</option>
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                    <option value="3">Others</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label for="date_of_birth" class="mb-2 fw-bold ">Date of Birth..*</label>
                                                <input type="text" id="date_of_birth" class="form-control datepicker required" name="dob">
                                                <div class="form-text">When you pick your birth date select GC</div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="nationality">Nationality..*</label>
                                                <select class="form-select form-select-lg mb-3 required" name="nationality">
                                                    <option selected value="">Nationality</option>
                                                    <option value="1">India</option>
                                                    <option value="2">America</option>
                                                    <option value="3">Europe</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="place_birth">Place of Birth..*</label>
                                                <input type="text" class="form-control required" id="place_birth" placeholder="Place of Birth" name="place_birth" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="m_status">Metrial Status..*</label>
                                                <select class="form-select form-select-lg mb-3 required" name="m_status">
                                                    <option selected value="">Metrial Status</option>
                                                    <option value="1">Single</option>
                                                    <option value="2">Merried</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold required" for="e_mail">Email..*</label>
                                                <input type="email" class="form-control required" id="e_mail" placeholder="Email" name="e_mail" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="pass_word">Password..*</label>
                                                <input type="password" class="form-control required" id="pass_word" placeholder="Password" name="pass_word" />
                                                <div class="form-text">must be between 5 to 20 characters which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character</div>
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="c_password">Comfirm Password..*</label>
                                                <input type="password" class="form-control required" id="c_password" placeholder="Comfirm Password" name="c_password" />
                                            </div>
                                            <div class="col-12 col-lg-6 my-3">
                                                <label class="mb-2 fw-bold" for="upload_photo">Upload Photo..*</label>
                                                <div class="form-control py-2">
                                                    <input type="file" id="upload_photo" class="required" name="profile_image" />
                                                </div>
                                            </div>
                                        </div>
                                   <!-- </form>-->
                                </div>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="student-form-footer">
                    <div class="row">
                        <div class="col-lg-8 col-md-7 col-12">
                            <div class="page-numbers p-5 p-md-3 pt-0 ">
                                <a href="javascript:void(0);" class="btn-page btn-selected " >1</a>
                                <a href="javascript:void(0);" class="btn-page ">2</a>
                                <a href="javascript:void(0);" class="btn-page ">3</a>
                                <a href="javascript:void(0);" class="btn-page ">4</a>
                                <a href="javascript:void(0);" class="btn-page ">5</a>
                                <a href="javascript:void(0);" class="btn-page ">6</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5 col-12">
                            <div class="d-flex align-items-center justify-content-center justify-content-lg-end flex-wrap h-100 py-3 p-lg-0 px-lg-3 p-md-0">
                                <a href="javascript:void(0);" class="custom_btn btn_submit_form btn-pre">Privous</a>
                                <a href="javascript:void(0);" class="custom_btn active_btn btn_submit_form btn-next">Next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function next_step(step){
        return (step+1);
    }
    function pre_step(step){
        return (step-1);
    }
    var form_data={};
    $(".btn_submit_form").click(function(e){
        e.preventDefault();
        var that=$(this);
        var step=parseInt($('.form-active').attr('form-step'));
        var current_form=$('.form-active');
        console.log('current_form',current_form);
        var flag=true;
        form_data.step=step;
        $(current_form).find('[name]').each(function(){
            form_data[$(this).attr('name')]=$(this).val();
           if (!$(that).hasClass('btn-pre')){
                if($(this).hasClass('required') && $(this).val() == "" ){
                    flag=false;
                    if($(this).closest('.upload-file-wrap')){
                        $(this).closest('.upload-file-wrap').find('input[type="text"]').addClass('input-error');
                    }
                     $(this).addClass('input-error');  
                }else{
                    if($(this).closest('.upload-file-wrap')){
                        $(this).closest('.upload-file-wrap').find('input[type="text"]').removeClass('input-error');
                    }
                    $(this).removeClass('input-error'); 
                } 
            }
           
        });

        if(flag==false){
            return false;
        }      
        console.log('current-form',form_data);
        if($(that).hasClass('btn-next')){
            if(!$(that).hasClass('final-submit')){

                next_pre_element=next_step(step);
            }
        }else if($(that).hasClass('btn-pre')){
            next_pre_element=pre_step(step);
        }else{
            $(that).closest('.page-numbers').find('.btn-selected').removeClass('btn-selected');
            $(that).addClass('btn-selected');
            next_pre_element=parseInt($(that).text());
            if($(that).hasClass('final')){
                $('.btn-next').text('Submit');
            }else{
                $('.btn-next').text('Next');
            }
        }
        if(!$(that).hasClass('final-submit')){
            var next_form=$('#student-form').find(`[form-step="${next_pre_element}"]`);
            $(current_form).removeClass('form-active');
            $(current_form).hide();
            $(next_form).addClass('form-active');
            $(next_form).show();
            console.log('next_pre_element',next_pre_element);
            if(next_pre_element>1){
                $('.btn-pre').removeClass('active');
                $('.btn-pre').addClass('active');
                if($(next_form).hasClass('final')){
                    $('.btn-next').text('Submit');
                    $('.btn-next').addClass('final-submit')
                }else{
                    $('.btn-next').removeClass('final-submit')
                    $('.btn-next').text('Next');
                }
                $('.btn-page').removeClass('btn-selected');
                $(`.btn-page:contains(${next_pre_element})`).addClass('btn-selected');
            }else{
                 $('.btn-pre').removeClass('active');
                 $('.btn-page').removeClass('btn-selected');
                 $(`.btn-page:contains(${next_pre_element})`).addClass('btn-selected');
            }
        }else{
            var form = $('#admission-form')[0]; // You need to use standard javascript object here
            var formData = new FormData(form);
            $.ajax({
                url:"<?php echo plugins_url('sync-course/student-admission-ajax.php'); ?>",
                type: 'POST',
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false,
                data:formData,
                beforeSend:function(){
                    $(that).prop('disabled',true);
                    $(that).text('Please Wait...');
                },
                success:function(response){
                    var data=JSON.parse(response);
                    if(data.status==false){
                        var error='';
                        data.data?.forEach(function(ele,index){//error-msg
                            error +=`  <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
     ${ele.msg}
  </div>`
                    $('#admission-form').find(`[name="${ele.key}"]`).addClass('input-error');
                
                        });
                        $('.error-msg').html(error);
                    }else{
                       window.location.href="<?php echo get_page_link(1239); ?>";
                    }
                    console.log('response',response);
                },
                complete:function(){
                    $(that).prop('disabled',false);
                    $(that).text('Submit');
                }
            });
        }
    });

    //remove input  error class
    $('#admission-form').on('focus','[name]',function(){
        console.log('input error');
        $(this).removeClass('input-error');
    });



    $(function() {
        $('.datepicker').datepicker();
    });

    $(document).on('click', '.upload-field', function() {
        var file = $(this).parent().parent().parent().find('.input-file');
        file.trigger('click');
    });
    $(document).on('change', '.input-file', function() {
        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        $(this).parent().find('.form-control').removeClass('input-error');
    });
</script>


<?php get_footer(); ?>