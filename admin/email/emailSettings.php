<?php include_once('../../config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?>
<?php include_once(ROOT_PATH . '/admin/email/emailLogic.php'); ?>
<?php getEmailSettings(); ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php xecho(APP_NAME); ?> - excellence</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <!-- Custom styles -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/checkBox.js" type="text/javascript"></script>
  </head>
  <body>
    <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>
    <div class="container" style="margin-bottom: 50px;">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">

          <?php if (hasPermissionTo('manage-email')): ?>
            <a href="../dashboard.php" class="btn btn-primary" style="margin-bottom: 5px;">
              <span class="glyphicon glyphicon-chevron-left"></span>
              Dashboard
            </a>
            <hr>
            <h1 class="text-center">Email settings</h1>
          <?php endif; ?>

          <div class="infoBlock">
            You can generate a API key at: <a href="https://app.sendgrid.com/" target="_blank">SendGrid </a>
          </div><br>

         

            <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
              <!-- if editting category, we need that category's id -->
        


              <div class="form-group <?php xecho(isset($errors['apikey']) ? 'has-error' : ''); ?>">
                <label class="control-label">SendGrid API key</label>
               
                <input type="text" name="apikey" class="form-control" value="<?php if(isset($apikey)): echo($apikey);  endif; ?>">
                    <?php if (isset($errors['apikey'])): ?>
                        <span class="help-block"><?php xecho($errors['apikey']); ?></span>
                    <?php endif; ?>

                    <?php if (isset($apikey)): ?>

                    <?php endif; ?>
                </div>

              <div class="form-group <?php xecho(isset($errors['email_from']) ? 'has-error' : ''); ?>">
                <label class="control-label">Email sender</label>
               
                <input type="text" name="email_from" class="form-control" value="<?php if(isset($email_from)): echo($email_from);  endif; ?>">
                    <?php if (isset($errors['email_from'])): ?>
                        <span class="help-block"><?php xecho($errors['email_from']); ?></span>
                    <?php endif; ?>
                </div>
             

              <div class="form-group">
                <?php echo(getCSRFTokenField() . "\n") ?>

                  <button type="submit" name="edit_email" class="btn btn-info btn-block btn-lg">Save</button>
              </div>
            </form>
        </div>
      </div>
    </div>
   
  <?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>
</html>
