<?php include_once(INCLUDE_PATH . '/logic/validation.php') ?>

<?php
    if (isset($_POST['edit_email'])) 
    {
        editEmail();
    }

    function editEmail()
    {
        global $conn;

        $email_data = filter_input_array(INPUT_POST, [
                "apikey"        => FILTER_UNSAFE_RAW,
                "email_from"    => FILTER_UNSAFE_RAW
        ]);

        $apikey     = $email_data["apikey"];
        $email_from = $email_data["email_from"];

        if (hasPermissionTo("manage-email")) 
        {
            $sql = "UPDATE email SET apikey=?, email_from=?";
            $result = modifyRecord($sql, 'ss', [$apikey, $email_from]);

            if ($result) 
            {
                $_SESSION['success_msg'] = "Email settins successfully updated!";
            } 
            else 
            {
                $_SESSION['error_msg'] = "Failed to upload email settings";
            }
        }
    }

    function getEmailSettings()
    {
        global $apikey, $email_from;
        $sql = "SELECT * FROM `email` LIMIT 1";
        $result = getSingleRecord($sql);

        if ($result) 
        {
            $apikey = $result["apikey"];
            $email_from = $result["email_from"];
        }
    }
?>