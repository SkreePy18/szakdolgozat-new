<?php include_once('../config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?> 
<?php include_once(ROOT_PATH . '/user/userSettingsLogic.php'); ?>

<?php 
    $user = $_SESSION['user'];
    $user_id = $user['id'];

    $sql = "SELECT * FROM `users` WHERE id=? LIMIT 1";
    $user_data = getSingleRecord($sql, "i", [$user_id])
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php xecho(APP_NAME) ?> - View opportunities</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <!-- Custome styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
  </head>
  <body>
    <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>


    <div class="container" style="margin-bottom: 50px;">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          
                <a href="<?php xecho(BASE_URL); ?>excellence/excellenceFilter.php?id=1" class="btn btn-primary" style="margin-bottom: 5px;">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    Excellence list
                </a>
            <hr>
          
            <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <input type="hidden" id="user_id" name="user_id" value="<?php xecho($_SESSION['user']['id']); ?>">
                
              <div class="form-group <?php xecho(isset($errors['profile_picture']) ? 'has-error' : '') ?>">
                <label class="control-label">Profile picture</label><br>

                <img class="profilePicture" src="uploads/<?php if (isset($user_data['profile_picture']) && $user_data['profile_picture'] !== "") { xecho($user_data['profile_picture']); } else {xecho("default-avatar.png");} ?>" width = "64" height="64">

                <?php if ($isEditing === true): ?>
                    <br><br>
                    <input type="file" accept="image/png, image/gif, image/jpeg" name="profile_picture" value="" class="form-control">
                <?php endif; ?>
              </div>

              <div class="form-group <?php xecho(isset($errors['username']) ? 'has-error' : '') ?>">
                <label class="control-label">Username</label>
                <input type="text" name="username" value="<?php isset($user['username']) ? xecho($user['username']) : ""; ?>" class="form-control" required disabled>
                <?php if (isset($errors['username'])): ?>
                  <span class="help-block"><?php xecho($errors['username']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php xecho(isset($errors['fullname']) ? 'has-error' : '') ?>">
                <label class="control-label">Full name</label>
                <input type="text" name="fullname" value="<?php isset($user_data['fullname']) ? xecho($user_data['fullname']) : ""; ?>" class="form-control" required <?php ($isEditing != true) ? xecho("disabled") : "" ?>>
                <?php if (isset($errors['fullname'])): ?>
                  <span class="help-block"><?php xecho($errors['fullname']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php xecho(isset($errors['neptuncode']) ? 'has-error' : '') ?>">
                <label class="control-label">NEPTUN code</label>
                <input type="text" name="neptuncode" value="<?php isset($user_data['neptuncode']) ? xecho($user_data['neptuncode']) : ""; ?>" class="form-control" required <?php ($isEditing != true) ? xecho("disabled") : "" ?>>
                <?php if (isset($errors['neptuncode'])): ?>
                  <span class="help-block"><?php xecho($errors['neptuncode']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php xecho(isset($errors['email']) ? 'has-error' : '') ?>">
                <label class="control-label">Email</label>
                <input type="text" name="email" value="<?php isset($user_data['email']) ? xecho($user_data['email']) : ""; ?>" class="form-control" required <?php ($isEditing != true) ? xecho("disabled") : "" ?>>
                <?php if (isset($errors['email'])): ?>
                  <span class="help-block"><?php xecho($errors['email']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group form-check form-switch <?php xecho(isset($errors['opportunity']) ? 'has-error' : '') ?>">
                <label class="control-label">Send email about opportunity completetion</label><br>
                <input type="checkbox" id="send_email" name="send_email" <?php ($user_data["send_email"] == 1) ? xecho("checked") : ""; ?> class="form-switch" <?php ($isEditing != true) ? xecho("disabled") : "" ?>>
                <label for="send_email"></label>
              </div>
              
              <?php echo(getCSRFTokenField() . "\n") ?>
              <button type="submit" name="<?php ($isEditing) == true ? xecho("save_profile") : xecho("edit_profile"); ?>" class="btn btn-success btn-block btn-lg"><?php ($isEditing) == true ? xecho("Save") : xecho("Edit"); ?> profile settings</button><br>
            </form>

            
    
        </div>
    </div>
  <?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>