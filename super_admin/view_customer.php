<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$super_admin_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid customer!'); window.location.href='customer_list.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT 
            customers.*, 
            sd.name AS super_distributor_name,
            ma.name AS main_admin_name, 
            retailer.first_name AS retailer_first_name, 
            retailer.last_name AS retailer_last_name,
            distributor.full_name AS distributor_name, 
            devices.*,  -- Fetching all device details
            emi_details.product_price, 
            emi_details.downpayment, 
            emi_details.tenure, 
            emi_details.loan_amount, 
            emi_details.downpayment_date, 
            emi_details.processing_fees, 
            emi_details.emi_amount, 
            emi_details.total_emi_amount, 
            emi_details.other_amount, 
            emi_details.first_emi_date, 
            emi_details.guarantor_name, 
            emi_details.guarantor_number, 
            emis.id AS emi_id, 
            emis.serial_number AS emi_serial_number, 
            emis.emi_date, 
            emis.paid_date, 
            emis.type, 
            emis.amount AS emi_amount_details, 
            emis.emi_date AS emi_due_date, 
            emis.paid_date AS emi_paid_date
          FROM customers 
          LEFT JOIN devices ON customers.device_id = devices.id
          LEFT JOIN emi_details ON devices.id = emi_details.device_id
          LEFT JOIN emis ON emi_details.id = emis.emi_details_id
          LEFT JOIN admin AS retailer ON devices.admin_id = retailer.id
          LEFT JOIN distributors AS distributor ON retailer.distributor_id = distributor.id
          LEFT JOIN super_distributor AS sd ON retailer.super_distributor_id = sd.id
          LEFT JOIN main_admin AS ma ON retailer.main_admin_id = ma.id
          WHERE customers.id = '$id' AND retailer.super_admin_id = '$super_admin_id'";





$result = mysqli_query($conn, $query);

if (!$row = mysqli_fetch_assoc($result)) {
    echo "<script>alert('Customer not found or access denied!'); window.location.href='customer_list.php';</script>";
    exit;
}

