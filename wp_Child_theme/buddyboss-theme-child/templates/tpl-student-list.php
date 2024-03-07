<?php
/* Template Name: Student List */
get_header();

$current_user = wp_get_current_user();
$current_user_meta = metadata_exists('user', $current_user->ID, 'student_login_id');
$is_parent_user_meta = metadata_exists('user', $current_user->ID, 'parent_value_'.$current_user->ID);
$user_paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$user_offset = ($user_paged - 1) * 10;

$user_args = array(
    'order' => 'ASC',
    'orderby' => 'user_nicename',
    'number' => 10,
    'offset' => $user_offset,
    'meta_key' => 'parent_login_id',
    'meta_value' => $current_user->ID,
);
$user_query = new WP_User_Query($user_args);
$user_results = $user_query->get_results();
$total_users = $user_query->get_total();

global $wp_roles;
/*echo "<pre>";
print_r($user_results);
echo "</pre>";*/






/*$userDob = $student_age->meta_value;
    			$udob = date('Y-m-d',strtotime($userDob));
    			$dob = new DateTime($udob);
    			$now = new DateTime();
    			$difference = $now->diff($dob);
    			$age = $difference->y;
*/



if (!is_user_logged_in()):
    echo '<div class="section"><div class="alert alert-danger mt-5 text-center">Need To Login</div></div>';
elseif (array_intersect(array('student'), $current_user->roles)):
    echo '<div class="section"><div class="alert alert-danger mt-5 text-center">Sorry, this page is not accessible.</div></div>';
else:
    ?>
    <div class="col-12">
        <div class="content-area">
            <section class="gray">
                <div class="container">
                    <div class="row">
                        <div class="detail-wrapper col-md-12">
                            <div class="student-edit-table table-responsive">
                                <?php if ($total_users > 0) : ?>
                                    <a href="javascript:void(0);" class="student-create btn btn-sm btn-outline-primary col-xs-6 mb-2" data-id="student-create" data-name="student-create">
                                        <span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                        <span>Add Student</span>
                                    </a>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="student-username">Student Username</th>
                                                <th class="student-name">Student Name</th>
                                                <th class="student-email">Student Email</th>
                                                <th class="student-email">Student Role</th>
                                                <th class="student-action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php foreach ($user_results as $user) : ?>
                                            <tr class="student-detail">
                                                <td class="student-username"><?php echo $user->user_nicename; ?></td>
                                                <td class="student-name"><?php echo $user->first_name . " " . $user->last_name; ?></td>
                                                <td class="student-email"><?php echo $user->user_email; ?></td>
                                                <td class="student-email"><?php 
                                                $user = get_userdata( $user->ID );
                                                // $role = get_user_meta( $user->ID, 'wp_capabilities');
                                                $roles = ( array )$user->roles;
                                                $userroles = array();
                                                foreach($roles as $role){
                                                    array_push($userroles, $wp_roles->roles[$role]['name']);
                                                }
                                                echo implode(", ", $userroles);
                                                 /*$userdob = get_user_meta( $user->ID, 'student_birth_date');
                                                if(!empty($userdob)){
	                                                $udob = DateTime::createFromFormat('!d-m-Y',  $userdob);
													$udobts =  $udob->format('U'); 
	                                                $dateOfBirth = date("Y-m-d",$udobts);  
										    		// Get today's date
													$now = date("Y-m-d");
													// Calculate the time difference between the two dates
													$diff = date_diff(date_create($dateOfBirth), date_create($now));											 
													// Get the age in years, months and days
													$userage = $diff->format('%y');
													if($userage <= 12){
														echo "Primary Student";
													}elseif($userage > 12 && $userage <=18){
														echo "Secondary Student";
													}else{
														echo $userage;
													}

                                                }*/
                                                ?></td>
                                                <td class="student-action">
                                                    <a href="javascript:void(0);" class="student-edit btn btn-sm btn-outline-primary col-xs-6" data-id="<?php echo $user->ID; ?>" data-name="student-edit">
                                                        <span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                                        <span>Edit</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php
$user_id = get_current_user_id();
$value = get_field('select_student', 'user_' . $user_id);
/*if (!empty($value) && is_array($value)) {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Student Username</th>';
    echo '<th>Student Name</th>';
    echo '<th>Student Email</th>';
    echo '<th>Action</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($value as $user) {
        $user_nicename = $user->data->user_nicename;
        $display_name = $user->data->display_name;
        $user_email = $user->data->user_email;

        echo '<tr>';
        echo '<td>' . $user_nicename . '</td>';
        echo '<td>' . $display_name . '</td>';
        echo '<td>' . $user_email . '</td>';
        echo '<td class="student-action">';
        echo '<a href="javascript:void(0);" class="student-edit btn btn-sm btn-outline-primary col-xs-6" data-id="' . $user->ID . '" data-name="student-edit">';
        echo '<span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
        echo 'Edit</span>';
        echo '</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}*/
?>

                                <?php else:
                                    echo '<strong>Create Student Accounts.</strong>';
                                    echo '<div class="pt-3"><a href="javascript:void(0);" class="student-create btn btn-sm btn-outline-primary col-xs-6 mb-2" data-id="student-create" data-name="student-create"><span class="student-loader spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span><span>Add Student</span></a></div>';
                                endif;
                                ?>
                            </div>
                            <?php student_user_pagination($user_args); ?>
                            <div class="edit_student_form"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


<?php
endif;

// get_sidebar();
get_footer();