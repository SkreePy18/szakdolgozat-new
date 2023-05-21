<?php include_once('../config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?>
<?php include_once(ROOT_PATH . '/tokens/tokenLogic.php'); ?>

<?php  
  // Get all users for selection
  $sql = "SELECT * FROM users";
  $users = getMultipleRecords($sql); 
  if(!$isEditing) {
    $opportunity_id = filter_input(INPUT_GET, "generate_code", FILTER_UNSAFE_RAW);
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php xecho(APP_NAME); ?> - Generate code</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <!-- Custome styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
  </head>
  <body>
    <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>

    <div class="container" style="margin-bottom: 50px;">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">

          <?php if (hasPermissionTo('view-opportunity-list')): ?>
            <a href="../opportunities/opportunityFilter.php?filter_opportunity=all" class="btn btn-primary" style="margin-bottom: 5px;">
              <span class="glyphicon glyphicon-chevron-left"></span>
              Opportunities
            </a>
            <hr>
          <?php endif; ?>

          <?php if (hasPermissionTo('generate-token')): ?>
            <h2 class="text-center">Generate code</h2>

            <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?<?php xecho(isset($_GET['edit_token']) ? "edit_token" : "generate_token")?>=<?php isset($opportunity_id) ? xecho($opportunity_id) : ""; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" id="owner_id" name="owner_id" value="<?php xecho($_SESSION['user']['id']); ?>">
            <input type="hidden" name="opportunity_id" value="<?php isset($opportunity_id) ? xecho($opportunity_id) : ""; ?>">

            <?php if($isEditing): ?>
              <input type="hidden" name="token_id" value="<?php xecho($token_id); ?>">
              <div class="form-group <?php xecho(isset($errors['token']) ? 'has-error' : '') ?>">
                <label class="control-label">Token</label><br>
                <input type="text" name="token" value="<?php xecho($token); ?>" class="form-control" disabled>
              </div>
              <div class="form-group <?php xecho(isset($errors['opportunity_id']) ? 'has-error' : '') ?>">
                <label class="control-label">Opportunity ID</label><br>
                <input type="text" name="opportunity_id" value="<?php xecho($opportunity_id); ?>" class="form-control" disabled>
              </div>
            <?php endif ?>
              <!-- Neptun code -->

              <div class="form-group <?php xecho(isset($errors['user_id']) ? 'has-error' : '') ?>">
                <label class="control-label">NEPTUN code of the user</label><br>
                <select class="form-control" name="user_id">
                <option value="<?php //xecho(isset($user_id)) ?>" disabled selected hidden>
                  <?php
                    if(isset($user_id)) {
                      $sql = "SELECT neptuncode FROM `users` WHERE id = ?";
                      $result = getSingleRecord($sql, 'i', [$user_id]);
                      $neptun_code = $result['neptuncode'];
                    }
                  ?>
                  Choose the user you want to generate the token to</option>
                  <?php
                      foreach ($users as $key => $user) {
                        $_user_id = $user["id"];
                        $neptun_code = $user["neptuncode"];
						            if(isset($user_id)) {
						            	if($user_id === $_user_id) {
						            	echo("<option value=$_user_id selected>" . $neptun_code . "</option>");
						            } else {
						            	echo("<option value=$_user_id>" . $neptun_code . "</option>");
						            }
						            } else {
						            	echo("<option value=$_user_id>" . $neptun_code . "</option>");
						            }
                      }
                  ?>
                </select>
                <?php if (isset($errors['user_id'])): ?>
                  <span class="help-block"><?php xecho($errors['user_id']); ?></span>
                <?php endif; ?>
              </div>

              <!-- Expiration date -->
              
              <div class="form-group <?php xecho(isset($errors['expiration_date']) ? 'has-error' : '') ?>">
                <label class="control-label">Expiration date</label>
                <input type="date" name="expiration_date" class="form-control" value="<?php if($isEditing) {
                  xecho($expiration_date);
                 } ?>" ></input>
                <?php if (isset($errors['expiration_date'])): ?>
                  <span class="help-block"><?php xecho($errors['expiration_date']); ?></span>
                <?php endif; ?>
              </div>

			   <div class="form-group <?php xecho(isset($errors['login_required']) ? 'has-error' : '') ?>">
           <label class="control-label">Require login</label><br>
				    <?php if (isset($_GET['edit_token'])): ?>
            
				    	<?php if ($login_required == "true"): ?>
				    		<input type="checkbox" id="login_required" name="login_required" value="true" checked disabled>
				    	<?php else: ?>
				    		<input type="checkbox" id="login_required" name="login_required" value="true" disabled>
				    	<?php endif; ?>
              
				    <?php else: ?>
                    <input type="checkbox" id="login_required" name="login_required" value="true" checked>
				    <?php endif; ?>
				    <label for="login_required"></label>
            <?php if (isset($errors['login_required'])): ?>
              <span class="help-block"><?php xecho($errors['login_required']); ?></span>
            <?php endif; ?>
              </div>

              <div class="form-group">
                <?php echo(getCSRFTokenField() . "\n") ?>
                  <button type="submit" name="<?php xecho(isset($_GET['edit_token']) ? "update_token" : "generate_token") ?>" class="btn btn-success btn-block btn-lg">
                    <?php xecho(isset($_GET['edit_token']) ? "Update token" : "Generate token") ?>
                  </button>
                
              </div>
            </form>
          <?php else: ?>
            <h2 class="text-center">No permissions to update or create token</h2>
          <?php endif ?>
        </div>
      </div>
    </div>
  <?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>