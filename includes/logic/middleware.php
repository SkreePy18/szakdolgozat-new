<?php
// except for these pages, check for logged in user
if (!in_array(basename($_SERVER['PHP_SELF']) , ['login.php', 'signup.php', 'excellenceFilter.php', 'passwordReset.php', 'passwordResetForm.php', 'accountVerification.php']))
{
    // if user is NOT logged in, redirect them to login page
    if (!isset($_SESSION['user']))
    {
        header("location: " . BASE_URL . "excellence/excellenceFilter.php?id=1");
        exit(0);
    }
}

// if user is logged in and the role is empty, redirect them to landing page
if (isset($_SESSION['user']) && is_null($_SESSION['user']['role']))
{
    header("location: " . BASE_URL);
    exit(0);
}

// from _SERVER information assemble the URL + Query string
function keepQueryServer()
{
    if (array_key_exists('QUERY_STRING', $_SERVER))
    {
        $query = $_SERVER['QUERY_STRING'];
    }
    else
    {
        $query = '';
    }
    parse_str($query, $query_array);
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (count($query_array) == 0)
    {
        return ($url);
    }
    else
    {
        $new_query = http_build_query($query_array);

        return (htmlspecialchars($url) . "?" . $new_query);
    }
}

// add a new query string to the URL
function addQueryServer($key, $value)
{
    global $_SERVER;

    if (array_key_exists('QUERY_STRING', $_SERVER))
    {
        $query = $_SERVER['QUERY_STRING'];
    }
    else
    {
        $query = '';
    }
    parse_str($query, $query_array);
    $query_array[$key] = $value;

    $new_query = http_build_query($query_array);
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    return (htmlspecialchars($url) . "?" . $new_query);
}

// delete a query string from the URL
function removeQueryServer($key)
{
    global $_SERVER;

    if (array_key_exists('QUERY_STRING', $_SERVER))
    {
        $query = $_SERVER['QUERY_STRING'];
    }
    else
    {
        $query = '';
    }
    parse_str($query, $query_array);
    unset($query_array[$key]);
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (count($query_array) == 0)
    {
        return ($url);
    }
    else
    {
        $new_query = http_build_query($query_array);

        return (htmlspecialchars($url) . "?" . $new_query);
    }
}

