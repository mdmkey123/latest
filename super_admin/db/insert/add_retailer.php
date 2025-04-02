<?php
include '../config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $super_admin_id = $_SESSION['user_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $company_name = $_POST['company_name'];
    $gstn_number = $_POST['gstn_number'];
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $main_admin_id = mysqli_real_escape_string($conn, $_POST['main_admin_id']);
    $super_distributor_id = mysqli_real_escape_string($conn, $_POST['super_distributor_id']);
    $distributor_id = mysqli_real_escape_string($conn, $_POST['distributor_id']);
    
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    $query_check = "SELECT * FROM admin WHERE email = '$email' OR phone = '$phone' LIMIT 1";
    $result_check = mysqli_query($conn, $query_check);
    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email or Phone number already exists']);
        exit;
    }

    $date = date('Ymd');
    $query_count = "SELECT COUNT(*) AS total FROM admin WHERE DATE(created_at) = CURDATE()";
    $result_count = mysqli_query($conn, $query_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $count = $row_count['total'] + 1;
    $unique_id = "ZYNTROR" . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    if(isset($_FILES['payment_qr'])){
                 $image= $_FILES['payment_qr'];
                 $target_dir = "uploads/document/";
                 $target_file_qr = $target_dir . "payment_qr".$t;
                 $path = "https://mdmlock.in/jmvpay/admin/super_admin/db/insert/uploads/document/";
                 $document_url_qr = $path."payment_qr".$t;
             }

    $query = "INSERT INTO admin (first_name, last_name, email, phone, company_name, gstn_number, password,  address, pincode, super_admin_id, main_admin_id, super_distributor_id, distributor_id, created_at, status, unique_admin_id) 
              VALUES ('$first_name', '$last_name', '$email', '$phone', '$company_name', '$gstn_number', '$password',  '$address', '$pincode', '$super_admin_id',  '$main_admin_id', '$super_distributor_id', '$distributor_id', NOW(), 1, '$unique_id')";

    if (mysqli_query($conn, $query)) {
        if(isset($_FILES['payment_qr'])){
            $insertId= $conn->insert_id;
            mysqli_query($conn,"UPDATE admin SET payment_qr='$document_url_qr' WHERE id='$insertId'");
                    move_uploaded_file($_FILES["payment_qr"]['tmp_name'],  $target_file_qr);
                }
        echo json_encode(['status' => 'success', 'message' => 'Retailer added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add retailer: ' . mysqli_error($conn)]);
    }
}
?>
