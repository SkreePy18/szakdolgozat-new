<?php include_once (INCLUDE_PATH . '/logic/validation.php') ?>
<?php include_once (INCLUDE_PATH . '/logic/qrCode.php') ?>
<?php

$opportunity_id = 0;
$opportunity = - 1;
$token = - 1;
$isEditing = false;
$isDeleting = false;
$errors = array();


// ACTION: Fetch data for viewing opportunity
if (isset($_POST['generate_token']))
{
    generateToken();
}

// Action: Click on redeem button
if (isset($_POST['redeem']))
{
    redeemToken();
}

if (isset($_GET['opportunity']))
{
    getTokensOfOpportunity();
}

if (isset($_GET['save_token']))
{
    saveFile();
}

if (isset($_GET['delete_token']))
{
    deleteToken();
}

if (isset($_GET['edit_token']))
{
    editToken();
}

if (isset($_POST['update_token']))
{
    updateToken();
}

if (isset($_GET['send_token']))
{
    sendTokenToRecipent();
}

function getTokens()
{
    global $conn;

    $sql = 'SELECT * FROM tokens';
    $tokens = getMultipleRecords($sql);
    return $tokens;
}

function getTokensOfOpportunity()
{
    global $conn, $tokens, $opportunity;

    $opportunity = filter_input(INPUT_GET, 'opportunity', FILTER_UNSAFE_RAW);
    $sql = 'SELECT * FROM tokens WHERE opportunity_id = ?';
    $tokens = getMultipleRecords($sql, 'i', [$opportunity]);
    return $tokens;
}

function generateToken()
{
    global $conn, $errors;

    $token_data = filter_input_array( INPUT_POST, [
        'user_id' => FILTER_UNSAFE_RAW,
        'token_type' => FILTER_UNSAFE_RAW,
        'owner_id' => FILTER_UNSAFE_RAW,
        'opportunity_id' => FILTER_UNSAFE_RAW,
        'expiration_date' => FILTER_UNSAFE_RAW,
        'login_required' => FILTER_UNSAFE_RAW
    ]);

    $errors = validateToken($token_data, ['generate_token', 'owner_id', 'opportunity_id', 'token_type', 'login_required', 'expiration_date', 'user_id']);

    if (count($errors) === 0) 
    {
        $token_type = $token_data['token_type'];
        $opportunity_id = $token_data['opportunity_id'];
        $expiration_date = $token_data['expiration_date'];
        $user_id = $token_data['user_id'];
        $generated_by = $token_data['owner_id'];
        $login_required = $token_data['login_required'];

        $sql = 'SELECT expiration_date FROM `opportunities` WHERE id = ?';
        $res = getSingleRecord($sql, 'i', [$opportunity_id]);
        $opportunity_expiration_date = $res['expiration_date'];

        if ($expiration_date == NULL)
        {
            // Expiration date is null so we have to get the opportunity's expiration date as default
            $expiration_date = $opportunity_expiration_date;
            $noDate = true;
        }

        if ($user_id == NULL)
        {
            $user_id = 2;
            $no_user = true;
        }

        $exp_date = strtotime($expiration_date);
        $opp_exp_date = strtotime($opportunity_expiration_date);

        if ($exp_date > $opp_exp_date)
        {
            $expiration_date = $opportunity_expiration_date;
            $expOverDue = true;
        }

        if ($login_required == NULL)
        {
            $login_required = "false";
        }

        if (!canGenerateCodeByID($opportunity_id, $user_id, "generate"))
        {
            header("location: " . BASE_URL . "opportunities/opportunityFilter.php");
            exit(0);
        }
        // Token generation - We will generate QR codes by these hexa numbers
        $token = bin2hex(random_bytes(10));

        $sql = "INSERT INTO tokens (token, opportunity_id, user_id, generated_by, expiration_date, login_required) VALUES (?, ?, ?, ?, ?, ?)";
        $result = modifyRecord($sql, 'siiiss', [$token, $opportunity_id, $user_id, $generated_by, $expiration_date, $login_required]);

        if ($result)
        {
            $QRCode = generateQRCode($token, $login_required);
            if ($QRCode)
            {
                $_SESSION['success_msg'] = "Token has been successfully created.";
                if (isset($noDate))
                {
                    $_SESSION['warning_msg'] = "Date has not been selected: Using opportunity's expiration date: " . $expiration_date;
                }
                if (isset($no_user))
                {
                    $_SESSION['warning_msg1'] = "User has not been selected, this token can be redeemed by all users";
                }
                if (isset($expOverDue))
                {
                    $_SESSION['warning_msg2'] = "The date was overdue according to the opportunity expiration date. Expiration date is set to the opportunity's expiration date.";
                }
                $QRCode->saveToFile("../qrCodes/" . $token . ".png");
                header("location: " . BASE_URL . "opportunities/opportunityFilter.php");
                exit(0);
            }
            else
            {
                $_SESSION['error_msg'] = "Fatal error while generating QR code. Contact an Administrator!";
            }
        }
    }
    else
    {
        $_SESSION['error_msg'] = "There was an error while generating token: " . $token_data['user_id'];
    } 
}

