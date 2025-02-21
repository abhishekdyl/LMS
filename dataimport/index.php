<?php
// DB Connection
$conn = new mysqli('localhost', 'root', '', 'databasecsv'); 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    
    // Check if file is selected
    if ($_FILES['csv_file']['error'] === 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        
        // Get csv file 
        if(($handle = fopen($file,"r")) !== FALSE) { 
                    
            // Skip headers rows
            for ($i = 1; $i < 7; $i++) {
                fgetcsv($handle);
            }

            // Prepare SQL for inserting into the database table
            $stmt = $conn->prepare("INSERT INTO csv_table (date, academic_year, session, allotted_category, voucher_type, voucher_no, roll_no, admno_unique_id, status, fee_category, faculty, program, department, batch, receipt_no, fee_head, due_amount, paid_amount, concession_amount, scholarship_amount, reverse_concession_amount, write_off_amount, adjusted_amount, refund_amount, fund_transfer_amount, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            // Bind parameters
            $stmt->bind_param("ssssssssssssssssssssssssss", $date, $academic_year, $session, $allotted_category, 
                $voucher_type, $voucher_no, $roll_no, $admno_unique_id, $status, $fee_category, $faculty, $program, 
                $department, $batch, $receipt_no, $fee_head, $due_amount, $paid_amount, $concession_amount, 
                $scholarship_amount, $reverse_concession_amount, $write_off_amount, $adjusted_amount, 
                $refund_amount, $fund_transfer_amount, $remarks);

            // Loop for CSV data
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Assign values from CSV data to variables
                $date = $data[1];
                $academic_year = $data[2];
                $session = $data[3];
                $allotted_category = $data[4];
                $voucher_type = $data[5];
                $voucher_no = $data[6];
                $roll_no = $data[7];
                $admno_unique_id = $data[8];
                $status = $data[9];
                $fee_category = $data[10];
                $faculty = $data[11];
                $program = $data[12];
                $department = $data[13];
                $batch = $data[14];
                $receipt_no = $data[15];
                $fee_head = $data[16];
                $due_amount = $data[17];
                $paid_amount = $data[18];
                $concession_amount = $data[19];
                $scholarship_amount = $data[20];
                $reverse_concession_amount = $data[21];
                $write_off_amount = $data[22];
                $adjusted_amount = $data[23];
                $refund_amount = $data[24];
                $fund_transfer_amount = $data[25];
                $remarks = $data[26];

                // Execute the prepared statement
                if (!$stmt->execute()) {
                    // insertion errors
                    echo "Error executing query: " . $stmt->error . "<br>";
                }
            }
            fclose($handle);
            echo "Data successfully imported.";

        }
    } else {
        echo "Error in file upload.";
    }
}

// Function to Distribute Data to Respective Tables
function distributeData($conn)
{
    // 1 Fetch Bulk Data from CSV Table
    $sql = "SELECT DISTINCT faculty, fee_category, fee_head as fee_name FROM csv_table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $branch_name = $row['faculty']; 
            $fee_category = $row['fee_category'];
            $fee_name = $row['fee_name']; 


            // Insert2 into Branches Table (if not exists)
            $branchQuery = "INSERT INTO branches (branch_name) 
                            SELECT * FROM (SELECT '$branch_name') AS tmp 
                            WHERE NOT EXISTS (SELECT branch_name FROM branches WHERE branch_name = '$branch_name') LIMIT 1";
            $conn->query($branchQuery);
            $branch_id = $conn->insert_id ?: $conn->query("SELECT id FROM branches WHERE branch_name = '$branch_name'")->fetch_assoc()['id'];

            // // Insert3 into Fee Collection Type (Academic Default)
            // $collection_head = "Academic";
            // $collection_desc = "Academic";

            // $collectionQuery = "INSERT INTO feecollectiontype (Collection_head, Collection_desc, Br_id) 
            //                     SELECT * FROM (SELECT '$collection_head', '$collection_desc', '$branch_id') AS tmp 
            //                     WHERE NOT EXISTS (SELECT id FROM feecollectiontype WHERE Br_id = '$branch_id') LIMIT 1";
            // $conn->query($collectionQuery);
            // $collection_id = $conn->insert_id ?: $conn->query("SELECT id FROM feecollectiontype WHERE Br_id = '$branch_id'")->fetch_assoc()['id'];


            // Insert4 into Fee Category (if not exists)
            $feeCategoryQuery = "INSERT INTO feecategory (fee_category, br_id) 
                                 SELECT * FROM (SELECT '$fee_category', '$branch_id') AS tmp 
                                 WHERE NOT EXISTS (SELECT id FROM feecategory WHERE fee_category = '$fee_category' AND br_id = '$branch_id') LIMIT 1";
            $conn->query($feeCategoryQuery);
            $fee_category_id = $conn->insert_id ?: $conn->query("SELECT id FROM feecategory WHERE fee_category = '$fee_category' AND br_id = '$branch_id'")->fetch_assoc()['id'];


            // echo "<pre>";
            // print_r($branch_id);
            // print_r($row);
            // echo "</pre>";
            // die;

            // Insert5 into Fee Types Table

            $feeTypesQuery = "INSERT INTO feetypes (Fee_category, F_name, Collection_id, Br_id, Seq_id, Fee_type_ledger, Fee_headtype) 
                              VALUES ('$fee_category_id', '$fee_name', '$collection_id', '$branch_id', '$seq_id', '$fee_name', '$fee_headtype')";
            
            if ($conn->query($feeTypesQuery) === TRUE) {
                echo "Inserted Fee Type: $fee_name for Branch: $branch_name\n";
            } else {
                echo "Error inserting Fee Type: " . $conn->error . "\n";
            }
        }
    } else {
        echo "No data found in csv_table.";
    }

    echo "<strong>Data Distribution Completed!</strong>";
}

