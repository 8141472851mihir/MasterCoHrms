<?php if(isset($_SESSION['msg'])) { ?>
<script type="text/javascript">
	Lobibox.notify('success', {
    pauseDelayOnHover: true,
    continueDelayOnInactiveTab: false,
    position: 'top right',
    icon: 'fa fa-check-circle',
    msg: '<?php echo $_SESSION['msg']; ?>.'
    });
</script>
<?php 
unset($_SESSION['msg']);
} elseif (isset($_SESSION['msg1'])) { ?>
<script type="text/javascript">
   Lobibox.notify('error', {
    pauseDelayOnHover: true,
    continueDelayOnInactiveTab: false,
    position: 'top right',
    icon: 'fa fa-times-circle',
    msg: '<?php echo $_SESSION['msg1']; ?>.'
    });
  </script>
<?php unset($_SESSION['msg1']); } ?>