function editToken()
{
    global $conn, $token_id, $token, $expiration_date, $opportunity_id, $user_id, $isEditing, $login_required;

    $token_id = filter_input(INPUT_GET, 'edit_token', FILTER_UNSAFE_RAW);
    // Get the token information to fetch to the form
    $sql = "SELECT * FROM `tokens` WHERE id=?";
    $result = getSingleRecord($sql, 'i', [$token_id]);

    if ($result)
    {
        $expiration_date = $result['expiration_date'];
        $opportunity_id = $result['opportunity_id'];
        $token = $result['token'];
        $user_id = $result['user_id'];
        $login_required = $result['login_required'];
        $isEditing = true;

        return true;
    }
}

function updateToken()
{
    $token_data = filter_input_array(INPUT_POST, [
        'token_id' => FILTER_UNSAFE_RAW,
        'opportunity_id' => FILTER_UNSAFE_RAW,
        'user_id' => FILTER_UNSAFE_RAW,
        'expiration_date' => FILTER_UNSAFE_RAW, 
    ]);

    if ($token_data)
    {
        $errors = validateToken($token_data, ['update_token', 'token_id', 'owner_id', 'opportunity_id', 'token_type', 'login_required', 'expiration_date', 'user_id']);
        $token_id = $token_data['token_id'];
        $opportunity_id = $token_data['opportunity_id'];

        if (empty($errors))
        {
            $token_id = $token_data['token_id'];
            $opportunity_id = $token_data['opportunity_id'];
            $user_id = $token_data['user_id'];
            $expiration_date = $token_data['expiration_date'];
            if (!canUpdateObjectByID('token', $token_id))
            {
                $_SESSION['error_msg'] = "You cannot update this token!";
                header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
                exit(0);
            }

            if (!canGenerateCodeByID($opportunity_id, $user_id, "update"))
            {
                header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
                exit(0);
            }

            if (!isset($user_id))
            {
                $user_id = 2;
            }

            $sql = "SELECT expiration_date FROM `opportunities` WHERE id = ?";
            $res = getSingleRecord($sql, 'i', [$opportunity_id]);
            $opportunity_expiration_date = $res['expiration_date'];

            if (strtotime($opportunity_expiration_date) < strtotime($expiration_date))
            {
                $expiration_date = $opportunity_expiration_date;
                $_SESSION['warning_msg2'] = "The date was overdue according to the opportunity expiration date. Expiration date is set to the opportunity's expiration date.";
            }

            $sql = "UPDATE `tokens` SET user_id = ?, expiration_date = ? WHERE id = ?";
            $result = modifyRecord($sql, 'isi', [$user_id, $expiration_date, $token_id]);
            if ($result)
            {
                $_SESSION['success_msg'] = "Token has been successfully updated!";
                header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
                exit(0);
            }
            else
            {
                $_SESSION['error_msg'] = "Could not update token!";
                header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
                exit(0);
            }
        }
        else
        {
            $_SESSION['error_msg'] = "There was an error while updating the token";
        }

    }
}

