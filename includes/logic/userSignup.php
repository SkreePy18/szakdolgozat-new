<?php include_once (INCLUDE_PATH . '/logic/validation.php'); ?>
<?php include_once (ROOT_PATH . "/admin/email/sendMail.php"); ?>
<?php
// variable declaration
$username = "";
$fullname = "";
$neptuncode = "";
$email = "";
$errors = [];

function loginById($user_id)
{
    global $conn;
    $sql = "SELECT u.id, u.role_id, u.username, u.profile_picture, r.name as role FROM users u LEFT JOIN roles r ON u.role_id=r.id WHERE u.id=? LIMIT 1";
    $user = getSingleRecord($sql, 'i', [$user_id]);

    if (!empty($user))
    {
        // put logged in user into session array
        $_SESSION['user'] = $user;
        $_SESSION['success_msg'] = "You are now logged in";
        // determine permissions for the user
        $permissionsSql = "SELECT p.name as permission_name FROM permissions as p
                          JOIN permission_role as pr ON p.id=pr.permission_id
                          WHERE pr.role_id=?";
        $userPermissions = getMultipleRecords($permissionsSql, "i", [$user['role_id']]);
        $_SESSION['userPermissions'] = $userPermissions;
        // redirect to homepage
        header('location: ' . BASE_URL . 'index.php');
        exit(0);
    }
}

// SIGN UP USER
if (isset($_POST['signup_btn']))
{
    $user_data = filter_input_array(INPUT_POST, [
        "username" => FILTER_UNSAFE_RAW, 
        "fullname" => FILTER_UNSAFE_RAW, 
        "neptuncode" => FILTER_UNSAFE_RAW, 
        "email" => FILTER_UNSAFE_RAW, 
        "password" => FILTER_UNSAFE_RAW, 
        "passwordConf" => FILTER_UNSAFE_RAW]);
    // receive all input values from the form. No need to escape... bind_param takes care of escaping
    $username   = sanitize_string($user_data['username']);
    $fullname   = sanitize_string($user_data['fullname']);
    $neptuncode = sanitize_string($user_data['neptuncode']);
    $email      = sanitize_string($user_data['email']);

    // validate form values
    $errors = validateUser($user_data, ['signup_btn']);

    $password = password_hash($user_data['password'], PASSWORD_DEFAULT); //encrypt the password before saving in the database
    // if no errors, proceed with signup
    if (count($errors) === 0)
    {
        // insert user into database
        $query = "INSERT INTO users SET role_id=2, pending_verification=1, username=?, fullname=?, neptuncode=?, email=?, password=?, created_at=CURRENT_TIMESTAMP()";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $username, $fullname, $neptuncode, $email, $password);
        $result = $stmt->execute();
        if ($result)
        {
            $user_id = $stmt->insert_id;
            $stmt->close();

            $token = bin2hex(random_bytes(25));
            $sql = "INSERT INTO account_verification (`user_id`, `token`) VALUES (?, ?)";
            $handler = modifyRecord($sql, 'ss', [$user_id, $token]);

            $content = "
            Dear " . $fullname . "<br><br>
            
            Please verify your account with the following link: <br>" . BASE_URL . "accountVerification.php?token=" . $token . "
            <br><br>Best regards,<br>" . APP_NAME;

            sendEmail($email, "Account verification", $content);
            // loginById($user_id); // log user in
            $_SESSION['success_msg'] = "Account successfully registered! Please confirm your account via the link sent in Email";

        }
        else
        {
            $_SESSION['error_msg'] = "Could not register user data";
        }
    }
    else
    {
        $_SESSION['error_msg'] = "Could not register user";
    }
}

// USER LOGIN
if (isset($_POST['login_btn']))
{
    $user_data = filter_input_array(INPUT_POST, ["username" => FILTER_UNSAFE_RAW, "password" => FILTER_UNSAFE_RAW, ]);
    $username = $user_data['username'];
    $password = $user_data['password']; // don't escape passwords.
    // validate form values
    $errors = validateUser($user_data, ['login_btn']);

    if (empty($errors))
    {
        $sql = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
        $user = getSingleRecord($sql, 'ss', [$username, $username]);
        if (!empty($user))
        { // if user was found
            if (password_verify($password, $user['password']))
            { // if password matches
                if ($user['pending_verification'] == 1)
                {
                    $_SESSION['error_msg'] = "You have to verify your email before proceeding";
                }
                else
                {
                    // log user in
                    loginById($user['id']);
                }
            }
            else
            { // if password does not match
                $_SESSION['error_msg'] = "Wrong credentials";
            }
        }
        else
        { // if no user found
            $_SESSION['error_msg'] = "Wrong credentials";
        }
    }
}

if (isset($_POST["request_reset"]))
{
    $user_data = filter_input_array(INPUT_POST, ["username" => FILTER_UNSAFE_RAW]);

    $username = $user_data["username"];

    $sql = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
    $user = getSingleRecord($sql, 'ss', [$username, $username]);

    if (!empty($user))
    {
        $user_id = $user['id'];
        $token = bin2hex(random_bytes(10));
        $sql = "INSERT INTO password_resets (`user_id`, `token`) VALUES (?, ?)";
        $handler = modifyRecord($sql, 'ss', [$user_id, $token]);

        $content = "Dear <strong> " . $user['fullname'] . "</strong><br><br>
        You have requested a password reset. In order to change your password, please click on this link:<br><br>
        " . BASE_URL . "/passwordResetForm.php?password_token=" . $token . "<br><br>Best regards,<br>SkreePy<br><hr>";

        sendEmail($user['email'], "Password reset", $content);

        $_SESSION["success_msg"] = "Password reset email has been sent";
    }
    else
    {
        $_SESSION['error_msg'] = "No user has been found correspondingly to this email";
    }
}

if (isset($_POST["reset_password"]))
{
    global $conn;

    $user_data = filter_input_array(INPUT_POST, ["new_password" => FILTER_UNSAFE_RAW, "new_password_confirm" => FILTER_UNSAFE_RAW, "password_token" => FILTER_UNSAFE_RAW]);

    $errors = validateUser($user_data, ["password_token"]);

    if (empty($errors))
    {
        // Get email from the token
        $token = $user_data['password_token'];
        $sql = "SELECT * FROM password_resets INNER JOIN `users` ON `user_id` = `users`.`id` WHERE token = ? LIMIT 1";
        $email_result = getSingleRecord($sql, 's', [$token]);

        if (!empty($email_result))
        {
            $user_id = $email_result['id'];
            $password = password_hash(htmlspecialchars($user_data['new_password']) , PASSWORD_DEFAULT);

            $query = "UPDATE users SET password = ? WHERE id = ?";
            modifyRecord($query, "ss", [$password, $user_id]);

            $deleteToken = "DELETE FROM password_resets WHERE token = ?";
            modifyRecord($deleteToken, "s", [$token]);

            header('location: ' . BASE_URL . 'index.php');
            $_SESSION["success_msg"] = "Password has been successfully reset";
        }
        else
        {
            $_SESSION['error_msg'] = "Error when changing password. No email found";
        }

    }
    else
    {
        $_SESSION['error_msg'] = "Error when changing password";
    }

}

