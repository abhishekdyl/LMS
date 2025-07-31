<?php
function parent_list(){
    $editajax = plugins_url() . '/sync-course/ajax/edit_child_ajax.php';
?>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <div class="divider"></div>
    <h3>Parent List</h3>
    <div class="row">
        <div class="col-md-8">
            <div class="err_msg" id="err_msg" style="color: red;"></div>
            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>S No.</th>
                        <th>Parent Name</th>                    
                        <th>Email</th>
                        <th>Subscription Type</th>
                        <th>Total Member</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S No.</th>
                        <th>Parent Name</th>                    
                        <th>Email</th>
                        <th>Subscription Type</th>
                        <th>Total Member</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>

                    <?php
                    global $wpdb;
    				$queryget = "SELECT mif.*,u.user_email,u.display_name FROM " . $wpdb->prefix . "members_info as mif INNER JOIN " .$wpdb->prefix. "users as u ON u.ID = mif.user_id ORDER BY u.display_name ASC"; 			
                    $memberlist = $wpdb->get_results($queryget);
                    $i = 1;
                    foreach ($memberlist as $members) {
                        if($members->payment_type==0){
                            $subtype = "Yearly";
                        }else if($members->payment_type==1){
                            $subtype = "monthly";
                        }
                        if($members->status==0){
                            $paystatus = "Not Completed";
                        }else if($members->status==1){
                            $paystatus = "Completed";
                        }

                        echo '<tr>
                            <td>'. $i. '</td>
                            <td>'.$members->display_name.'</td>
                            <td>'.$members->user_email.'</td>
                            <td>'.$subtype.'</td>
                            <td>'.$members->member_count.'</td>
                            <td>'.$members->price.'</td>
                            <td>'.$paystatus.'</td>
                            <td><a class="btn btn-danger" onclick="delete_parent('.$members->user_id.') ">Delete</a> | <a class="btn btn-info" href="'.get_permalink(wc_get_page_id('myaccount')).'&add_student&parentid='.$members->user_id.'" target="_blank">View</a></td>
                        </tr>';
                        $i++;
                    }
                    ?>
               </tbody>
            </table>
        </div>

    </div>
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function () {
        $('#example').DataTable();
    });
    function delete_parent(id){
        var editajax = '<?php echo $editajax;?>';
        //console.log('userid',id,'url',editajax);
         if (confirm("Are you sure you want to delete this Record?")) {
            jQuery.ajax({
                type: "POST",
                url: editajax,
                data: {
                    action: 'delete_parent',
                    userid: id
                },
                success: function (data) {
                    console.log('ddddddddd',data);
                    if(data==0){
                        document.getElementById('err_msg').innerHTML="This user can't be deleted";
                    }else if(data==4){
                        document.getElementById('err_msg').innerHTML="Delete All Child User First";
                    }else{
                        window.location.reload(); 
                    }
                }
            });
        }

    }
        
    </script>
<?php
}

?>