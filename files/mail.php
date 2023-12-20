<?php

    include '../conn.php';
$returndata = new stdClass();
$returndata->status = false;
$returndata->message = "Failed";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $phone = $_POST['phoneno'];
    $email = $_POST['email'];
    $query = $_POST['query'];
    $checkbox = $_POST['checkbox'];

 $sql = "INSERT INTO `enquiry` (`name`, `phoneno`, `email`, `message`, `condition_flag`) VALUES ('$name', '$phone', '$email', '$query', '$checkbox')";
 

$headers = "MIME-Version: 1.0" . "\r\n"; 
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
$msg = "  <html> 
    <head> 
        <title>Welcome to CodexWorld</title> 
    </head> 
    <body> 
        <h1>Interested user detail</h1> 
        
    <table cellspacing='0' style='border: 2px dashed #e0e0e0; width: 50%;'>
            <tr style='background-color: #e0e0e0;'> 
                 <th>Name :</th><td>".$name."</td> 
            </tr> 
            <tr> 
                 <th>Phone :</th><td>".$phone."</td>
            </tr> 
              <tr style='background-color: #e0e0e0;'> 
                  <th>Email :</th><td>".$email."</td>
            </tr> 
              <tr> 
                   <th>Massage :</th><td>".$query."</td>
            </tr> 
           
       </table>
    </body> 
    </html>
";
$msg = wordwrap($msg,70);
mail("abhishek@lds-international.in","Notification for interested user",$msg,$headers);

if( $result = $conn->query($sql)){
        $returndata->status = true;
        $returndata->message = "Your notification is successfully sent. ";
    }else{
        $returndata->status = false;
        $returndata->message = "Please fill complete form.";
    }
    

}
echo json_encode($returndata);