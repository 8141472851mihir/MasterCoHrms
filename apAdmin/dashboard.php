 <?php include 'common/header.php';
 if(isset( $_GET['f'])) {
      if(file_exists($_GET['f'].'.php'))
         {
              include $_GET['f'].'.php';
         } 
         else
         {
             include '404.php';
         }
  } else {
      include 'welcome.php';
  }
  include 'common/footer.php'; ?>