<?php 
include_once("../includes/logic/validation.php");
$isEditing = false;
$errors = array();

if (isset($_POST["edit_profile"])) {
    $isEditing = true;
}

if (isset($_POST["save_profile"])) {
    saveProfile();
}


function saveProfile() 
{
    global $isEditing, $errors;

    $user_data = filter_input_array(INPUT_POST, [
        "username"          => FILTER_UNSAFE_RAW,
        "profile_picture"   => FILTER_UNSAFE_RAW,
        "fullname"          => FILTER_UNSAFE_RAW,
        "neptuncode"        => FILTER_UNSAFE_RAW,
        "email"             => FILTER_UNSAFE_RAW,
        "send_email"        => FILTER_UNSAFE_RAW
    ]);

    $neptun = sanitize_string($user_data["neptuncode"]);
    $fullname = sanitize_string($user_data["fullname"]);
    $profile_picture = isset($_FILES["profile_picture"]["name"]) ? $_FILES["profile_picture"]["name"] : NULL;
    $send_email = ($user_data["send_email"]) == true ? 1 : 0;
    $email = sanitize_string($user_data["email"]);
    $errors = validateUser($user_data, ["save_profile", "profile_picture", "username", "send_email"]);

    if (empty($errors)) {
        if ($profile_picture != NULL) {
            $tmpFile = $_FILES["profile_picture"]["tmp_name"];
            $ext = pathinfo($profile_picture, PATHINFO_EXTENSION);
            if ($ext !== "png" && $ext !== "jpg" && $ext !== "jpeg" && $ext !== "svg") {
                $_SESSION["error_msg"] = "Only image files are allowed! " . $ext;
                header("Location: " . BASE_URL . "/user/settings.php");
                exit(0);
            }
            $folder = "uploads/" . $_SESSION['user']['id'] . "." . $ext;
            move_uploaded_file($tmpFile, $folder);
    
            $sql = "UPDATE `users` SET neptuncode=?, fullname=?, email=?, send_email=?, profile_picture=? WHERE id=?";
            modifyRecord($sql, 'sssssi', [$neptun, $fullname, $email, $send_email, $_SESSION["user"]["id"] . "." . $ext, $_SESSION["user"]["id"]]);
    
            $_SESSION["user"]["profile_picture"] = $_SESSION["user"]["id"] . "." . $ext;
        } else {
            $sql = "UPDATE `users` SET neptuncode=?, fullname=?, email=?, send_email=? WHERE id=?";
            modifyRecord($sql, 'ssssi', [$neptun, $fullname, $email, $send_email, $_SESSION["user"]["id"]]);
        }

        $_SESSION["success_msg"] = "Profile has been successfully updated";

    } else {
        $_SESSION["error_msg"] = "There was an error while updating user";
    }

    $isEditing = false;
   
       
}


?>
