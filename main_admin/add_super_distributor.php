<?php 
include 'header.php'; 
include 'topbar.php'; 
include 'sidebar.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Using user_id from session
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add Super Distributor</h4>
                        </div>
                        <div class="card-body">
                            <div id="alert-container" style="display: none;" class="alert"></div>

                            <form id="super-distributor-form">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="full_name"
                                                placeholder="Enter Full Name" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                placeholder="Enter Email ID" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Mobile Number</label>
                                            <input type="tel" class="form-control" name="mobile"
                                                placeholder="Enter Mobile Number" pattern="[0-9]{10}" maxlength="10"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select State</label>
                                            <select class="form-control" name="state_id" id="state-select">
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" readonly class="form-control" name="username" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" name="password"
                                                placeholder="Enter Password" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                placeholder="Confirm Password" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <button type="button" id="submit-btn" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-generate username based on email
        document.querySelector("input[name='email']").addEventListener("input", function () {
            let email = this.value.trim();
            let usernameField = document.querySelector("input[name='username']");
            if (email.includes("@")) {
                let username = email.split("@")[0].toLowerCase() + "@superdistributor";
                usernameField.value = username;
            } else {
                usernameField.value = "";
            }
        });

        // Fetch states based on the logged-in main_admin_id
        function loadStates() {
            fetch("db/get/get_states.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "user_id=<?php echo $user_id; ?>" // Send user_id via AJAX
            })
            .then(response => response.json())
            .then(states => {
                let stateSelect = document.getElementById("state-select");
                stateSelect.innerHTML = '<option value="">Select State</option>';
                states.forEach(state => {
                    stateSelect.innerHTML += `<option value="${state.id}">${state.name}</option>`;
                });
            })
            .catch(error => console.error("Error loading states:", error));
        }

        document.addEventListener("DOMContentLoaded", loadStates); // Load states when page loads

        // Submit form via AJAX
        document.getElementById("submit-btn").addEventListener("click", function () {
            let formData = new FormData(document.getElementById("super-distributor-form"));

            fetch("db/insert/add_super_distributor.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                let alertContainer = document.getElementById("alert-container");
                alertContainer.style.display = "block";
                alertContainer.className = data.status === "success" ? "alert alert-success" : "alert alert-danger";
                alertContainer.innerText = data.message;

                if (data.status === "success") {
                    document.getElementById("super-distributor-form").reset();
                    loadStates(); // Reload states after successful addition
                }

                setTimeout(() => {
                    alertContainer.style.display = "none";
                }, 3000);
            })
            .catch(error => console.error("Error:", error));
        });
    </script>

<?php include 'footer.php'; ?> 