function deleteToken()
{
    global $conn, $token, $opportunity;

    $token_data = filter_input_array(INPUT_GET, [
          "delete_token"  => FILTER_UNSAFE_RAW, 
          "opportunity"   => FILTER_UNSAFE_RAW]);

    $token = $token_data['delete_token'];
    $opportunity_id = $token_data['opportunity'];

    $result = removeToken($token, $opportunity_id);

    if (!$result)
    {
        $_SESSION['error_msg'] = "You cannot delete this token!";
        header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
        exit(0);
    }
    else
    {
        $_SESSION['success_msg'] = "Token has been successfully deleted!";
        header("location: " . BASE_URL . "tokens/tokenList.php?opportunity=" . $opportunity_id);
        exit(0);
    }
}

function removeToken($token, $opportunity_id)
{
    $sql = "DELETE FROM `tokens` WHERE token = ? AND opportunity_id = ?";
    $result = modifyRecord($sql, 'si', [$token, $opportunity_id]);

    if (!$result)
    {
        return false;
    }
    else
    {
        $file = __DIR__ . "/qrCodes/" . $token . ".png";
        if (file_exists($file))
        {
            unlink($file);
            return true;
        }
    }

    return true;
}

function redeemToken()
{
    $token_data = filter_input_array(INPUT_POST, ["user_id" => FILTER_UNSAFE_RAW, "token" => FILTER_UNSAFE_RAW]);

    $user_id = $token_data["user_id"];
    $token = $token_data["token"];

    if (!canUserRedeemToken($user_id, $token))
    {
        // $_SESSION['error_msg'] = "You cannot redeem this token!";
        header("location: " . BASE_URL . "tokens/redeemToken.php");
        exit(0);
    }

    $sql = "UPDATE tokens SET redeemed='yes', redeemed_by = ? WHERE token = ? AND ( user_id = ? OR user_id = 2 )";
    $result = modifyRecord($sql, 'sii', [$user_id, $token, $user_id]);

    if ($result)
    {
        return insertPoints($user_id, $token);
    }
    else
    {
        $_SESSION['error_msg'] = "You cannot redeem this token!";
        header("location: " . BASE_URL . "tokens/redeemToken.php");
        exit(0);
    }
}

function insertPoints($user_id, $token)
{
    $sql = "SELECT * FROM `tokens` WHERE token = ? AND ( user_id = ? OR user_id = 2 )";
    $result = getSingleRecord($sql, 'si', [$token, $user_id]);
    
    if ($result)
    {
        $sql = "INSERT INTO excellence_points ( opportunity_id, user_id ) VALUES ( ?, ? )";
        $insert = modifyRecord($sql, "ii", [$result['opportunity_id'], $user_id]);
        if ($insert)
        {
            $_SESSION['success_msg'] = "You have successfully redeemed the token!";
            header("location: " . BASE_URL . "tokens/redeemToken.php");
            exit(0);
        }
        else
        {
            $_SESSION['error_msg'] = "You cannot redeem this token!";
            header("location: " . BASE_URL . "tokens/redeemToken.php");
            exit(0);
        }
    }
    else 
    {
        $_SESSION['error_msg'] = "You cannot redeem this token!";
        header("location: " . BASE_URL . "tokens/redeemToken.php");
        exit(0);
    }
}

function saveFile()
{
    // Get parameters
    $token_data = filter_input_array(INPUT_GET, [
        "save_token" => FILTER_UNSAFE_RAW, 
        "opportunity" => FILTER_UNSAFE_RAW
    ]);

    $token = $token_data['save_token'];
    $opportunity_id = $token_data['opportunity'];
    $file = "../qrCodes/" . $token . ".png";

    if (file_exists($file)) 
    {
        header('Content-Type: image/png');
        header("Content-Disposition: attachment; filename=" . $token . ".png");

        // Prevent corrupt files
        while (ob_get_level()) {
            ob_end_clean();
        }

        readfile($file);
        header('Location: ' . BASE_URL . 'tokens/tokenList.php?opportunity=' . $opportunity_id);
        exit; // Terminate the script after saving the file
    }
}

