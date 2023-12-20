<?php
function wslexport(){
    global $wpdb;
 
    function filterData(&$str){ 
        $str = preg_replace("/\t/", "\\t", $str); 
        $str = preg_replace("/\r?\n/", "\\n", $str); 
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
    } 
     
    // Excel file name for download 
    $fileName = "wslcbexport.csv"; 
     
    // Column names 
    $fields = array('Provider #','Trainer #','Month','Day','Year','Permit #','Last Name','First Name','Middle Initial','Social Security Number','Month','Day','Year','Sex-M/F/X','Street Address','City','State','Zip Code','Feet','Inches','Weight','Email','Phone','Ex. M','Ex. D',' Ex. Yr'); 
     
    // Display column names as first row 
    $excelData = implode("\t", array_values($fields)) . "\n";     

    // Fetch records from database 
    if(!empty($_SESSION['usersid'])){      
        // $newuser_metaa = get_user_meta(5420);
        // echo '<pre>------';
        // print_r($newuser_metaa);
        // echo '</pre>';
        // die; 
        foreach ($_SESSION['usersid'] as $useid => $value) {
            
            $newuser_metaa = get_user_meta($useid);
            if(!empty($newuser_metaa['permit_expiration_date'])){
                $pex_m = date("m", strtotime($newuser_metaa['permit_expiration_date'][0]));
                $pex_d = date("d", strtotime($newuser_metaa['permit_expiration_date'][0]));
                $pex_y = date("Y", strtotime($newuser_metaa['permit_expiration_date'][0]));
            }else{
                $pex_m ='';
                $pex_d ='';
                $pex_y ='';
            }


            $ht_ft = round($newuser_metaa['height_ft_opt'][0]);
            $ht_in = round($newuser_metaa['height_in_opt'][0]);
            $permitdate = $wpdb->get_row('SELECT pn.assigntime FROM `wp_permit_number` pn WHERE `permit_number` = ' . $newuser_metaa['permit_number'][0] . '');
            if (!empty($permitdate)) {
                $newuser_metaa['permit_assign_date'][0] = $permitdate->assigntime;
            }
            // $lineData = array('aaaa','bbbbb','vvv');
            $lineData = array('78','5465',(date("m", $newuser_metaa['permit_assign_date'][0])),
                date("d", $newuser_metaa['permit_assign_date'][0]),
                date("Y", $newuser_metaa['permit_assign_date'][0]),
                $newuser_metaa['permit_number'][0],
                strtoupper($newuser_metaa["last_name"][0]),
                strtoupper($newuser_metaa["first_name"][0]),'MIDDLE','1111111111',
                date("m", strtotime($newuser_metaa['date_of_birth'][0])),
                date("d", strtotime($newuser_metaa['date_of_birth'][0])),
                date("Y", strtotime($newuser_metaa['date_of_birth'][0])),
                (($newuser_metaa['user_gender'][0] == "Male") ? "M" : "F"),
                strtoupper($newuser_metaa['billing_address_1'][0]),
                strtoupper($newuser_metaa['billing_city'][0]),
                strtoupper($newuser_metaa['billing_state'][0]),
                $newuser_metaa['billing_postcode'][0],$ht_ft,$ht_in,
                $newuser_metaa['weight_lbs'][0],
                strtoupper($newuser_metaa["billing_email"][0]),
                $newuser_metaa['billing_phone'][0],
                $pex_m,$pex_d,$pex_y
            ); 
            

            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
        
    }else{
        $excelData .= 'No records found...'. "\n"; 
    }
    
    // Headers for download 
    header("Content-Type: application/vnd.ms-excel"); 
    header("Content-Disposition: attachment; filename=\"$fileName\""); 
     
    // Render excel data 
    echo $excelData; 
     
    exit;
}

function printexport(){
    global $wpdb;
 
    function filterData(&$str){ 
        $str = preg_replace("/\t/", "\\t", $str); 
        $str = preg_replace("/\r?\n/", "\\n", $str); 
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
    } 
     
    // Excel file name for download 
    $fileName = "printing_file.csv"; 
     
    // Column names 
    $fields = array('License_no','Name','Sx','Mo','Dy','Yr','Ht','Wt','Exp','Address','City','State','Postcode','Student Number','License Type'); 
     
    // Display column names as first row 
    $excelData = implode("\t", array_values($fields)) . "\n";     

    if(!empty($_SESSION['usersid'])){
        foreach ($_SESSION['usersid'] as $useid => $value) {           
            $newuser_metaa = get_user_meta($useid);
            $udob = date('Y-m-d', strtotime($newuser_metaa['date_of_birth'][0]));
            $dob = new DateTime($udob);
            $now = new DateTime();
            $difference = $now->diff($dob);
            $age = $difference->y;
            $mynumber = $newuser_metaa['permit_number'][0];
            $get_pernumber = substr($mynumber, 0, 2);
            if ($age >= 21) {
                $ltype = "class 12";
            }else if ($age > 17 || $age < 21) {
                $ltype = "class 13";
            }
            
            $name = $newuser_metaa["first_name"][0]." ".$newuser_metaa["last_name"][0];
            $ht_ft = round($newuser_metaa['height_ft_opt'][0]);
            $ht_in = round($newuser_metaa['height_in_opt'][0]);
            $height = $ht_ft . "'" . $ht_in;
            $lineData = array($newuser_metaa['permit_number'][0],
                strtoupper($name),
                (($newuser_metaa['user_gender'][0] == "Male") ? "M" : "F"),
                date("m", strtotime($newuser_metaa['date_of_birth'][0])),
                date("d", strtotime($newuser_metaa['date_of_birth'][0])),
                date("Y", strtotime($newuser_metaa['date_of_birth'][0])),
                $height,
                $newuser_metaa['weight_lbs'][0],
                date("d/m/Y", strtotime($newuser_metaa['permit_expiration_date'][0])),
                strtoupper($newuser_metaa['billing_address_1'][0]),
                strtoupper($newuser_metaa['billing_city'][0]),
                strtoupper($newuser_metaa['billing_state'][0]),
                $newuser_metaa['billing_postcode'][0],$useid,$ltype,
            
            ); 
            

            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
        
    }else{
        $excelData .= 'No records found...'. "\n"; 
    }
    
    // Headers for download 
    header("Content-Type: application/vnd.ms-excel"); 
    header("Content-Disposition: attachment; filename=\"$fileName\""); 
     
    // Render excel data 
    echo $excelData; 
     
    exit;

}




?>