?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">

            <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Customer Details</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Customer Name:</strong> <?= htmlspecialchars($row['name']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                            <p><strong>Bank Name:</strong> <?= htmlspecialchars($row['bank_name']) ?></p>
                            <p><strong>Bank Account Number:</strong> <?= htmlspecialchars($row['bank_account_number']) ?></p>
                            <p><strong>IFSC Code:</strong> <?= htmlspecialchars($row['ifsc_code']) ?></p>
                            <p><strong>Registration Number:</strong> <?= htmlspecialchars($row['registration_number']) ?></p>
                            <p><strong>Profile Image:</strong><br> 
                                <img src="<?= htmlspecialchars($row['profile_image']) ?>" alt="Profile Image" width="150">
                            </p>
                            <p><strong>Aadhar Card Front:</strong><br> 
                                <img src="<?= htmlspecialchars($row['aadhar_card_image_front']) ?>" alt="Aadhar Card Front" width="150">
                            </p>
                            <p><strong>Aadhar Card Back:</strong><br> 
                                <img src="<?= htmlspecialchars($row['aadhar_card_image_back']) ?>" alt="Aadhar Card Back" width="150">
                            </p>
                            <p><strong>Pan Card:</strong><br> 
                                <img src="<?= htmlspecialchars($row['pan_card_image']) ?>" alt="Pan Card" width="150">
                            </p>
                            <p><strong>Passbook Image:</strong><br> 
                                <img src="<?= htmlspecialchars($row['passbook_image']) ?>" alt="Passbook Image" width="150">
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Device Details</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Brand:</strong> <?= htmlspecialchars($row['brand']) ?></p>
                            <p><strong>Model:</strong> <?= htmlspecialchars($row['model']) ?></p>
                            <p><strong>IMEI1:</strong> <?= htmlspecialchars($row['imei1']) ?></p>
                            <p><strong>IMEI2:</strong> <?= htmlspecialchars($row['imei2']) ?></p>
                            <p><strong>Manufacturer:</strong> <?= htmlspecialchars($row['manufacturer']) ?></p>
                            <p><strong>Serial Number:</strong> <?= htmlspecialchars($row['serial_number']) ?></p>
                            <p><strong>Version:</strong> <?= htmlspecialchars($row['version']) ?></p>
                            <p><strong>SIM 1:</strong> <?= htmlspecialchars($row['sim_1']) ?></p>
                            <p><strong>SIM 2:</strong> <?= htmlspecialchars($row['sim_2']) ?></p>
                            <p><strong>Latitude:</strong> <?= htmlspecialchars($row['latitude']) ?></p>
                            <p><strong>Longitude:</strong> <?= htmlspecialchars($row['longitude']) ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                            <p><strong>Locked:</strong> <?= $row['locked'] ? 'Yes' : 'No' ?></p>
                            <p><strong>Created At:</strong> <?= date("d-m-Y H:i:s", strtotime($row['created_at'])) ?></p>
                            <p><strong>Control Panel : </strong><a href="https://mdmlock.in/jmvpay/admin/super_admin/control_panel.php?id=<?php echo $row['id'];?>">View</a></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">EMI Details</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Product Price:</strong> ₹<?= number_format($row['product_price'], 2) ?></p>
                            <p><strong>Downpayment:</strong> ₹<?= number_format($row['downpayment'], 2) ?></p>
                            <p><strong>Tenure:</strong> <?= htmlspecialchars($row['tenure']) ?> months</p>
                            <p><strong>Loan Amount:</strong> ₹<?= number_format($row['loan_amount'], 2) ?></p>
                            <p><strong>Downpayment Date:</strong>
                                <?= date("d-m-Y", strtotime($row['downpayment_date'])) ?></p>
                            <p><strong>Processing Fees:</strong> ₹<?= number_format($row['processing_fees'], 2) ?></p>
                            <p><strong>EMI Amount:</strong> ₹<?= number_format($row['emi_amount'], 2) ?></p>
                            <p><strong>Total EMI Amount:</strong> ₹<?= number_format($row['total_emi_amount'], 2) ?></p>
                            <p><strong>Other Amount:</strong> ₹<?= number_format($row['other_amount'], 2) ?></p>
                            <p><strong>First EMI Date:</strong> <?= date("d-m-Y", strtotime($row['first_emi_date'])) ?>
                            </p>
                            <p><strong>Guarantor Name:</strong> <?= htmlspecialchars($row['guarantor_name']) ?></p>
                            <p><strong>Guarantor Number:</strong> <?= htmlspecialchars($row['guarantor_number']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Administrative Details</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Super Distributor:</strong> <?= $row['super_distributor_name'] ?></p>
                            <p><strong>Main Admin:</strong> <?= $row['main_admin_name'] ?></p>
                            <p><strong>Distributor:</strong> <?= $row['distributor_name'] ?></p>
                            <p><strong>Retailer:</strong>
                                <?= $row['retailer_first_name'] . ' ' . $row['retailer_last_name'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actions</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if($row['contacts_document']!=null){
                                ?>
                                <p><strong>Contacts : </strong><a href="db/get/downloadcsv.php?id=<?php echo $row['device_id'];?>">View</a></p>
                                <?php
                            }else{
                                ?>
                                <p><strong>Get Contacts:</strong> <button class="status-btn inactive-status"
                                    onclick="getContacts(<?= $row['device_id']; ?>)" title="Delete">Download</button></p>
                                <?php
                            }
                            ?>
                            
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">EMI Transactions</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>EMI ID</th>
                                            <th>Serial Number</th>
                                            <th>EMI Date</th>
                                            <th>Paid Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $emi_result = mysqli_query($conn, $query);
                                        while ($emi_row = mysqli_fetch_assoc($emi_result)) {
                                            ?>
                                            <tr>
                                                <td><?= $emi_row['emi_id'] ?></td>
                                                <td><?= $emi_row['serial_number'] ?></td>
                                                <td><?= date("d-m-Y", strtotime($emi_row['emi_date'])) ?></td>
                                                <td><?= $emi_row['paid_date'] ? date("d-m-Y", strtotime($emi_row['paid_date'])) : 'Not Paid Yet' ?>
                                                </td>
                                                <td>₹<?= number_format($emi_row['emi_amount_details'], 2) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 mx-auto">
                    <a href="user_list.php" class="btn btn-secondary mt-3">Back to List</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function deleteCustomer(id) {
            if (confirm("Are you sure you want to delete this customer?")) {
                fetch('db/delete/delete_customer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === "success") {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
        
        function getContacts(id) {
        // alert(id +" "+  userId +" "+ userRole);
            fetch('db/update/push_notif.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        device_id: id,
                        body: "GETCONTACTS uzhm8669",  // Example of additional parameter
                        title: "Controls" // Another example
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Your contact will be downloaded shortly. Please refresh the page as needed.");
                        location.reload();
                    } else {
                        alert("Failed to update status!");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    <?php include 'footer.php'; ?>