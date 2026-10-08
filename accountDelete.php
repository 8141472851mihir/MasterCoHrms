<?php
include "apAdmin/lib/dao.php";
$d=new dao();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./img/fav.png" type="image/png">
    <title>Delete Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            
                            <img src="img/logo.png" width="100">
                        </div>
                        <h1 class="text-danger text-center"><?php echo $d->app_name(); ?> Account Delete </h1>
                        
                        <p class="text-center">Type <strong>DELETE</strong> in the box below to confirm.</p>
                        <form id="deleteAccountForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" placeholder="Mobile Number" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for deletion</label>
                                <textarea class="form-control" id="reason" placeholder="Reason for deletion" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="confirmationInput" class="form-label">Confirmation</label>
                                <input type="text" class="form-control" id="confirmationInput" placeholder="Type DELETE to confirm">
                            </div>
                            <button type="submit" class="btn btn-danger w-100" id="deleteButton" disabled>Delete Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const confirmationInput = document.getElementById('confirmationInput');
        const deleteButton = document.getElementById('deleteButton');

        confirmationInput.addEventListener('input', () => {
            deleteButton.disabled = confirmationInput.value.toUpperCase() !== 'DELETE';
        });

        document.getElementById('deleteAccountForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const mobile = document.getElementById('mobile').value;
            const email = document.getElementById('email').value;
            const reason = document.getElementById('reason').value;

            if (!name || !mobile || !email) {
                alert('Please fill out all required fields.');
                return;
            }

            alert(`Account delete request sent successfully`);
            // Add your account deletion logic here.
             // Clear the form after submission
            form.reset();
            deleteButton.disabled = true;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
