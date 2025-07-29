<?

  require_once($CFG->dirroot . "/course/externallib.php");

  $userpicture = new user_picture($USER);
  $userpicture->size = 1; // Size f1.
  echo $img = $userpicture->get_url($PAGE)->out(false);