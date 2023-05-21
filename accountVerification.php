<?php 
    include_once("config.php");

    $token = $_GET["token"];

    if (isset($token)) 
    {
        $sql = "SELECT * FROM account_verification INNER JOIN `users` ON `account_verification`.`user_id` = `users`.`id` WHERE token=?";
        $result = getSingleRecord($sql, 's', [$token]);

        if (! empty($result)) 
        {
            $email = $result['email'];

            $sql = "UPDATE users SET pending_verification=0 WHERE email=?";
            $result = modifyRecord($sql, 's', [$email]);

            $sql = "DELETE FROM `account_verification` WHERE token=?";
            $result = modifyRecord($sql, 's', [$token]);

            $_SESSION['success_msg'] = "Account has been successfully verified";
            header("Location: " . BASE_URL . "login.php");
            exit(0);
        }
        else 
        {
            $_SESSION['error_msg'] = "Invalid token";
        }
    }

?>