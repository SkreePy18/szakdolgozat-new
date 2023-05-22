<!-- closing container div can be found in the footer -->

<div class="container">
  <?php if (isset($_SESSION['user']) && hasPermissionTo('view-dashboard')): ?>
  <nav class="navbar navbar-inverse">
  <?php else: ?>
  <nav class="navbar navbar-default">
  <?php endif; ?>
    <div class="container-fluid">
      <div class="navbar-header">
        <a style="padding-top: 20px" class="top-padding navbar-brand" class="title" href="<?php xecho(BASE_URL . 'excellence/excellenceFilter.php?id=1'); ?>"><?php xecho(APP_NAME); ?></a>        
      </div>
      <ul class="nav navbar-nav navbar-right">
        <?php if (isset($_SESSION['user'])): ?>
          <li><label style="padding-top: 20px" class="top-padding" id="mytimer" class="navbar-text">-00:00:00</label></li>
          <?php echo('<script src="'.BASE_URL.'assets/js/timer.js" type="text/javascript"></script>'); ?>
          <script type="text/javascript">
            topic_start_timer(<?php echo(-$timeout) ?>, 'You have been logged out due to inactivity!', "<?php echo(BASE_URL.'logout.php') ?>" );
          </script>

          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <img src="<?php xecho(BASE_URL); ?>user/uploads/<?php if (isset($_SESSION['user']['profile_picture']) && $_SESSION['user']['profile_picture'] !== "") { xecho($_SESSION['user']['profile_picture']); } else {xecho("default-avatar.png");} ?>" class="profilePicture" width="32" height="32">
              <?php xecho($_SESSION['user']['username'] . ' (' . $_SESSION['user']['role'] . ')'); ?> <span class="caret"></span></a>

              <ul class="dropdown-menu">
               
                <?php if (hasPermissionTo('view-dashboard')): ?>
                  <li><a href="<?php xecho(BASE_URL . 'admin/dashboard.php'); ?>" style="color: red;">Admin Control Panel</a></li>
                <?php endif; ?>
                <!-- User settings -->
                <li><a href="<?php xecho(BASE_URL . 'user/settings.php'); ?>">Profile</a></li>

                <li role="separator" class="divider"></li>
                <li><a href="<?php xecho(BASE_URL . 'excellence/excellenceFilter.php?id=1'); ?>" >Excellence list</a></li>
                


                <!-- Opportunities -->
                <?php if (hasPermissionTo('view-opportunity-list')): ?>
                  <li><a href="<?php xecho(BASE_URL . 'opportunities/opportunityFilter.php'); ?>" >Opportunities</a></li>
                <?php endif; ?>

                <!-- Redeem token -->

                <?php if (hasPermissionTo('view-opportunity-list')): ?>
                  <li><a href="<?php xecho(BASE_URL . 'tokens/redeemToken.php'); ?>" >Redeem token</a></li>
                <?php endif; ?>

                <!-- Logout -->
                <li><a href="<?php xecho(BASE_URL . 'logout.php'); ?>" style="color: red;">Logout</a></li>
              </ul>
          </li>
        <?php else: ?>
          <li><a href="<?php xecho(BASE_URL . 'signup.php') ?>"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
          <li><a href="<?php xecho(BASE_URL . 'login.php') ?>"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
  <?php if (hasPermissionTo('manage-email')): ?>
    <br> 
      <?php $sql = "SELECT * FROM `email`";
        $result = getSingleRecord($sql, '', []);
        if ($result): ?>
          <?php if ($result['apikey'] == NULL || $result['email_from'] == NULL): ?>
      
       <div class="infoBlock"><span class="glyphicon glyphicon-envelope"> </span> Email settings are not fully configured. Please configure <a href="<?php xecho(BASE_URL); ?>admin/email/emailSettings.php">here</a></div>
    <br><br>
    <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php include_once(INCLUDE_PATH . "/layouts/messages.php") ?>

