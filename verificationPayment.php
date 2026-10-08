<?php
$amount = $_POST['amount'];
$society_name = $_POST['society_name'];
$society_id = $_POST['society_id'];
$redirect_url = $_POST['redirect_url'];
$admin_email = $_POST['admin_email'];
$admin_mobile = $_POST['admin_mobile'];
$csrf = $_POST['csrf'];
if ($society_id == 1 || $society_id == 2) {
    $razorpay_key = "rzp_test_4LfSrUTBuvkzQq";
} else {
    $razorpay_key = "rzp_live_tE7vIVlqnKDTrf";
}
$currency = 'INR';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Razorpay -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .payment-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        #loadingSpinner {
            display: none;
        }
    </style>
</head>

<body onload="startPayment()">

    <div class="container payment-container text-center">
        <h2 class="mb-3 text-primary">Payment Verification</h2>
        <p class="text-muted">This page is used to verify payment details.</p>

        <div id="loadingSpinner" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Processing...</span>
            </div>
            <p class="mt-2">Launching Razorpay Checkout...</p>
        </div>

        <div id="paymentInfo" class="mt-4">
            <h5>Society: <span class="text-dark"><?= htmlspecialchars($society_name) ?></span></h5>
            <h5>Amount: <span class="text-success">₹<?= htmlspecialchars($amount) ?></span></h5>
        </div>
    </div>

    <form id="paymentForm" method="POST" action="<?= htmlspecialchars($redirect_url) ?>">
        <input type="hidden" name="payment_id" id="payment_id">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
        <input type="hidden" name="fidyPayRecharge" value="fidyPayRecharge">
    </form>

    <script>
        function startPayment() {
            document.getElementById('loadingSpinner').style.display = 'block';

            var options = {
                "key": "<?= $razorpay_key ?>",
                "amount": <?= $amount ?> * 100,
                "currency": "<?= $currency ?>",
                "name": "<?= htmlspecialchars($society_name) ?>",
                "description": "Recharge Payment",
                "prefill": {
                    "email": "<?= htmlspecialchars($admin_email) ?>",
                    "contact": "<?= htmlspecialchars($admin_mobile) ?>"
                },
                "handler": function(response) {
                    document.getElementById('payment_id').value = response.razorpay_payment_id;
                    document.getElementById('paymentForm').submit();
                },
                "modal": {
                    "ondismiss": function() {
                        window.location.href = "<?= htmlspecialchars($redirect_url) ?>";
                    }
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        }
    </script>

</body>

</html>