<?php include_once('../config.php'); ?>
<?php include_once(ROOT_PATH . '/csrf.php') ?> 
<?php include_once(ROOT_PATH . '/opportunities/opportunityLogic.php'); ?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php xecho(APP_NAME) ?> - View opportunities</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <!-- Custome styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Bootstrap tooltip -->
    <!-- <script src="../assets/js/tooltip.js"></script> -->
  </head>
  <body>
    <?php include_once(INCLUDE_PATH . "/layouts/navbar.php") ?>

    <?php if ($isDeleting === true): ?>
      <div class="col-md-6 col-md-offset-3">
        <form class="form" action="<?php xecho(removeQueryServer("delete_opportunity"));?>" method="post" enctype="multipart/form-data">
          <input type="hidden" name="opportunity_id" value="<?php xecho($opportunity_id); ?>">
          <p class="text-center">Do you really want to delete Opportunity: '<?php xecho($opportunity); ?>'?</p>
          <div class="form-group text-center">
            <?php echo(getCSRFTokenField() . "\n") ?>
            <button type="submit" name="force_delete_opportunity" class="btn btn-success btn-lg">Delete</button>
            <button type="submit" name="cancel_delete_opportunity" class="btn btn-danger btn-lg">Cancel</button>
          </div>
        </form>
      </div>
    <?php endif; ?>

    <div class="container" style="margin-bottom: 50px;">
      <div class="row">
        <div class="col-md-10 col-md-offset-1">

          <!-- Opportunity creation -->
          <?php if (hasPermissionTo('create-opportunity')): ?>
            <a href="opportunityForm.php" class="btn btn-success">
              <span class="glyphicon glyphicon-plus"></span>
              Create an opportunity
            </a>
  
          <?php endif ?>

          <?php if (isset($_GET["filter_completed"]) || isset($_GET["filter_supervisor"])): ?>
            <a style="float: right" href="opportunityFilter.php" class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>
                 All opportunities
              </a>
          <?php else: ?>
            <a style="float: right" href="opportunityFilter.php?filter_completed=true" class="btn btn-primary">
                <span class="glyphicon glyphicon-ok"></span>
                 My completed opportunities
              </a>
      
          <?php endif; ?>

            <hr>

          <?php if (hasPermissionTo('view-opportunity-list')): ?>

            <?php
              $ncol = hasPermissionTo('update-opportunity') + hasPermissionTo('delete-opportunity');
              $title = "";
              if (isset($_GET["filter_supervisor"])) {
                $owner_id = filter_input(INPUT_GET, 'filter_supervisor', FILTER_UNSAFE_RAW);
                list($opportunities, $title) = getFilterOpportunitiesBySupervisor($owner_id, $opportunity_id);
              } elseif (isset($_GET["filter_completed"])) {
                $my_id = $_SESSION['user']['id'];
                list($opportunities, $title) = getCompletedOpportunities($my_id, $opportunity_id);
              } else{
                $filter_type = "all";
                list($opportunities, $title) = getFilterOpportunities($filter_type, $opportunity_id);
              }
            ?>

            <h1 class="text-center"><?php xecho($title); ?></h1>
            <br />

            <?php if (! empty($opportunities)): ?>
              <table class="table table-striped table-hover">
                <thead>
                  <tr class="hidden-sm hidden-xs">
                    <th width="2%">#</th>
                    <th>Opportunity</th>
                    <th width="10%" class='hidden-sm hidden-xs'>Supervisor</th>
                    <th width="15%" class='hidden-sm hidden-xs'>Type of points</th>
                    <th width="15%">Achievable points</th>
                    <th colspan="5" class="text-center" width="23%">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($opportunities as $key => $value): ?>
                    <?php if ( canViewOpportunityByID($value['id']) || canUpdateObjectByID('opportunity', $value['id'] ) || canDeleteCategoryByID( $value['id'] ) ): ?>
                      <tr>
                        <td class="hidden-sm hidden-xs"><?php xecho($key + 1); ?></td>
                        <?php $url = "opportunities/opportunityView.php?view_opportunity=" ?>
                        <td class="hidden-sm hidden-xs">
                          <a href=<?php echo(BASE_URL . $url . $value['id']); ?> > <?php xecho($value['opportunity']); ?></a><br>
                        </td>
                        <td class='hidden-sm hidden-xs'>
                          <a href="<?php xecho(BASE_URL . 'opportunities/opportunityFilter.php?filter_supervisor=' . $value['owner_id']); ?>" class="btn absoluteCenter">
                            <?php 
                                $sql = "SELECT fullname FROM users WHERE id=? LIMIT 1";
                                $aid = getSingleRecord($sql, 'i', [ $value['owner_id'] ]);
                                xecho($aid['fullname']);
                            ?>
                          </a>
                        </td>

                        <!-- Type of points -->

                        <td class='hidden-sm hidden-xs'>
                        
                          <span class="absoluteCenter">
                            <?php 
                              $sql = "SELECT name FROM opportunity_points_type WHERE id=? LIMIT 1";
                              $result = getSingleRecord($sql, 'i', [ $value['points_type'] ]);
                              xecho($result['name']);
                            ?>
                          </span>
                        </td>

                        <!-- Achievable points -->
                        <td class="hidden-sm hidden-xs">
                          <span class="absoluteCenter"><?php xecho($value['points']); ?> </span>
                        </td>
                        <!-- Action buttons -->

                        

                        <td class="text-center hidden-sm hidden-xs">
                          <a data-toggle="tooltip" title="View opportunity" href="<?php xecho(BASE_URL); ?>opportunities/opportunityView.php?view_opportunity=<?php 
                              xecho($value['id']);
                            
                            ?>" class="btn btn-sm <?php xecho("btn-primary"); ?>">
                            <span class="glyphicon glyphicon-info-sign"></span>
                          </a>
                        </td>

                         <!-- View QR codes -->

                         <?php if (canUpdateOpportunityByID($value['id']) && canGenerateCodeByID( $value['id'] )): ?>
                          <td class="text-center hidden-xs hidden-sm">
                            <a data-toggle="tooltip" title="View tokens" href="<?php xecho(BASE_URL); ?>tokens/tokenList.php?opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-info">
                              <span class="glyphicon glyphicon-eye-open"></span>
                              <!-- <i class='fa fa-eye'> </i>q -->
                            </a>
                          </td>
                        <?php endif ?>


                        <!-- Generate QR code / hexadecimal number -->
                        <?php if (canUpdateOpportunityByID($value['id']) && canGenerateCodeByID( $value['id'] )): ?>
                          <td class="text-center hidden-xs hidden-sm">
                            <a data-toggle="tooltip" title="Generate token" href="<?php xecho(BASE_URL); ?>tokens/codeGenerationForm.php?generate_code=<?php xecho($value['id']); ?>" class="btn btn-sm btn-warning">
                              <span class="glyphicon glyphicon-qrcode"></span>
                            </a>
                          </td>
                        <?php endif ?>

                        <!-- Edit opportunity -->

                        <?php if (canUpdateOpportunityByID( $value['id'] )): ?>
                          <td class="text-center hidden-sm hidden-xs">
                            <a data-toggle="tooltip" title="Edit opportunity" href="<?php xecho(BASE_URL); ?>opportunities/opportunityForm.php?edit_opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-success">
                              <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                          </td>
                        <?php endif ?>

                        <?php if (canUpdateOpportunityByID( $value['id'] )): ?>
                          <td class="text-center hidden-sm hidden-xs">
                            <!-- <a href="<?php xecho(BASE_URL); ?>opportunities/opportunityFilter.php?delete_opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-danger"> -->
                            <a data-toggle="tooltip" title="Delete opportunity" href="<?php xecho(addQueryServer("delete_opportunity", $value['id'])) ?>" class="btn btn-sm btn-danger">
                              <span class="glyphicon glyphicon-trash"></span>
                            </a>
                          </td>
                        <?php endif ?>

                        <!-- Responsive -->

                        <td class="hidden-md hidden-lg">
                          <b>Opportunity:</b><a href=<?php echo(BASE_URL . $url . $value['id']); ?>> <?php xecho($value['opportunity']); ?></a><br>
                          <b>Supervisor:</b>
                            <a href="<?php xecho(BASE_URL . 'opportunities/opportunityFilter.php?filter_supervisor=' . $value['owner_id']); ?>">
                              <?php 
                                $sql = "SELECT fullname FROM users WHERE id=? LIMIT 1";
                                $aid = getSingleRecord($sql, 'i', [ $value['owner_id'] ]);
                                xecho($aid['fullname']);
                            ?>
                          </a><br>
                          <b>Type of points: </b> <?php 
                              $sql = "SELECT name FROM opportunity_points_type WHERE id=? LIMIT 1";
                              $result = getSingleRecord($sql, 'i', [ $value['points_type'] ]);
                              xecho($result['name']);
                            ?><br>
                          <b>Points: </b><?php xecho($value['points']); ?><br>    
                          <a data-toggle="tooltip" title="View opportunity" href="<?php xecho(BASE_URL); ?>opportunities/opportunityView.php?view_opportunity=<?php 
                              xecho($value['id']);
                            
                            ?>" class="btn btn-sm <?php xecho("btn-primary"); ?>">
                            <span class="glyphicon glyphicon-info-sign"></span>
                          </a>
                          <!-- View QR codes -->

                         <?php if (canUpdateOpportunityByID($value['id']) && canGenerateCodeByID( $value['id'] )): ?>
                            <a data-toggle="tooltip" title="View tokens" href="<?php xecho(BASE_URL); ?>tokens/tokenList.php?opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-info">
                              <span class="glyphicon glyphicon-eye-open"></span>
                              <!-- <i class='fa fa-eye'> </i>q -->
                            </a>
                        <?php endif ?>


                        <!-- Generate QR code / hexadecimal number -->
                        <?php if (canUpdateOpportunityByID($value['id']) && canGenerateCodeByID( $value['id'] )): ?>
                            <a data-toggle="tooltip" title="Generate token" href="<?php xecho(BASE_URL); ?>tokens/codeGenerationForm.php?generate_code=<?php xecho($value['id']); ?>" class="btn btn-sm btn-warning">
                              <span class="glyphicon glyphicon-qrcode"></span>
                            </a>
                        <?php endif ?>

                        <!-- Edit opportunity -->

                        <?php if (canUpdateOpportunityByID( $value['id'] )): ?>
                            <a data-toggle="tooltip" title="Edit opportunity" href="<?php xecho(BASE_URL); ?>opportunities/opportunityForm.php?edit_opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-success">
                              <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                        <?php endif ?>

                        <?php if (canUpdateOpportunityByID( $value['id'] )): ?>
                            <!-- <a href="<?php xecho(BASE_URL); ?>opportunities/opportunityFilter.php?delete_opportunity=<?php xecho($value['id']); ?>" class="btn btn-sm btn-danger"> -->
                            <a data-toggle="tooltip" title="Delete opportunity" href="<?php xecho(addQueryServer("delete_opportunity", $value['id'])) ?>" class="btn btn-sm btn-danger">
                              <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        <?php endif ?>     


                        </td>

                                      
                      </tr>
                    <?php endif ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <h2 class="text-center">No opportunities</h2>
            <?php endif; ?>
          <?php else: ?>
            <h2 class="text-center">No permissions to view opportunity list</h2>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php include_once(INCLUDE_PATH . "/layouts/footer.php") ?>