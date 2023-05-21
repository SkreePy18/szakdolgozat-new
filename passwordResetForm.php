<?php include_once('config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?>
<?php include_once(INCLUDE_PATH . '/logic/userSignup.php'); ?>

<?php 
    if(isset($_GET['password_token'])) {
        $token = $_GET['password_token'];
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php xecho(APP_NAME) ?> - Login</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
  <!-- Custome styles -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>
  <div class="container" style="margin-bottom: 50px;">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <h2 class="text-center">Reset password</h2>
          <hr>
          <!-- display form error messages  -->
          <?php include_once(INCLUDE_PATH . "/layouts/messages.php") ?>
          <input type="hidden" name="password_token" id="password_token" value=<?php xecho(isset($token) ? $token : ''); ?>>
          <div class="form-group <?php xecho(isset($errors['new_password']) ? 'has-error' : ''); ?>">
            <label class="control-label">New password</label>
            <input type="password" required name="new_password" id="new_password" class="form-control">
            <?php if (isset($errors['new_password'])): ?>
              <span class="help-block"><?php xecho($errors['new_password']) ?></span>
            <?php endif; ?>
          </div>

          <div class="form-group <?php xecho(isset($errors['new_password_confirm']) ? 'has-error' : ''); ?>">
            <label class="control-label">Confirm password</label>
            <input type="password" required name="new_password_confirm" id="new_password_confirm" class="form-control">
            <?php if (isset($errors['new_password_confirm'])): ?>
              <span class="help-block"><?php xecho($errors['new_password_confirm']) ?></span>
            <?php endif; ?>
          </div>
         
          <div class="form-group">
            <?php echo(getCSRFTokenField() . "\n") ?>
            <button type="submit" name="reset_password" class="btn btn-success">Reset</button>
          </div>
          <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </form>
      </div>
    </div>
  </div>
<?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>