// Accept a user ID and returns true if user is admin and false otherwise
function isAdmin($user_id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE id=? AND role_id=1 LIMIT 1";
    $user = getSingleRecord($sql, 'i', [$user_id]); // get single user from database
    if (!empty($user))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function getSupervisorRoleID()
{
    return 4;
}

function getStudentRoleID()
{
    return 3;
}

function getGuestRoleID()
{
    return 2;
}

function hasPermissionTo($permisson_code)
{
    if (isset($_SESSION['userPermissions']) && in_array(['permission_name' => $permisson_code], $_SESSION['userPermissions']))
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Update object by ID
function canUpdateObjectByID($object_type, $object_id = null)
{
    global $conn, $database_by_object;
    $permission = 'update-' . $object_type;

    // Special checks
    if ($object_type == "user")
    {
        if ($object_id == $_SESSION['user']['id'])
        {
            return true;
        }
    }
    elseif ($object_type == "semester")
    {
        if ($object_id == 1)
        {
            return false;
        }
    }

    // Global, applies on every update
    if (in_array(['permission_name' => $permission], $_SESSION['userPermissions']) || isAdmin($_SESSION['user']['id']))
    {
        if (array_key_exists($object_type, $database_by_object))
        {
            $table = $database_by_object[$object_type];
            if (!$table)
            {
                return false;
            }
            $sql = "SELECT id FROM $table WHERE id = ?";
            $cat_results = getSingleRecord($sql, 'i', [$object_id]);
            if (is_null($cat_results))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function canDeleteUserByID($user_id)
{
    if (in_array(['permission_name' => 'delete-user'], $_SESSION['userPermissions']))
    {

        // if current user is equal to the user to delete, do not allow it
        if ($user_id == $_SESSION['user']['id'])
        {
            $_SESSION['error_msg'] = "You cannot delete yourself";
            return false;
        }

        // check whether user exists at all
        $sql = "SELECT * FROM users WHERE id=?";
        $user_result = getSingleRecord($sql, 'i', [$user_id]);
        if (is_null($user_result))
        {
            $_SESSION['error_msg'] = "User does not exist to delete it";
            return false;
        }

        return true;
    }
    else
    {
        $_SESSION['error_msg'] = "No permissions to delete role";
        return false;
    }
}

function canDeleteRoleByID($role_id = NULL)
{
    if (in_array(['permission_name' => 'delete-role'], $_SESSION['userPermissions']))
    {
        // check whether role exists at all
        $sql = "SELECT * FROM roles WHERE id=?";
        $role_result = getSingleRecord($sql, 'i', [$role_id]);
        if (is_null($role_result))
        {
            $_SESSION['error_msg'] = "Role does not exist to delete it";
            return false;
        }

        // check whether role is assigned to any user
        $sql = "SELECT id FROM users WHERE role_id=?";
        $role_result = getMultipleRecords($sql, 'i', [$role_id]);
        if (count($role_result) > 0)
        {
            $_SESSION['error_msg'] = "Cannot delete role, a user belongs to it";
            return false;
        }

        return true;
    }
    else
    {
        $_SESSION['error_msg'] = "No permissions to delete role";
        return false;
    }
}

function canAssignRolePermissionsByID($role_id = NULL)
{
    if (in_array(['permission_name' => 'assign-role-permission'], $_SESSION['userPermissions']))
    {
        // check whether role exists at all
        $sql = "SELECT * FROM roles WHERE id=?";
        $role_result = getSingleRecord($sql, 'i', [$role_id]);
        if (is_null($role_result))
        {
            return false;
        }

        return true;
    }
    else
    {
        return false;
    }
}

// Opportunity related
function canDeleteOpportunityByID($opportunity_id = null)
{
    global $conn;
    if (in_array(['permission_name' => 'delete-opportunity'], $_SESSION['userPermissions']))
    {
        // Check if opportunity exists
        $sql = "SELECT id FROM `opportunities` WHERE id = ?";
        $result = getSingleRecord($sql, 'i', [$opportunity_id]);
        if (is_null($result))
        {
            $_SESSION['error_msg'] = "Opportunity does not exist to delete it. ID: '" . $opportunity_id . "'";
            return false;
        }

        return true;
    }
    else
    {
        // $_SESSION['error_msg'] = "No permissions to delete the opportunity";
        return false;
    }
}

function canViewOpportunityByID($opportunity_id)
{
    if (in_array(['permission_name' => 'view-opportunity-list'], $_SESSION['userPermissions']))
    {

        // Check if opportunity exists
        $sql = "SELECT * from opportunities WHERE id=?";
        $opportunities = getSingleRecord($sql, 'i', [$opportunity_id]);

        if (is_null($opportunities))
        {
            return false;
        }
        // admin role can view everything
        if (isAdmin($_SESSION['user']['id']))
        {
            return true;
        }

        return true;
    }
    else
    {
        return false;
    }
}

function canGenerateCodeByID($opportunity_id, $user_id = NULL, $type = NULL)
{
    if (in_array(['permission_name' => 'generate-token'], $_SESSION['userPermissions']))
    {
        // Check if opportunity exists
        if (is_null($type))
        {
            $type = "generate";
        }
        $sql = "SELECT * from opportunities WHERE id=?";
        $opportunities = getSingleRecord($sql, 'i', [$opportunity_id]);

        if (is_null($opportunities))
        {
            $_SESSION['error_msg'] = "Could not " . $type . " token. Cause: Opportunity doesn't exist.";
            return false;
        }

        if ($user_id == 2)
        {
            return true;
        }

        if ($user_id != NULL)
        {
            // Check whether the user has a token already generated or achieved the point
            $sql = "SELECT id FROM tokens WHERE user_id = ? AND opportunity_id = ?";
            $tokenResults = getSingleRecord($sql, 'ii', [$user_id, $opportunity_id]);
            if (!is_null($tokenResults))
            {
                $_SESSION['error_msg'] = "Could not " . $type . " token. Cause: A token already exists for this user on this opportunity.";
                return false;
            }

            $sql = "SELECT id FROM excellence_points WHERE user_id = ? AND opportunity_id = ?";
            $pointResults = getSingleRecord($sql, 'ii', [$user_id, $opportunity_id]);
            if (!is_null($pointResults))
            {
                $_SESSION['error_msg'] = "Could not " . $type . " token. Cause: This user has already achieved this opportunity.";
                return false;
            }
        }

        return true;
    }
    else
    {
        $_SESSION['error_msg'] = "No permission to " . type . " token.";
        return false;
    }
}

function canImportPointsForOpportunityByID($opportunity_id, $user_id)
{
    if (in_array(['permission_name' => 'generate-token'], $_SESSION['userPermissions']))
    {
        $sql = "SELECT id FROM `excellence_points` WHERE user_id = ? AND opportunity_id = ?";
        $result = getSingleRecord($sql, 'ii', [$user_id, $opportunity_id]);
        if (!is_null($result))
        {
            return false;
        }

        return true;
    }
    else
    {
        return false;
    }
}

function canUserRedeemToken($user_id, $token)
{
    // Check if opportunity exists
    $sql = "SELECT * from tokens WHERE token=? AND (user_id = ? OR user_id = 2)";
    $tokenInsance = getSingleRecord($sql, 'si', [$token, $user_id]);

    if (is_null($tokenInsance))
    {
        $_SESSION['error_msg'] = "This token doesn't exist for you!";
        return false;
    }
    $token_user_id = $tokenInsance['user_id'];

    if (is_null($tokenInsance))
    {
        if ($token_user_id !== 2 && $token_user_id !== $user_id)
        {
            $_SESSION['error_msg'] = "This token doesn't exist for you!";
            return false;
        }
    }

    if ($tokenInsance["redeemed"] !== "no")
    {
        $_SESSION['error_msg'] = "This token was already redeemed!";
        return false;
    }

    // Check date data
    $expiration_date = strtotime($tokenInsance['expiration_date']);
    $date = strtotime(date('y-m-d'));

    if ($expiration_date < $date)
    {
        $_SESSION['error_msg'] = "This token is expired!";
        return false;
    }

    // Check if achieved already
    $opportunity_id = $tokenInsance['opportunity_id'];
    $sql = "SELECT id FROM excellence_points WHERE opportunity_id = ? AND user_id = ?";
    $result = getSingleRecord($sql, 'ii', [$opportunity_id, $user_id]);

    if (!is_null($result))
    {
        $_SESSION['error_msg'] = "You have achieved the opportunity for this token so you cannot redeem it!";
        return false;
    }

    return true;
}

// Type of points
// checks if logged in user can update semester
function canViewTypeByID($type_id = null)
{
    global $conn;

    if (in_array(['permission_name' => 'update-semester'], $_SESSION['userPermissions']))
    {
        // check whether semester exists at all
        $sql = "SELECT id FROM semesters WHERE id=?";
        $semester_result = getSingleRecord($sql, 'i', [$type_id]);
        if (is_null($semester_result))
        {
            return false;
        }

        // we are not allowed to update the current semester (id == 1)
        if ($type_id > 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function canDeleteTypeByID($semester_id = null)
{
    global $conn;

    if (in_array(['permission_name' => 'delete-point-type'], $_SESSION['userPermissions']))
    {
        // check whether semester exists at all
        $sql = "SELECT id FROM opportunity_points_type WHERE id=?";
        $semester_result = getSingleRecord($sql, 'i', [$semester_id]);
        if (is_null($semester_result))
        {
            $_SESSION['error_msg'] = "Point type does not exist";
            return false;
        }

        return true;
    }
    else
    {
        $_SESSION['error_msg'] = "No permissions to delete point type";
        return false;
    }
}

function canDeleteExcellenceByID($excellence_id = null)
{
    global $conn;

    if (in_array(['permission_name' => 'delete-excellence-list'], $_SESSION['userPermissions']))
    {
        // check whether semester exists at all
        $sql = "SELECT id FROM excellence_lists WHERE id=?";
        $excellence_result = getSingleRecord($sql, 'i', [$excellence_id]);
        if (is_null($excellence_result))
        {
            $_SESSION['error_msg'] = "Excellence list does not exist to delete it";
            return false;
        }

        return true;
    }
    else
    {
        $_SESSION['error_msg'] = "No permissions to delete excellence list";
        return false;
    }
}

function canUpdateOpportunityByID($object_id)
{
    global $conn;

    if (in_array(['permission_name' => 'update-opportunity'], $_SESSION['userPermissions']))
    {
        $sql = "SELECT id, owner_id FROM `opportunities` WHERE id = ?";
        $result = getSingleRecord($sql, 'i', [$object_id]);

        if (is_null($result))
        {
            return false;
        }

        if (isAdmin($_SESSION['user']['id']))
        {
            return true;
        }

        if ($_SESSION['user']['id'] != $result['owner_id'])
        {
            return false;
        }

        return true;
    }
}

