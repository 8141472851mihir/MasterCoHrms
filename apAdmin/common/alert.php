 <?php if(isset($_SESSION['msg'])) { ?>
  <script type="text/javascript">
    swal({
    	 title: "Success!",
		   icon: "success",
	     text: "<?php echo addslashes($_SESSION['msg']); ?>",
	     timer: 3000
    });
  </script>
  <?php
	unset($_SESSION['msg']);
   } elseif (isset($_SESSION['bulk_csv_errors']) && is_array($_SESSION['bulk_csv_errors'])) {
	$errList = $_SESSION['bulk_csv_errors'];
	$successMsg = isset($_SESSION['bulk_csv_success']) ? $_SESSION['bulk_csv_success'] . "\n\n" : '';
	$errText = $successMsg . implode("\n", $errList);
	unset($_SESSION['bulk_csv_errors']);
	if (isset($_SESSION['bulk_csv_success'])) unset($_SESSION['bulk_csv_success']);
	?>
  <script type="text/javascript">
    swal({
      title: "Bulk Upload",
      icon: <?php echo !empty($successMsg) ? '"warning"' : '"error"'; ?>,
      text: <?php echo json_encode($errText); ?>,
      timer: <?php echo count($errList) > 10 ? 0 : 12000; ?>
    });
  </script>
  <?php
   } elseif (isset($_SESSION['msg1'])) { ?>
  <script type="text/javascript">
    swal({
    	 title: "Error!",
		   icon: "error",
	     text: "<?php echo addslashes($_SESSION['msg1']); ?>",
	     timer: 4000
    });
  </script>
  <?php
	unset($_SESSION['msg1']);
   } ?>