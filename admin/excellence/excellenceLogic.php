<?php include_once(INCLUDE_PATH . '/logic/validation.php') ?>
<?php
  // variable declaration. These variables will be used in the category form
  $name = "";
  $excellence_id = -1;
  $isEditing = false;
  $isDeleting = false;
  $errors = array();
  $show_name = "false";

  // ACTION: Save 
  if (isset($_POST['create_excellence_list'])) {  // if user clicked save button ...
    createExcellence();
  }
  // ACTION: Update 
  if (isset($_POST['update_excellence_list'])) { // if user clicked update button ...
    updateExcellence();
  }
  // ACTION: Fetch for editing
  if (isset($_GET["edit_excellence_list"])) {
    editExcellence();
  }
  // ACTION: Delete with confirmation
  if (isset($_GET['delete_excellence_list'])) {
    deleteExcellence();
  }
  // ACTION: Force delete
  if (isset($_POST['force_delete_excellence_list'])) {
    forceDeleteExcellence();
  }

  function getExcellenceList(){
    global $conn;
    $sql = "SELECT * FROM excellence_lists";
    $roles = getMultipleRecords($sql);
    return $roles;
  }

  // Save category to database
  function createExcellence(){
    global $conn, $errors, $name, $isEditing;

    // validate data
    $type_data = filter_input_array(INPUT_POST, [
                       "name"           => FILTER_UNSAFE_RAW,
                       "choose_points"  => FILTER_UNSAFE_RAW,
                      //  "point_types"    => FILTER_UNSAFE_RAW,
                       "choose_users"   => FILTER_UNSAFE_RAW,
                      //  "students"       => FILTER_UNSAFE_RAW,
                       "created_by"     => FILTER_UNSAFE_RAW,
					              "show_names"		=> FILTER_UNSAFE_RAW
                     ]);

    // receive all input values from the form
    $name             = $type_data['name'];
    $bPointsSelected  = $type_data['choose_points'];
    // $point_types      = $type_data['point_types'];
    $bUserSelected    = $type_data['choose_users'];
    // $users            = $type_data['students'];
    $created_by       = $type_data['created_by'];
	  $show_names		  = $type_data['show_names'];

    //Get array values and encode in json
    if(isset($_POST['point_types'])) {
      $point_types = json_encode($_POST['point_types']);
    } else {
      $point_types = "all";
    }

    if(isset($_POST['students'])) {
      $users = json_encode($_POST['students']);
    } else {
      $users = "all";
    }

    if (! hasPermissionTo('manage-excellence-list')) {
      $_SESSION['error_msg'] = "No permissions to create excellence-list";
      header("location: " . BASE_URL . "admin/points/pointsList.php");
      exit(0);
    }

	if (! isset($show_names)) {
		$show_names = "false";
	}

    $sql = "INSERT INTO `excellence_lists` (name, users, points_type, created_by, show_name) VALUES (?, ?, ?, ?, ?)";
    $result = modifyRecord($sql, 'sssis', [$name, $users, $point_types, $created_by, $show_names]);

     if($result){
        $_SESSION['success_msg'] = "Excellence list created successfully";
        header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
        exit(0);
      } else {
        $_SESSION['error_msg'] = "Could not create excellence list";
      }
  }

  function updateExcellence() {
    global $conn, $errors, $excellence_id, $name, $isEditing;

    // validate data
    $excellence_data = filter_input_array(INPUT_POST, [
                  "excellence_id" => FILTER_UNSAFE_RAW,
                  "name" => FILTER_UNSAFE_RAW,
                  "show_names" => FILTER_UNSAFE_RAW,
                  "choose_users" => FILTER_UNSAFE_RAW,
                  "choose_points" => FILTER_UNSAFE_RAW
                 ]);

    // receive all input values from the form
    $excellence_id  = $excellence_data['excellence_id'];
    $type           = $excellence_data['name'];
    $show_names     = $excellence_data["show_names"];
    $choose_users   = $excellence_data["choose_users"];
    $choose_points  = $excellence_data["choose_points"];

    // check permission to update the category data
    if (! canUpdateObjectByID('excellence-list', $excellence_id )) {
      $_SESSION['error_msg'] = "No permissions to update excellence list!";
      header("location: " . BASE_URL . "admin/points/excellenceList.php");
      exit(0);
    }

    if(isset($_POST['point_types'])) {
      $point_type = json_encode($_POST['point_types']);
    } else {
      $point_type = "all";
    }

    if (! isset($choose_points)) 
    {
        $point_type = "all";
    }

    if(isset($_POST['students'])) {
      $users = json_encode($_POST['students']);
    } else {
      $users = "all";
      echo("ALL");
    }

    if (! isset($choose_users)) 
    {
        $users = "all";
    }


    if (! isset($show_names)) {
      $show_names = "false";
    }

    // $errors = validateExcellence($excellence_data, ["show_names", "choose_users", "choose_points"]);

    // if (count($errors) === 0) {
      $sql = "UPDATE excellence_lists SET name=?, show_name=?, points_type=?, users=? WHERE id=?";
      $result = modifyRecord($sql, 'ssssi', [$type, $show_names, $point_type, $users, $excellence_id]);
      if ($result) {
        $_SESSION['success_msg'] = "Excellence list has been successfully updated!";
        if(hasPermissionTo('manage-excellence-list')) {
          header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
        } else {
          header("location: " . BASE_URL . "index.php");
        } 
        exit(0);
      } else {
        $_SESSION['error_msg'] = "Could not update excellence list";
      }
    // } else {
      // $_SESSION['error_msg'] = "Could not update excellence list";
      // header("location: " . BASE_URL . "admin/excellence/excellenceForm.php?edit_excellence_list=$excellence_id");
     
    // }
    $isEditing = true;
  }

  function editExcellence(){
    global $conn, $excellence_id, $name, $show_name, $type_of_points, $selected_users, $isEditing;

    $excellence_id = filter_input(INPUT_GET, 'edit_excellence_list', FILTER_UNSAFE_RAW);
    
    if (! canUpdateObjectByID('excellence-list', $excellence_id)) {
      $_SESSION['error_msg'] = "No permissions to edit excellence list!";
      header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
      exit(0);
    }


    $sql = "SELECT * FROM excellence_lists WHERE id=?";
    $type_data = getSingleRecord($sql, 'i', [$excellence_id]);

    $excellence_id  = $type_data['id'];
    $name           = $type_data['name'];
	  $show_name      = $type_data['show_name'];
	  $type_of_points     = $type_data['points_type'];
	  $selected_users          = $type_data['users'];

	
		$selected_users = ($selected_users != "all") ? json_decode($selected_users) : "all";
	  $type_of_points = ($type_of_points != "all") ? json_decode($type_of_points) : "all";

    $isEditing = true;
  }


  function deleteExcellence() {
    global $conn, $excellence_id, $name, $isDeleting;

    $excellence_id = filter_input(INPUT_GET, 'delete_excellence_list', FILTER_UNSAFE_RAW);

    if (! canDeleteExcellenceByID( $excellence_id )) {
      header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
      exit(0);
    }

    $sql = "SELECT * FROM excellence_lists WHERE id=?";
    $type_data = getSingleRecord($sql, 'i', [$excellence_id]);

    $name = $type_data['name'];
    $isDeleting = true;
  }

  function forceDeleteExcellence() {
    global $conn, $excellence_id;

    $excellence_id = filter_input(INPUT_POST, 'excellence_id', FILTER_UNSAFE_RAW);
    if (! canDeleteExcellenceByID( $excellence_id )) {
      header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
      exit(0);
    }

    $sql = "DELETE FROM excellence_lists WHERE id=?";
    $result = modifyRecord($sql, 'i', [$excellence_id]);

    if ($result) {
      $_SESSION['success_msg'] = "Excellence list has been deleted!!";
      header("location: " . BASE_URL . "admin/excellence/excellenceList.php");
      exit(0);
    } else {
      $_SESSION['error_msg'] = "Could not delete this excellence list";
    }
  }

