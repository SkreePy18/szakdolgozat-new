<?php
include_once ("../includes/logic/database.php");

$token = $_GET['token'];

if (isset($token))
{
    $sql = "SELECT * FROM tokens WHERE token=?";
    $result = getSingleRecord($sql, 's', [$token]);
    if ($result)
    {
        $user_id = $result['user_id'];
        $opportunity_id = $result['opportunity_id'];
        $expiration_date = $result['expiration_date'];
        $redeemed = $result['redeemed'];
        $login_required = $result['login_required'];

        if ($login_required == "true")
        {
            header("Location: tokenResponse.php?icon=error&message=This token can be redeemed if you are logged in!");
        }

        $time_expiration = strtotime($expiration_date);
        $curr_date = strtotime(date('y-m-d'));

        if ($curr_date <= $time_expiration)
        {
            if ($user_id !== 2)
            {
                // Check if the user hasn't achieved the opportunity yet
                $sql = "SELECT * FROM excellence_points WHERE opportunity_id = ? AND user_id = ? LIMIT 1";
                $opportunityInstance = getSingleRecord($sql, 'ii', [$opportunity_id, $user_id]);
                if (is_null($opportunityInstance))
                {
                    $sql = "UPDATE tokens SET redeemed='yes' WHERE token=? AND user_id = ?";
                    modifyRecord($sql, 'si', [$token, $user_id]);

                    $sql = "INSERT INTO excellence_points (opportunity_id, user_id) VALUES (?, ?)";
                    modifyRecord($sql, 'ii', [$opportunity_id, $user_id]);
					
                    header("Location: tokenResponse.php?icon=success&message=You have successfully redeemed the token!");
                }
                else
                {
                    header("Location: tokenResponse.php?icon=error&message=This user has already achieved this opportunity!");
                }
            }
            else
            {
                header("Location: tokenResponse.php?icon=error&message=This token cannot be redeemed. ");
            }
        }
        else
        {
            header("Location: tokenResponse.php?icon=error&message=This token is expired!");
        }
    }
    else
    {
        header("Location: tokenResponse.php?icon=error&message=This token does not exist!");
        echo "No such token exits";
    }
}

echo ($token);

?>