distributeData($conn);

?>

<!-- Include mandetory CDN -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- CSS -->
<style>
#main .uploadata label i{
    color : #5cb85c;
}
#main .uploadata label{
    padding-top: 150px;
    width: 100%;
    font-size: 25%;
}
#main .uploadata {
    background-color: #a0d6a073;
    border: 4px dashed #5cb85c;
    border-radius: 30px;
    margin: 10px 0;
    text-align: center;
    font-size: 60px;
    font-weight: bolder;
}
#main .field {
    padding-bottom: 50px;
    width: 100%;
    opacity: 0;
}
</style>

<!-- HTML Form for uploading CSV -->

<div class="container">
    <div class="row">
      <div class="col-md-12" id="main">
        <h1>Upload CSV Or Drag & Drop CSV</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="uploadata" >
            <label for="fileUpload"><i class="fa fa-plus"></i></label>
            <input type="file" name="csv_file" id="fileUpload" class="field" onchange="showFileName()" required>
            <span id="fileName" class="filename"></span>
            </div>
            <input type="submit" name="submit" class="btn btn-success" value="Import Data">
        </form>

      </div>

      <!-- HTML for show record counts -->

      <div class="col-md-12" id="second">
        <h2>Record Count</h2>
        <?php
        $formatter = new NumberFormatter("en_IN", NumberFormatter::DECIMAL);
        // $sql = "SELECT * from users";
        $sql = "SELECT 
            COUNT(*) AS total_records,
            SUM(due_amount) AS total_due_amount,
            SUM(paid_amount) AS total_paid_amount,
            SUM(concession_amount) AS total_concession_amount,
            SUM(scholarship_amount) AS total_scholarship_amount,
            -- SUM(scholar_amount) AS total_scholarship_amount,
            SUM(refund_amount) AS total_refund_amount
        FROM csv_table";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                    echo "Total Count : ". $formatter->format($row['total_records']) ."<br>";
                    echo "Total Due amount : ". $formatter->format($row['total_due_amount']) ."<br>";
                    echo "Total Paid amount : ". $formatter->format($row['total_paid_amount']) ."<br>";
                    echo "Total Concession amount : ". $formatter->format($row['total_concession_amount']) ."<br>";
                    echo "Total Scholarship amount : ". $formatter->format($row['total_scholarship_amount']) ."<br>";
                    echo "Total Refund amount : ". $formatter->format($row['total_refund_amount']) ."<br>";
            } else {
                echo "No records found.";
            }
        ?>
      </div>
    </div>
</div>

<!-- Script to print the uploaded file NAme -->
<script>
    function showFileName() {
        const fileInput = document.getElementById('fileUpload');
        const fileName = document.getElementById('fileName');
        
        const file = fileInput.files[0];
        if (file) {
            fileName.textContent = file.name;
        } else {
            fileName.textContent = "";
        }
    }
</script>

