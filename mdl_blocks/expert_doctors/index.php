<?php
require_once(__DIR__ . '/../../config.php');
global $DB,$CFG,$USER,$PAGE,$OUTPUT; 
require_once($CFG->dirroot . "/course/externallib.php");

// require_login(null, false);
// if (isguestuser()) {
//     throw new require_login_exception('Guests are not allowed here.');
// }

$blockinstance = $DB->get_record('block_instances', ["blockname"=>'expert_doctors'], '*', MUST_EXIST);
$config = unserialize(base64_decode($blockinstance->configdata));
$doctors = $config->userselect1;
$nutritionists = $config->userselect2;

// echo '<pre>---';
// print_r($doctors);
// echo '</pre>';

echo $OUTPUT->header();
?>

<style>
  body, .container {
    background-color: #e7f4fa;
    font-family: 'Segoe UI', sans-serif;
  }

  .section-title {
    font-weight: 500;
    font-size: 3.5rem;
    color: #0c2b4b;
  }

  .section-subtext {
    color: #444;
    font-size: 1.40rem;
    max-width: 450px;
  }

  .btn-custom {
    background-color: #062f4f;
    color: white;
    border-radius: 30px;
    padding: 10px 37px;
    font-size: 1.20rem;
  }

  .badge-custom {
    background-color: #0c7cc0;
    color: white;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 1.00rem;
    font-weight: 600;
    margin-bottom: 10px;
    display: inline-block;
  }

  .card-custom {
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: none;
  }

  .card-img-top {
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    /* border-top-right-radius: 10px; */
  }

  .card-body p {
    font-size: 0.85rem;
    color: #555;
  }

  .read-more {
    font-weight: 600;
    font-size: 0.85rem;
    color: #0c7cc0;
  }

  .card-hd{
    padding: 12px;
  }

  .modelhead{
    display: flex;
  }
  .modelhead .img img{
    margin: 10px 25px;
    border-radius: 95px;
    width: 115px;
  }
  .modelhead .name{
      margin: auto;
      margin-left: 5px;
      color: #0c3552;
  }
  .modeldesc{
    padding: 25px;
  }

  .modal-content .btn-close{
    padding-top: 23px;
    padding-right: 25px;
  }

  #maincontent{
    height: 0px;
  }


</style>


<div class="container py-5">
  <div class="row align-items-center mb-4">
    <!-- <div class="col-md-5">
      <h2 class="section-title">Meet the Content Review Committee</h2>
      <p class="section-subtext mt-3">
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus...
      </p>
      <button class="btn btn-custom mt-4">View all experts</button>
    </div> -->

    <div class="col-md-12">
      <div class="mb-2">
        <span class="badge-custom">Doctors</span>
      </div>
      <div class="row g-3">
        <?php
      foreach ($doctors as $ids) { 
        $doctor = $DB->get_record("user",["id"=>$ids]);
        $plain_desc = strip_tags($doctor->description);
        $userpicture = new user_picture($doctor);
        $userpicture->size = 1; // Size f1.
        $img = $userpicture->get_url($PAGE)->out(false);
        echo '<div class="col-md-4">
          <div class="card card-custom">
            <div class="card-hd">
                <img src="'.$img.'" class="card-img-top" alt="Doctor">
            </div>
            <div class="card-body">
              <h6 class="card-title mb-1"><strong>Dr. '.$doctor->firstname.' '.$doctor->lastname.'</strong></h6>
              <p>'.$plain_desc.'</p>
            </div>
          </div>
        </div>';
      }
        ?>



      </div>

      <div class="mt-4 mb-2">
        <span class="badge-custom">Nutritionist</span>
      </div>
      <div class="row g-3">
      
      <?php
      foreach ($nutritionists as $ids) { 
        $nutrit = $DB->get_record("user",["id"=>$ids]);
        $plain_desc = strip_tags($nutrit->description);
        $userpicture = new user_picture($nutrit);
        $userpicture->size = 1; // Size f1.
        $img = $userpicture->get_url($PAGE)->out(false);
          
        echo '<div class="col-md-3">
          <div class="card card-custom h-100">
            <div class="card-hd">    
                <img src="'.$img.'" class="card-img-top" alt="Nutritionist">
            </div>
            <div class="card-body">
              <h6 class="card-title mb-1"><strong>'.$nutrit->firstname.' '.$nutrit->lastname.'</strong></h6>
              <p>'.$plain_desc.'</p>
            </div>
          </div>
        </div>';
      }
      ?>

        <div class="col-md-3">
          <div class="card card-custom h-100">
            <div class="card-hd">
                <img src="http://localhost/moodle/pluginfile.php/1/local_mb2builder/pageimages/2/Shilpa%20Joshi.png" class="card-img-top" alt="Nutritionist">
            </div>
            <div class="card-body">
              <h6 class="card-title mb-1"><strong>Dr. Shilpa Joshi</strong></h6>
              <p>Consultant Dietitian | Diabetes Educator Dr. Shilpa... <span class="read-more">Read more</span></p>
            </div>
          </div>
        </div>
        <div class="col-md-3" data-bs-toggle="modal" data-bs-target="#myModal">
          <div class="card card-custom h-100">
            <div class="card-hd">
                <img src="http://localhost/moodle/pluginfile.php/1/local_mb2builder/pageimages/2/c36efd51c527bb0c883a69baedf65a9b%402x.png" class="card-img-top" alt="Nutritionist">
            </div>
            <div class="card-body">
              <h6 class="card-title mb-1"><strong>Dr. Shilpa Varma</strong></h6>
              <p>PhD, MSc (Dietetics & Applied Nutrition)... <span class="read-more">Read more</span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      <div class="modelhead">
          <div class="img">
              <img src="http://localhost/moodle/pluginfile.php/1/local_mb2builder/pageimages/2/c36efd51c527bb0c883a69baedf65a9b%402x.png" class="card-img-top" alt="Nutritionist">
            </div>
            <div class="name">
                <h4 class="card-title mb-1"><strong>Dr. Shilpa Varma</strong></h4>
            </div>
      </div>
      <div class="modeldesc">
        RD, CDE, CPT, CISSN, ND, MDHA Monash FODMAP Trained Dietitian Founder, Nurture Health Solutions & Academy Sheryl S. Salis is a Registered Dietitian, Certified Diabetes Educator, and Certified Sports Nutritionist with over 25 years of clinical experience. She is the founder of Nurture Health Solutions and serves as a consultant nutritionist to the Juvenile Diabetes Foundation, Maharashtra Chapter. She holds key international roles, including Advisory Council Member of ISPAD and Lead of ISPAD’s Nutrition SIG, and was recently appointed as a Lancet Diabetes and Endocrinology Commission Contributor for global Type 1 diabetes policy. Sheryl has co-authored multiple national and international clinical nutrition guidelines, including the ISPAD 2022 Guidelines and RSSDI-NIN 2025 Guidelines. She is also the author of the award-winning book “Diet in Diabetes Simplified.” She has received numerous honors, including the Allan Drash Fellowship (2021), Sadhana Award (2022), and IAPEN National Award for Excellence (2023). She continues to contribute actively as a speaker, researcher, reviewer, and educator in the field of diabetes and clinical nutrition.
      </div>

    </div>
  </div>
</div>


<?php
// echo 'abhishek';
echo $OUTPUT->footer();