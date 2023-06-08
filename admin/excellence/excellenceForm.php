<?php include_once('../../config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?>
<?php include_once(ROOT_PATH . '/admin/excellence/excellenceLogic.php'); ?>

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
  <body onload="onChange('choose_points', 'point_selector'); onChange('choose_users', 'user_selector')">
    <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>
    <div class="container" style="margin-bottom: 50px;">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">

          <?php if (hasPermissionTo('view-point-types')): ?>
            <a href="excellenceList.php" class="btn btn-primary" style="margin-bottom: 5px;">
              <span class="glyphicon glyphicon-chevron-left"></span>
              Excellence lists
            </a>
            <hr>
          <?php endif; ?>

          <?php if (canUpdateObjectByID('point-type', $excellence_id) || hasPermissionTo('create-excellence-list') ): ?>

            <?php if ($isEditing === true ): ?>
              <h2 class="text-center">Update excellence list</h2>
            <?php else: ?>
              <h2 class="text-center">Create excellence list</h2>
            <?php endif; ?>

            <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
              <!-- if editting category, we need that category's id -->
              <?php if ($isEditing === true): ?>
                <input type="hidden" name="excellence_id" value="<?php xecho($excellence_id); ?>">
              <?php endif; ?>

              <input type="hidden" name="created_by" value="<?php xecho($_SESSION['user']['id']); ?>">
              
              <div class="form-group <?php xecho(isset($errors['name']) ? 'has-error' : ''); ?>">
                <label class="control-label">Name of excellence list</label>
                <?php if ($isEditing === true): ?>
                 <input type="text" name="name" value="<?php xecho($name); ?>" class="form-control" required>
                <?php else: ?>
                  <input type="text" name="name" class="form-control" required>
                <?php endif; ?>
                <?php if (isset($errors['name'])): ?>
                  <span class="help-block"><?php xecho($errors['name']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group">
              <label class="control-selector">Choose the type of points - Default: all</label><br>
                <?php if ($isEditing != true || $isEditing === true && isset($type_of_points) && $type_of_points === "all"): ?>
                <input type="checkbox"name='choose_points'  id='choose_points' onchange="onChange('choose_points', 'point_selector')" class="form-check-input">
                <?php elseif ($isEditing === true || isset($type_of_points) && $type_of_points !== "all"): ?>
                  <input type="checkbox" name='choose_points' id='choose_points' onchange="onChange('choose_points', 'point_selector')" class="form-check-input" value="true" checked>
                  <?php endif; ?>
                <?php if (isset($errors['type'])): ?>
                  <span class="help-block"><?php xecho($errors['type']); ?></span>
                <?php endif; ?>
                 <label for = 'choose_points' class="form-check-label"></label>
              </div>

              <div id="point_selector" class="form-group <?php xecho(isset($errors['user_id']) ? 'has-error' : '') ?>">
                <label class="control-selector">Type of points</label><br>
                
                <select class='form-control' name="point_types[]" multiple="true">
                  <?php 
                    // Get the types of points 
                    $sql = "SELECT * FROM `opportunity_points_type`";
                    $result = getMultipleRecords($sql);
                    if($result)
                    {
                      if (isset($type_of_points) && $type_of_points !== "all") 
                      {
                        foreach($result as $key => $point_type) 
                        {
                          if (! in_array($point_type["id"], $type_of_points)) {
                            echo("<option value=" . $point_type['id'] . ">". ucfirst($point_type['name']) . "</option>");
                          } 
                          else 
                          {
                            echo("<option selected value=" . $point_type['id'] . ">". ucfirst($point_type['name']) . "</option>");
                          }
                        } 
                      }
                      else 
                      {
                        foreach($result as $key => $point_type) 
                        {  
                          echo("<option value=" . $point_type['id'] . ">". ucfirst($point_type['name']) . "</option>");
                        } 
                      }
                    }
                    ?>
                  </select>
                <?php if (isset($errors['user_id'])): ?>
                  <span class="help-block"><?php xecho($errors['user_id']); ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group <?php xecho(isset($errors['type']) ? 'has-error' : ''); ?>">
                <!-- <input type="checkbox" id='choose_users' onchange="onChange('choose_users', 'user_selector')" class="form-check-input"> -->
                <label class="control-selector">Choose users - default: all</label><br>
                <?php if ($isEditing != true || $isEditing === true && isset($selected_users) && $selected_users === "all"): ?>
                <input type="checkbox" id='choose_users' name='choose_users' onchange="onChange('choose_users', 'user_selector')" >
                <?php elseif ($isEditing === true && isset($selected_users) && $selected_users !== "all"): ?>
                  <input type="checkbox" id='choose_users' name='choose_users' onchange="onChange('choose_users', 'user_selector')" value="true" checked>
                 <?php endif; ?>
                 <label for="choose_users" class="form-check-label"></label>

                <?php if (isset($errors['type'])): ?>
                  <span class="help-block"><?php xecho($errors['type']); ?></span>
                <?php endif; ?>
                 
              </div>

              <div id="user_selector" class="form-group <?php xecho(isset($errors['user_id']) ? 'has-error' : '') ?>">
                <label class="control-label">Students</label><br>
                <select class="form-control" name="students[]" multiple="true">
                  <?php 
                    // Get the types of points 
                    $sql = "SELECT neptuncode FROM `users`";
                    $result = getMultipleRecords($sql);
                    if($result){
                      if (isset($selected_users) && $selected_users !== "all") {
                        foreach($result as $key => $user) {
                          if (! in_array($user["neptuncode"], $selected_users)) {
                            echo("<option value=" . $user['neptuncode'] . ">". $user['neptuncode'] . "</option>");
                          } 
                          else 
                          {
                            echo("<option selected value=" . $user['neptuncode'] . ">". $user['neptuncode'] . "</option>");
                          }
                        }
                      }
                      else 
                      {
                        foreach($result as $key => $user) {  
                          echo("<option value=" . $user['neptuncode'] . ">". $user['neptuncode'] . "</option>");
                        }
                      }
                     
                    }
                    ?>
                  </select>
                <?php if (isset($errors['user_id'])): ?>
                  <span class="help-block"><?php xecho($errors['user_id']); ?></span>
                <?php endif; ?>
              </div>
			  
			   <div class="form-group <?php xecho(isset($errors['type']) ? 'has-error' : ''); ?>">
         <label class="control-label">Show names</label><br>
			    <?php if ($isEditing === true && isset($show_name) && $show_name == "true"): ?>
                <input type="checkbox" id='show_names' name='show_names' class="form-check-input" value="true" checked>
				<?php else: ?>
				        <input type="checkbox" id='show_names' name='show_names' class="form-check-input" value="true">
				<?php endif; ?>
                <?php if (isset($errors['type'])): ?>
                  <span class="help-block"><?php xecho($errors['type']); ?></span>
                <?php endif; ?>
                 <label for = 'show_names' class="form-check-label"></label>
              </div>

              <div class="form-group">
                <?php echo(getCSRFTokenField() . "\n") ?>
                <?php if ($isEditing === true): ?>
                  <button type="submit" name="update_excellence_list" class="btn btn-success btn-block btn-lg">Update type</button>
                <?php else: ?>
                  <button type="submit" name="create_excellence_list" class="btn btn-info btn-block btn-lg">Create type</button>
                <?php endif; ?>
              </div>
            </form>
          <?php else: ?>
            <h2 class="text-center">No permissions to view category</h2>
          <?php endif; ?>
        </div>
      </div>
    </div>
   
  <?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>
</html>
