<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

$super_admin_id = $_SESSION['user_id'];

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1
$offset = ($page - 1) * $limit; 

$result= mysqli_query($conn,"SELECT * FROM activation_requests WHERE status='Pending' LIMIT $limit OFFSET $offset");

$totalQuery = "SELECT COUNT(*) as total FROM activation_requests WHERE status='Pending'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Main Admin List</h4>
                            <div class="d-flex">
                                <select id="entriesPerPage" class="form-select d-inline-block w-auto">
                                    <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= ($limit == 25) ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= ($limit == 100) ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Request-Id</th>
                                            <th>Customer Registration Number</th>
                                            <th>Customer Name</th>
                                            <th>Status</th>
                                            <th>Retailer</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sr_no = $offset + 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $statusClass = ($row['status'] == 'active') ? 'active-status' : 'inactive-status';
                                            $statusText = ucfirst($row['status']);
                                            $customerId= $row['customer_id'];
                                            $retailerId= $row['admin_id'];
                                            $customerQuery= mysqli_query($conn,"SELECT * FROM customers WHERE id='$customerId'");
                                            $customer= $customerQuery->fetch_assoc();
                                            $retailerQuery= mysqli_query($conn,"SELECT * FROM admin WHERE id='$retailerId'");
                                            $retailer= $retailerQuery->fetch_assoc();
                                            ?>
                                            <tr>
                                                    <td><?php echo $sr_no; ?></td>
                                                    <td><?php echo $row['request_id']; ?></td>
                                                    <td><?php echo $customer['registration_number']; ?></td>
                                                    <td><a href="https://mdmlock.in/jmvpay/admin/super_admin/view_customer.php?id=<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></a></td>
                                                    <td><?php echo $row['status']; ?></td>
                                                    <td><a href="https://mdmlock.in/jmvpay/admin/super_admin/view_retailer.php?id=<?php echo $retailer['id']; ?>"><?php echo $retailer['first_name']." ".$retailer['last_name'] ; ?></a></td>
                                                    <td><?php echo $row['created_at']; ?></td>
                                                    <td>
                                                        <button class='status-btn active-status' onclick='toggleStatus(<?php echo $row['request_id'];?>,"<?php echo $super_admin_id;?>","<?php echo "super_admin";?>")' id='status-{$row['id']}'>
                                                            Activate
                                                        </button>
                                                    </td>
                                            </tr>
                                                  <?php
                                            $sr_no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <nav>
                                        <ul class="pagination pagination justify-content-center mb-0">

                                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleStatus(id,userId,userRole) {
        // alert(id +" "+  userId +" "+ userRole);
        fetch('db/update/approve_device_activation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id: id,
                    user_id: userId,  // Example of additional parameter
                    user_role: userRole // Another example
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    location.reload();
                    alert(data.message);
                } else {
                    alert("Failed to update status!");
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
<?php include 'footer.php'; ?>