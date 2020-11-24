<?php
include("includes/connection.php");
include("includes/function.php");
include("language/app_language.php");
include("smtp_email.php");
include("src/Twilio/autoload.php");

use Twilio\Rest\Client;

error_reporting(0);
/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/

date_default_timezone_set("Asia/Kolkata");

$file_path = getBaseUrl();
define("PACKAGE_NAME", $settings_details['package_name']);
define("HOME_LIMIT", $settings_details['api_home_limit']);
define("API_PAGE_LIMIT", $settings_details['api_page_limit']);

function get_user_status($user_id)
{
    global $mysqli;

    $user_qry = "SELECT * FROM tbl_users where id='" . $user_id . "'";
    $user_result = mysqli_query($mysqli, $user_qry);
    $user_row = mysqli_fetch_assoc($user_result);

    if (mysqli_num_rows($user_result) > 0) {
        if ($user_row['status'] == 2) {
            return 'true';
        } else {
            return 'false';
        }
    } else {
        return 'false';
    }
}


/**
 * @return bool|string
 */





if ($settings_details['envato_buyer_name'] == '' or $settings_details['envato_purchase_code'] == '' or $settings_details['envato_purchased_status'] == 0) {

    if ($get_method['user_id'] != '') {
        $set['user_status'] = get_user_status($get_method['user_id']);
    } else {
        $set['user_status'] = "false";
    }

    $set['JOBS_APP'][] = array('msg' => 'Purchase code verification failed!', 'success' => '0');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}


function generateRandomPassword($length = 10)
{

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function apply_job_count($user_id)
{

    global $mysqli;

    $qry_applied = "SELECT COUNT(*) as num FROM tbl_apply WHERE `user_id`='" . $user_id . "'";

    $total_applied = mysqli_fetch_array(mysqli_query($mysqli, $qry_applied));
    $total_applied = $total_applied['num'];

    return $total_applied;

}

function get_saved_info($user_id, $job_id)
{

    global $mysqli;

    $sql = "SELECT * FROM tbl_saved WHERE tbl_saved.`user_id`='$user_id' AND tbl_saved.`job_id`='$job_id'";
    $res = mysqli_query($mysqli, $sql);

    return ($res->num_rows == 1) ? 'true' : 'false';
}

function get_applied_info($user_id, $job_id)
{

    global $mysqli;

    $sql = "SELECT * FROM tbl_apply WHERE tbl_apply.`user_id`='$user_id' AND tbl_apply.`job_id`='$job_id'";
    $res = mysqli_query($mysqli, $sql);

    return ($res->num_rows == 1) ? 'true' : 'false';
}


function saved_job_count($user_id)
{
    global $mysqli;

    $qry_saved = "SELECT COUNT(*) as num FROM tbl_saved WHERE `user_id` ='" . $user_id . "' ";

    $total_saved = mysqli_fetch_array(mysqli_query($mysqli, $qry_saved));
    $total_saved = $total_saved['num'];

    return $total_saved;
}

function getGUIDnoHash()
{
    mt_srand((double)microtime() * 10000);
    $charid = md5(uniqid(rand(), true));
    $c = unpack("C*", $charid);
    $c = implode("", $c);

    return substr($c, 0, 10);
}

function get_job_info($job_id, $field_name)
{
    global $mysqli;

    $qry_job = "SELECT * FROM tbl_jobs WHERE `id`='" . $job_id . "'";
    $query1 = mysqli_query($mysqli, $qry_job);
    $row_job = mysqli_fetch_array($query1);

    $num_rows1 = mysqli_num_rows($query1);

    if ($num_rows1 > 0) {
        return $row_job[$field_name];
    } else {
        return "";
    }
}

function get_user_info($user_id, $field_name)
{
    global $mysqli;

    $qry_user = "SELECT * FROM tbl_users WHERE id='" . $user_id . "'";
    $query1 = mysqli_query($mysqli, $qry_user);
    $row_user = mysqli_fetch_array($query1);

    $num_rows1 = mysqli_num_rows($query1);

    if ($num_rows1 > 0) {
        return $row_user[$field_name];
    } else {
        return "";
    }
}

function get_apply_count($job_id)
{
    global $mysqli;

    $qry_apply = "SELECT COUNT(*) as num FROM tbl_apply,tbl_users WHERE user_id = tbl_users.id AND tbl_users.status = 2 AND `job_id`='" . $job_id . "'";
    $total_apply = mysqli_fetch_array(mysqli_query($mysqli, $qry_apply));
    $total_apply = $total_apply['num'];

    if ($total_apply) {
        return $total_apply;
    } else {
        return 0;
    }

}

function get_city_name($city_id)
{
    global $mysqli;

    $qry_video = "SELECT * FROM tbl_city WHERE `c_id`='" . $city_id . "'";
    $query1 = mysqli_query($mysqli, $qry_video);
    $row_video = mysqli_fetch_array($query1);

    return $row_video['city_name'];
}

function getAndSendOtp($mobileNumber)
{
    $generator = "1357902468";


    $thisOTP = 0;
    //AC58368fe26d4cd6ddade89ba79cb227e4
// Your Account SID and Auth Token from twilio.com/console
    $account_sid = 'AC58368fe26d4cd6ddade89ba79cb227e4';
    $auth_token = '1571f3150582d1111846f68a8c368bf8';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]
// A Twilio number you own with SMS capabilities
    $twilio_number = "+15597154186";
    try {
        $thistpGen = "";
        for ($i = 1; $i <= 4; $i++)
        {
            $thistpGen .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        $thisOTP = $thistpGen;
        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
        // Where to send a text message (your cell phone?)
            "+" . $mobileNumber,
            array(
                'from' => $twilio_number,
                'body' => 'Your PART TIME Verification Code is ' . $thisOTP
            )
        );

    } catch (Exception $e) {
        //echo $e->getCode() . ' : ' . $e->getMessage() . "<br>";
        $thisOTP = 0;
    }
    //print_r($client);
    return ($thisOTP) ? $thisOTP : 0;
}

$get_method = checkSignSalt($_POST['data']);

if ($get_method['method_name'] == "get_home") {

    $user_id = $get_method['user_id'];
    //getAndSendOtp("919584092141");
    $jsonObj3 = array();

    $queryUser = "SELECT * FROM tbl_users WHERE `id`= $user_id";
    $sqlUser = mysqli_query($mysqli, $queryUser) or die(mysqli_error($mysqli));
    $resultUser = mysqli_fetch_assoc($sqlUser);

    $row['current_wallet_amount'] = $resultUser["current_wallet_amount"];
    $row['credits_remaining']= $resultUser["credits_remaining"];
    $row['subscription_plan_id']= $resultUser["subscription_plan_id"];



    $query3 = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`= 1 AND tbl_jobs.`job_status`= 0 AND tbl_category.`status`= 1
		ORDER BY tbl_jobs.`id` DESC LIMIT " . HOME_LIMIT;

    $sql3 = mysqli_query($mysqli, $query3) or die(mysqli_error($mysqli));

    while ($data3 = mysqli_fetch_assoc($sql3)) {

        $row3['id'] = $data3['id'];
        $row3['cat_id'] = $data3['cat_id'];
        $row3['city_id'] = $data3['city_id'];
        $row3['job_type'] = $data3['job_type'];
        $row3['job_name'] = $data3['job_name'];
        $row3['job_designation'] = $data3['job_designation'];
        $row3['job_desc'] = $data3['job_desc'];
        $row3['job_salary'] = $data3['job_salary'];
        $row3['job_salary_mode'] = $data3['job_salary_mode'];
        $row3['job_company_name'] = $data3['job_company_name'];
        $row3['job_company_website'] = $data3['job_company_website'];
        $row3['job_phone_number'] = $data3['job_phone_number'];
        $row3['job_country_code'] = $data3['job_country_code'];
        $row3['job_mail'] = $data3['job_mail'];
        $row3['job_vacancy'] = $data3['job_vacancy'];
        $row3['job_address'] = $data3['job_address'];
        $row3['job_qualification'] = $data3['job_qualification'];
        $row3['job_skill'] = $data3['job_skill'];
        $row3['job_experince'] = $data3['job_experince'];
        $row3['job_work_day'] = $data3['job_work_day'];
        $row3['job_work_time'] = $data3['job_work_time'];
        $row3['job_map_latitude'] = $data3['job_map_latitude'];
        $row3['job_map_longitude'] = $data3['job_map_longitude'];
        $row3['job_image'] = $data3['job_image'];
        $row3['job_image'] = $file_path . 'images/' . $data3['job_image'];
        $row3['job_image_thumb'] = $file_path . 'images/thumbs/' . $data3['job_image'];
        $row3['job_date'] = date('m/d/Y', $data3['job_date']);
        $row3['cid'] = $data3['cid'];
        $row3['category_name'] = $data3['category_name'];
        $row3['category_image'] = $file_path . 'images/' . $data3['category_image'];
        $row3['category_image_thumb'] = $file_path . 'images/thumbs/' . $data3['category_image'];
        $row3['is_favourite'] = get_saved_info($user_id, $data3['id']);
        $row3['is_applied'] = get_applied_info($user_id, $data3['id']);

        array_push($jsonObj3, $row3);
    }

    $row['latest_job'] = $jsonObj3;

    $jsonObj1 = array();

    $query1 = "SELECT * FROM tbl_jobs 
      			 LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` WHERE
	       		 FIND_IN_SET(tbl_jobs.`id`,(SELECT `job_id` FROM tbl_recent WHERE tbl_recent.`user_id` = '" . $user_id . "')) AND tbl_jobs.`status`= 1 AND tbl_jobs.`job_status`= 0 AND tbl_category.`status`= 1 ORDER BY tbl_jobs.`id` DESC LIMIT " . HOME_LIMIT;
    $sql1 = mysqli_query($mysqli, $query1) or die(mysqli_error($mysqli));

    while ($data1 = mysqli_fetch_assoc($sql1)) {
        $row1['id'] = $data1['id'];
        $row1['cat_id'] = $data1['cat_id'];
        $row1['city_id'] = $data1['city_id'];
        $row1['job_type'] = $data1['job_type'];
        $row1['job_name'] = $data1['job_name'];
        $row1['job_designation'] = $data1['job_designation'];
        $row1['job_desc'] = $data1['job_desc'];
        $row1['job_salary'] = $data1['job_salary'];
        $row1['job_salary_mode'] = $data1['job_salary_mode'];
        $row1['job_company_name'] = $data1['job_company_name'];
        $row1['job_company_website'] = $data1['job_company_website'];
        $row1['job_phone_number'] = $data1['job_phone_number'];
        $row1['job_country_code'] = $data1['job_country_code'];
        $row1['job_mail'] = $data1['job_mail'];
        $row1['job_vacancy'] = $data1['job_vacancy'];
        $row1['job_address'] = $data1['job_address'];
        $row1['job_qualification'] = $data1['job_qualification'];
        $row1['job_skill'] = $data1['job_skill'];
        $row1['job_experince'] = $data1['job_experince'];
        $row1['job_work_day'] = $data1['job_work_day'];
        $row1['job_work_time'] = $data1['job_work_time'];
        $row1['job_map_latitude'] = $data1['job_map_latitude'];
        $row1['job_map_longitude'] = $data1['job_map_longitude'];
        $row1['job_image'] = $data1['job_image'];
        $row1['job_image'] = $file_path . 'images/' . $data1['job_image'];
        $row1['job_image_thumb'] = $file_path . 'images/thumbs/' . $data1['job_image'];
        $row1['job_date'] = date('m/d/Y', $data1['job_date']);

        $row1['cid'] = $data1['cid'];
        $row1['category_name'] = $data1['category_name'];
        $row1['category_image'] = $file_path . 'images/' . $data1['category_image'];
        $row1['category_image_thumb'] = $file_path . 'images/thumbs/' . $data1['category_image'];

        $row1['is_favourite'] = get_saved_info($user_id, $data1['id']);
        $row1['is_applied'] = get_applied_info($user_id, $data1['id']);

        array_push($jsonObj1, $row1);

    }

    $row['recent_job'] = $jsonObj1;

    $jsonObj_2 = array();

    $cid = API_CAT_ORDER_BY;

    $query2 = "SELECT `cid`,`category_name`,`category_image` FROM tbl_category ORDER BY tbl_category." . $cid . " ASC LIMIT 5";
    $sql2 = mysqli_query($mysqli, $query2) or die(mysqli_error($mysqli));

    while ($data2 = mysqli_fetch_assoc($sql2)) {
        $row2['cid'] = $data2['cid'];
        $row2['category_name'] = $data2['category_name'];
        $row2['category_image'] = $file_path . 'images/' . $data2['category_image'];
        $row2['category_image_thumb'] = $file_path . 'images/thumbs/' . $data2['category_image'];

        array_push($jsonObj_2, $row2);

    }
    $row['cat_list'] = $jsonObj_2;

    $set['JOBS_APP'] = $row;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_category") {
    if (isset($get_method['page'])) {
        $query_rec = "SELECT COUNT(*) as num FROM tbl_category WHERE `status`= 1";
        $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

        $page_limit = API_PAGE_LIMIT;

        $limit = ($get_method['page'] - 1) * $page_limit;

        $jsonObj = array();

        $cat_order = API_CAT_ORDER_BY;

        $query = "SELECT cid,category_name,category_image FROM tbl_category 
    		WHERE `status`=1 ORDER BY tbl_category." . $cat_order . " LIMIT $limit, $page_limit";
        $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

        $total_item = $total_pages['num'];

    } else {
        $jsonObj = array();

        $cat_order = API_CAT_ORDER_BY;

        $query = "SELECT `cid`,`category_name`,`category_image` FROM tbl_category WHERE `status`=1 ORDER BY tbl_category." . $cat_order . "";
        $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

        $total_item = 0;
    }

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['total_item'] = $total_item;
        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_city") {
    $jsonObj = array();

    $query = "SELECT `c_id`,`city_name` FROM tbl_city WHERE tbl_city.`status`=1 ORDER BY tbl_city.`c_id` DESC";
    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {

        $row['c_id'] = $data['c_id'];
        $row['city_name'] = $data['city_name'];

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_notification") {
    if($get_method["user_id"]!="")
    {
        $jsonObj = array();
        $query = "SELECT * FROM `tbl_notification` WHERE `user_id`=".$get_method["user_id"]." ORDER BY `id` DESC";
        $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

        while ($data = mysqli_fetch_assoc($sql)) {

            $row['title'] = $data['title'];
            $row['body'] = $data['body'];
            $row['click_action'] = $data['click_action'];

            array_push($jsonObj, $row);

        }
        $set['JOBS_APP'] = $jsonObj;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_list") {
    $user_id = $get_method['user_id'];

    $jsonObj0 = array();

    $query12 = "SELECT region_id FROM tbl_users,tbl_city WHERE tbl_city.c_id =tbl_users.city AND tbl_users.`id`=$user_id ";
    $sql12 = mysqli_query($mysqli, $query12) or die(mysqli_error($mysqli));
    $row12 = mysqli_fetch_assoc($sql12);
    $region_id = $row12["region_id"];

    $query0 = "SELECT * FROM tbl_city WHERE tbl_city.`status`=1 AND region_id = $region_id ORDER BY tbl_city.`c_id` DESC";
    $sql0 = mysqli_query($mysqli, $query0) or die(mysqli_error($mysqli));

    while ($data0 = mysqli_fetch_assoc($sql0)) {

        $row0['c_id'] = $data0['c_id'];
        $row0['city_name'] = $data0['city_name'];

        array_push($jsonObj0, $row0);

    }

    $row['city_list'] = $jsonObj0;

    $jsonObj1 = array();

    $query1 = "SELECT * FROM tbl_jobs WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0
		        ORDER BY tbl_jobs.`id` DESC ";

    $sql1 = mysqli_query($mysqli, $query1) or die(mysql_error($mysqli));

    while ($data1 = mysqli_fetch_assoc($sql1)) {
        $row1['id'] = $data1['id'];
        $row1['job_company_name'] = $data1['job_company_name'];

        array_push($jsonObj1, $row1);

    }

    $row['company_list'] = $jsonObj1;

    $jsonObj_2 = array();

    $cid = API_CAT_ORDER_BY;

    $query2 = "SELECT * FROM tbl_category ORDER BY tbl_category." . $cid . " DESC";
    $sql2 = mysqli_query($mysqli, $query2) or die(mysqli_error($mysqli));

    while ($data2 = mysqli_fetch_assoc($sql2)) {
        $row2['cid'] = $data2['cid'];
        $row2['category_name'] = $data2['category_name'];
        $row2['category_image'] = $file_path . 'images/' . $data2['category_image'];
        $row2['category_image_thumb'] = $file_path . 'images/thumbs/' . $data2['category_image'];

        array_push($jsonObj_2, $row2);

    }
    $row['cat_list'] = $jsonObj_2;

    $set['JOBS_APP'] = $row;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_job_by_cat_id") {
    $post_order_by = API_CAT_POST_ORDER_BY;

    $cat_id = $get_method['cat_id'];

    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='" . $cat_id . "' AND tbl_jobs.`status`=1  AND tbl_jobs.`job_status`= 0";

    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		WHERE tbl_jobs.`cat_id`='" . $cat_id . "' AND tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 ORDER BY tbl_jobs.`id` " . $post_order_by . " LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['total_item'] = $total_pages['num'];
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('m/d/Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);
        array_push($jsonObj, $row);

    }
    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "get_latest_job") {
    $latest_limit = API_LATEST_LIMIT;
    $jsonObj = array();

    $page_limit = API_PAGE_LIMIT;

    $total_pages = round($latest_limit / $page_limit);

    $limit = ($get_method['page'] - 1) * $page_limit;

    $actual_limit = $get_method['page'] * $page_limit;

    if ($actual_limit <= $latest_limit) {
        $page_limit = API_PAGE_LIMIT;
    } else if ($get_method['page'] <= $total_pages) {
        $page_limit = $latest_limit - $page_limit;
    } else {
        $page_limit = 0;
    }
    $query = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`= 1  AND tbl_jobs.`job_status`= 0ORDER BY tbl_jobs.`id` DESC LIMIT $limit,$page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);
        array_push($jsonObj, $row);

    }


    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "get_recent_job") {
    $user_id = $get_method['user_id'];

    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
      			 LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` WHERE
	       		 FIND_IN_SET(tbl_jobs.`id`,(SELECT `job_id` FROM tbl_recent WHERE tbl_recent.`user_id` = '" . $user_id . "')) AND tbl_jobs.`status`= 1  AND tbl_jobs.`job_status`= 0 AND tbl_category.`status`= 1 ORDER BY tbl_jobs.`id`";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_jobs 
      			 LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` WHERE
	       		 FIND_IN_SET(tbl_jobs.`id`,(SELECT `job_id` FROM tbl_recent WHERE tbl_recent.`user_id` = '" . $user_id . "')) AND tbl_jobs.`status`= 1 AND tbl_jobs.`job_status`= 0 AND tbl_category.`status`= 1 ORDER BY tbl_jobs.`id` DESC LIMIT $limit ,$page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($user_id, $data['id']);
        $row['is_applied'] = get_applied_info($user_id, $data['id']);
        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "get_search_job") {

    $job_search = $get_method['search_text'];
    $cat_id = $get_method['cat_id'];
    $city_id = $get_method['city_id'];
    $budget_range = $get_method['budget_range'];
    $day_range = $get_method['day_range'];
    $time_range = $get_method['time_range'];
    $job_type = $get_method['job_type'];

    switch ($budget_range)
    {
        case 1:
            $budget_range="0-500";
            break;
        case 2:
            $budget_range="500-5000";
            break;
        case 3:
            $budget_range="5000-10000";
            break;
        case 4:
            $budget_range="10000-1000000000";
            break;
    }

    $jsonObj = array();

    $sqlFilter = "";

    if($job_search)
    {
        $sqlFilter .= " AND (
                            tbl_jobs.`job_name` LIKE '%".$job_search."%' 
                         OR tbl_jobs.`job_designation` LIKE '%".$job_search."%' 
                         OR tbl_jobs.`job_desc` LIKE '%".$job_search."%' 
                         OR tbl_jobs.`job_address` LIKE '%".$job_search."%') ";
    }

    if($cat_id)
    {
        $sqlFilter .= " AND tbl_jobs.`cat_id` = '$cat_id' ";
    }

    if($day_range)
    {
        $sqlFilter .= " AND tbl_jobs.`job_work_day` LIKE '%".$day_range."%' ";
    }

    if($time_range)
    {
        $sqlFilter .= " AND tbl_jobs.`job_work_time` LIKE '%".$time_range."%' ";
    }

    if($budget_range)
    {
        $budRanArray = explode("-",$budget_range);
        $start = $budRanArray[0];
        $end = $budRanArray[1];
        $sqlFilter .= " AND tbl_jobs.`job_salary` BETWEEN $start AND $end ";
    }

    if($city_id)
    {
        $sqlFilter .= " AND tbl_jobs.`city_id` = '$city_id' ";
    }

    if($job_type)
    {
        $sqlFilter .= " AND tbl_jobs.`job_type` LIKE '%" . $job_type . "%' ";
    }

    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 $sqlFilter";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $query = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 $sqlFilter ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['total_item'] = $total_pages['num'];
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('m/d/Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);
        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "search_by_keyword") {

    $job_search = $get_method['search_text'];

    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 AND (tbl_jobs.`job_name` LIKE '%" . $job_search . "%' OR tbl_jobs.`job_address` LIKE '%" . $job_search . "%')";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 AND (tbl_jobs.`job_name` LIKE '%" . $job_search . "%' OR tbl_jobs.`job_address` LIKE '%" . $job_search . "%') ORDER BY tbl_jobs.`job_name` LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['total_item'] = $total_pages['num'];
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "get_single_job") {
    $user_id = $get_method['user_id'];
    $job_id = $get_method['job_id'];

    $jsonObj = array();

    $query = "SELECT tbl_jobs.*,tbl_category.*,tbl_users.name as user_name,tbl_users.email as user_email,tbl_users.user_image FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_users ON tbl_jobs.`user_id`= tbl_users.`id`
		WHERE tbl_jobs.`id`='" . $job_id . "'";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['id'] = $data['id'];
        $row['user_name'] = $data['user_name'];
        $row['user_email'] = $data['user_email'];
        $row['user_image'] = $data['user_image'];
        $row['cat_id'] = $data['cat_id'];
        $row['user_id'] = $data['user_id'];
        $row['city_id'] = $data['city_id'];
        $row['city_name'] = get_city_name($data['city_id']);
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);
        $row['job_start_time'] = $data['job_start_time'];
        $row['job_end_time'] = $data['job_end_time'];

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];


        array_push($jsonObj, $row);

    }

    $qry_saved = "SELECT * FROM tbl_saved WHERE `user_id`= '" . $user_id . "' AND `job_id` = '" . $job_id . "'";
    $result_saved = mysqli_query($mysqli, $qry_saved);
    $num_rows = mysqli_num_rows($result_saved);
    //$row = mysqli_fetch_assoc($result_saved);

    if ($num_rows > 0) {
        $set['already_saved'] = 'true';
    } else {
        $set['already_saved'] = 'false';
    }

    $sql_recent = "SELECT * FROM tbl_recent WHERE `user_id`= '" . $user_id . "'";
    $res_recent = mysqli_query($mysqli, $sql_recent);
    $num_rows = mysqli_num_rows($res_recent);

    if ($num_rows == 0 && $user_id != '') {

        $data_log = array(
            'user_id' => $user_id,
            'job_id' => $row['id'],
            'recent_date' => date('Y-m-d')
        );

        $qry = Insert('tbl_recent', $data_log);

    } else {
        $recent_row = mysqli_fetch_assoc($res_recent);

        $job_id = explode(',', $recent_row['job_id']);

        if (!in_array($row['id'], $job_id)) {

            $data1 = array(
                'job_id' => $recent_row['job_id'] . ',' . $row['id']
            );

            $qry = Update('tbl_recent', $data1, "WHERE id = '" . $recent_row['id'] . "'");

        }
    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


}
else if ($get_method['method_name'] == "get_similar_jobs") {

    //Get cat id using job id
    $query_job = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid`
		WHERE tbl_jobs.`id`='" . $get_method['job_id'] . "' AND tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0";
    $sql_job = mysqli_query($mysqli, $query_job) or die(mysqli_error($mysqli));
    $row_job = mysqli_fetch_assoc($sql_job);


    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='" . $row_job['cat_id'] . "' AND tbl_jobs.`id` !='" . $get_method['job_id'] . "' AND tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		WHERE tbl_jobs.`cat_id`='" . $row_job['cat_id'] . "' AND tbl_jobs.`id` !='" . $get_method['job_id'] . "' AND tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 ORDER BY tbl_jobs.`id` DESC LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['total_item'] = $total_pages['num'];
        $row['id'] = $data['id'];
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);
        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "apply_job_add") {

    $apply_user_id = $get_method['apply_user_id'];
    $apply_job_id = $get_method['apply_job_id'];

    $qry = "SELECT * FROM tbl_apply WHERE `user_id` = '" . $apply_user_id . "' AND `job_id` = '" . $apply_job_id . "'";
    $result = mysqli_query($mysqli, $qry);
    $num_rows = mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);

    $user_qry = "SELECT * FROM tbl_users WHERE `id` = '" . $apply_user_id . "'";
    $user_result = mysqli_query($mysqli, $user_qry);
    $user_row = mysqli_fetch_assoc($user_result);

    $qry_job = "SELECT * FROM tbl_jobs WHERE `id`='" . $apply_job_id . "'";
    $job_result = mysqli_query($mysqli, $qry_job);
    $job_row = mysqli_fetch_assoc($job_result);


    if ($num_rows == 0) {
        if ($user_row['user_resume'] != '') {
            //Insert data
            $data_apply = array(
                'user_id' => $apply_user_id,
                'job_id' => $apply_job_id,
                'apply_date' => date('Y-m-d H:i:s')
            );

            $qry_apply = Insert('tbl_apply', $data_apply);

            $user_id = $job_row['user_id'];
            $fcmMessage = "Part Time: Job Applied";
            $fcmBody = $user_row["name"]." has Applied to your job!!";
            $fcmClickIntent = "provider_job_applied";
            sendFcmNotification($user_id,$fcmMessage,$fcmBody,$fcmClickIntent);


            if (get_user_info($job_row['user_id'], 'email') != '') {

                $to = (get_user_info($job_row['user_id'], 'email'));
                $recipient_name = $job_row['job_name'];

                $path = 'uploads/' . $user_row['user_resume'];

                $user_resume = rand(0, 99999) . "_" . str_replace(" ", "-", $_FILES['user_resume']['name']);
                $tmp = $_FILES['user_resume']['tmp_name'];
                move_uploaded_file($tmp, $path . $user_resume);
                $icon_path = 'uploads/' . $user_row['user_resume'];

                // subject
                $subject = '[IMPORTANT] ' . APP_NAME . '  New apply details';

                $message = '<div style="background-color: #f9f9f9;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							     <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="header" width="120"/></td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                        <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500; margin-bottom:0px;"> 
							                           <h1> ' . $job_row['job_name'] . '</h1></p>
							                         <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                          ' . $job_row['job_company_name'] . '</p>

							                        <h4 style="color: #262626; font-size: 18px; margin-top:0px;">Apply User Details</h4>
							                        <hr>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Name: ' . $user_row['name'] . '</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Email: ' . $user_row['email'] . '</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            Phone: ' . $user_row['phone'] . '</p>
							                           
							                        </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © ' . APP_NAME . '.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

                send_email($to, $recipient_name, $subject, $message, $icon_path);

                $to1 = $user_row['email'];
                $recipient_name1 = $user_row['name'];
                // subject
                $subject1 = $subject = '[IMPORTANT] ' . APP_NAME . '  Job Information';

                $message1 = '<div style="background-color: #f9f9f9;" align="center"><br />
							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
							    <tbody>
							      <tr>
							        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="header" width="120"/></td>
							      </tr>
							      <tr>
							        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
							            <tbody>
							              <tr>
							                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
							                    <tbody>
							                      <tr>
							                        <td>
							                        <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500; margin-bottom:0px;"> 
							                           <h1> ' . $job_row['job_name'] . '</h1></p>
							                         <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                          ' . $job_row['job_company_name'] . '</p>

							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            ' . $job_row['job_type'] . '</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                           ' . $job_row['job_address'] . '</p>
							                          <p style="color:#262626; font-size:16px; line-height:10px;font-weight:500;"> 
							                            ' . $job_row['job_company_website'] . '</p>

							                          <h4 style="color: #262626; font-size: 18px; margin-top:0px;">You have successfully Apply</h4>
							                        </td>
							                      </tr>
							                    </tbody>
							                  </table>
							                </td>
							              </tr>
							            </tbody>
							          </table>
							        </td>
							      </tr>
							      <tr>
							        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © ' . APP_NAME . '.</td>
							      </tr>
							    </tbody>
							  </table>
							</div>';

                send_email($to1, $recipient_name1, $subject1, $message1);


                $set['JOBS_APP'][] = array('msg' => $app_lang['job_applied'], 'success' => '1');

            }
        } else {

            $set['JOBS_APP'][] = array('msg' => $app_lang['upload_resume'], 'status' => -1, 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => $app_lang['already_applied'], 'success' => '0');

    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


}
else if ($get_method['method_name'] == "saved_job_add") {

    $saved_user_id = $get_method['saved_user_id'];
    $saved_job_id = $get_method['saved_job_id'];

    $sql = "SELECT * FROM tbl_saved WHERE `user_id` = '$saved_user_id' AND `job_id` = '$saved_job_id'";
    $res = mysqli_query($mysqli, $sql);

    if ($res->num_rows == 0) {

        //Inser data
        $data = array(
            'user_id' => $saved_user_id,
            'job_id' => $saved_job_id,
            'created_at' => date('Y-m-d H:i:s')
        );

        $qry_apply = Insert('tbl_saved', $data);

        $set['JOBS_APP'][] = array('msg' => $app_lang['add_favourite'], 'success' => '1');

    } else {
        // remove to favourite list
        $deleteSql = "DELETE FROM tbl_saved WHERE `user_id`='$saved_user_id' AND `job_id`='$saved_job_id'";
        if (mysqli_query($mysqli, $deleteSql)) {
            $set['JOBS_APP'][] = array('msg' => $app_lang['remove_favourite'], 'success' => '0');
        } else {

            $set['JOBS_APP'][] = array('msg' => $app_lang['error_msg'], 'success' => '0');
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "delete_job") {


    Delete('tbl_jobs', 'id=' . $get_method['delete_job_id'] . '');

    Delete('tbl_apply', 'job_id=' . $get_method['delete_job_id'] . '');


    $set['JOBS_APP'][] = array('msg' => $app_lang['delete_job'], 'success' => '1');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


}
else if ($get_method['method_name'] == "send_notification") {

    $user_id = $get_method['user_id'];

    sendFcmNotification($user_id,"title hu mai"," message hu mai","actuion ");

    $set['JOBS_APP'][] = array('msg' => " notification_sent ", 'success' => '1');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


}
else if ($get_method['method_name'] == "job_list") {

    $jsonObj = array();

    $page_limit = API_PAGE_LIMIT;

    $limit = ($get_method['page'] - 1) * $page_limit;

    $user_id = $get_method['user_id'];


    $queryUser = "SELECT * FROM tbl_users WHERE `id`= $user_id";
    $sqlUser = mysqli_query($mysqli, $queryUser) or die(mysqli_error($mysqli));
    $resultUser = mysqli_fetch_assoc($sqlUser);

    $row['current_wallet_amount'] = $resultUser["current_wallet_amount"];
    $row['credits_remaining']= $resultUser["credits_remaining"];
    $row['subscription_plan_id']= $resultUser["subscription_plan_id"];

    $job_status = $get_method['job_status'];
    $job_status_query = "";
    if($job_status AND  $job_status!="")
    {
        $job_status_query = " AND `job_status`='$job_status' ";
    }

    $jsonObj = array();

    $queryCount = "SELECT COUNT(*) AS all_jobs,SUM(if(tbl_jobs.job_status = 4, 1, 0)) AS active_jobs, SUM(if(tbl_jobs.job_status = 1, 1, 0)) AS completed_jobs, SUM(if(tbl_jobs.job_status = 3, 1, 0)) AS failed_jobs FROM tbl_jobs 
           WHERE tbl_jobs.`user_id`='" . $user_id . "' AND tbl_jobs.`status`= 1";
    $resultCount = mysqli_query($mysqli, $queryCount) or die(mysqli_error($mysqli));
    $rowCount = mysqli_fetch_assoc($resultCount);
    //print_r($rowCount);

    $all_jobs = $rowCount["all_jobs"];
    $active_jobs = $rowCount["active_jobs"];
    $completed_jobs = $rowCount["completed_jobs"];
    $failed_jobs = $rowCount["failed_jobs"];

    $query = "SELECT * FROM tbl_jobs
           LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
           WHERE tbl_jobs.`user_id`='" . $user_id . "' AND tbl_jobs.`status`= 1 $job_status_query 
           ORDER BY tbl_jobs.`id` DESC LIMIT $limit, $page_limit";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['id'] = $data['id'];
        $row['all_jobs'] = $all_jobs;
        $row['active_jobs'] = $active_jobs;
        $row['completed_jobs'] = $completed_jobs;
        $row['failed_jobs'] = $failed_jobs;
        $row['cat_id'] = $data['cat_id'];
        $row['city_id'] = $data['city_id'];
        $row['job_type'] = $data['job_type'];
        $row['job_name'] = $data['job_name'];
        $row['job_designation'] = $data['job_designation'];
        $row['job_desc'] = $data['job_desc'];
        $row['job_salary'] = $data['job_salary'];
        $row['job_salary_mode'] = $data['job_salary_mode'];
        $row['job_company_name'] = $data['job_company_name'];
        $row['job_company_website'] = $data['job_company_website'];
        $row['job_phone_number'] = $data['job_phone_number'];
        $row['job_country_code'] = $data['job_country_code'];
        $row['job_mail'] = $data['job_mail'];
        $row['job_vacancy'] = $data['job_vacancy'];
        $row['job_address'] = $data['job_address'];
        $row['job_qualification'] = $data['job_qualification'];
        $row['job_skill'] = $data['job_skill'];
        $row['job_experince'] = $data['job_experince'];
        $row['job_work_day'] = $data['job_work_day'];
        $row['job_work_time'] = $data['job_work_time'];
        $row['job_map_latitude'] = $data['job_map_latitude'];
        $row['job_map_longitude'] = $data['job_map_longitude'];
        $row['job_image'] = $data['job_image'];
        $row['job_status'] = $data['job_status'];
        $row['user_alloted'] = $data['user_alloted'];
        $row['job_start_time'] = $data['job_start_time'];
        $row['job_end_time'] = $data['job_end_time'];
        $row['job_image'] = $file_path . 'images/' . $data['job_image'];
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . $data['job_image'];
        $row['job_date'] = date('d-m-Y', $data['job_date']);

        $row['job_apply_total'] = get_apply_count($data['id']);

        $row['cid'] = $data['cid'];
        $row['category_name'] = $data['category_name'];
        $row['category_image'] = $file_path . 'images/' . $data['category_image'];
        $row['category_image_thumb'] = $file_path . 'images/thumbs/' . $data['category_image'];

        $row['is_favourite'] = get_saved_info($user_id, $data['id']);
        $row['is_applied'] = get_applied_info($user_id, $data['id']);
        array_push($jsonObj, $row);
    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_job_apply_list") {

    $jsonObj = array();

    $query = "SELECT * FROM tbl_users
             LEFT JOIN tbl_apply ON tbl_users.`id`= tbl_apply.`user_id` 
             WHERE tbl_users.status = 2 AND tbl_apply.`job_id`=" . $get_method['apply_job_id'] . " ORDER BY tbl_users.`id` DESC";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['seen'] = $data['seen'];
        $row['user_id'] = $data['id'];
        $row['name'] = $data['name'];
        $row['email'] = $data['email'];
        $row['phone'] = $data['phone'];
        $row['country_code'] = $data['country_code'];
        $row['city'] = $data['city'];

        if ($data['user_image']) {
            $user_image = $file_path . 'images/' . $data['user_image'];
        } else {
            $user_image = '';
        }

        $row['user_image'] = $user_image;

        if ($data['user_resume']) {
            $user_resume = $file_path . 'uploads/' . $data['user_resume'];
        } else {
            $user_resume = '';
        }

        $row['user_resume'] = $user_resume;

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_apply_list") {

    $query_rec = "SELECT COUNT(*) as num FROM tbl_apply  
 	 			  WHERE  tbl_apply.`user_id`=" . $get_method['user_id'] . "";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;
    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_apply  WHERE  tbl_apply.`user_id`=" . $get_method['user_id'] . "
	 		 ORDER BY tbl_apply.`ap_id` DESC LIMIT $limit, $page_limit";
    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {

        $row['total_item'] = $total_pages['num'];
        $row['apply_id'] = $data['ap_id'];
        $row['user_id'] = $data['user_id'];
        $row['job_id'] = $data['job_id'];

        $row['id'] = get_job_info($data['job_id'], 'id');
        $row['cat_id'] = get_job_info($data['job_id'], 'cat_id');
        $row['city_id'] = get_job_info($data['job_id'], 'city_id');
        $row['job_type'] = get_job_info($data['job_id'], 'job_type');
        $row['job_name'] = get_job_info($data['job_id'], 'job_name');
        $row['job_designation'] = get_job_info($data['job_id'], 'job_designation');
        $row['job_desc'] = get_job_info($data['job_id'], 'job_desc');
        $row['job_salary'] = get_job_info($data['job_id'], 'job_salary');
        $row['job_salary_mode'] = get_job_info($data['job_id'], 'job_salary_mode');
        $row['job_company_name'] = get_job_info($data['job_id'], 'job_company_name');
        $row['job_company_website'] = get_job_info($data['job_id'], 'job_company_website');
        $row['job_phone_number'] = get_job_info($data['job_id'], 'job_phone_number');
        $row['job_country_code'] = get_job_info($data['job_id'], 'job_country_code');
        $row['job_mail'] = get_job_info($data['job_id'], 'job_mail');
        $row['job_vacancy'] = get_job_info($data['job_id'], 'job_vacancy');
        $row['job_address'] = get_job_info($data['job_id'], 'job_address');
        $row['job_qualification'] = get_job_info($data['job_id'], 'job_qualification');
        $row['job_skill'] = get_job_info($data['job_id'], 'job_skill');
        $row['job_experince'] = get_job_info($data['job_id'], 'job_experince');
        $row['job_work_day'] = get_job_info($data['job_id'], 'job_work_day');
        $row['job_work_time'] = get_job_info($data['job_id'], 'job_work_time');
        $row['job_map_latitude'] = get_job_info($data['job_id'], 'job_map_latitude');
        $row['job_map_longitude'] = get_job_info($data['job_id'], 'job_map_longitude');
        $row['job_image'] = $file_path . 'images/' . get_job_info($data['job_id'], 'job_image');
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . get_job_info($data['job_id'], 'job_image');
        $row['job_date'] = date('d-m-Y', get_job_info($data['job_id'], 'job_date'));
        $row['apply_date'] = date('Y-m-d', strtotime($data['apply_date']));
        $row['job_status'] = get_job_info($data['job_id'], 'job_status');

        if ($data['seen'] == 1) {
            $row['seen'] = 'true';
        } else {
            $row['seen'] = 'false';
        }

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_apply_job_seen") {

    $queryJobs = "SELECT user_id,job_salary,job_salary_mode  FROM tbl_jobs  
 	 			  WHERE  tbl_jobs.`job_status` = 0 AND `status` = 1 AND id = ".$get_method['job_id'];
    $queryJobs = mysqli_query($mysqli, $queryJobs);

    if(mysqli_num_rows($queryJobs))
    {
        $rowJob = mysqli_fetch_assoc($queryJobs);
        $provider_id = $rowJob["user_id"];
        $job_salary = $rowJob["job_salary"];
        $job_salary_mode = $rowJob["job_salary_mode"];//todo


        $qry1 = "SELECT `name`,current_wallet_amount FROM tbl_users WHERE `id` = '" . $provider_id . "'";
        $result1 = mysqli_query($mysqli, $qry1) or die('Error in fetch data ->' . mysqli_error($mysqli));

        $rowResult1 = mysqli_fetch_assoc($result1);
        $current_wallet_amount = $rowResult1["current_wallet_amount"];

        if($current_wallet_amount>=$job_salary)
        {
            $data = array(
                'job_status' => '4',
                'job_start_time' => time()*1000,
                'user_alloted' => $get_method['apply_user_id']
            );
            $edit_status = Update('tbl_jobs', $data, "WHERE tbl_jobs.`job_status` = 0 AND `status` = 1 AND id = '" . $get_method['job_id'] . "'");

            $data = array(
                'seen' => '1',
                'job_start_time' => time()*1000
            );
            $edit_status = Update('tbl_apply', $data, "WHERE user_id = '" . $get_method['apply_user_id'] . "' AND job_id = '" . $get_method['job_id'] . "'");

            $user_id = $get_method['apply_user_id'];
            $fcmMessage = "Part Time: Job Awarded";
            $fcmBody = "New Job Awarded to you!!";
            $fcmClickIntent = "seeker_job_awarded";
            sendFcmNotification($user_id,$fcmMessage,$fcmBody,$fcmClickIntent);

            $set['JOBS_APP'][] = array('msg' => $app_lang['job_seen'], 'success' => '1');

        }else{
            $set['JOBS_APP'][] = array('msg' => "Failed!! Insufficient Amount in Wallet", 'success' => '0');
        }
    }else{
        $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid Data Provided", 'success' => '0');
    }
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_saved_list") {

    $query_rec = "SELECT COUNT(*) as num FROM tbl_saved  WHERE  tbl_saved.`user_id`=" . $get_method['user_id'] . "";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = API_PAGE_LIMIT;
    $limit = ($get_method['page'] - 1) * $page_limit;

    $jsonObj = array();

    $query = "SELECT * FROM tbl_saved  WHERE  tbl_saved.`user_id`=" . $get_method['user_id'] . "
		   ORDER BY tbl_saved.`sa_id` DESC LIMIT $limit, $page_limit";
    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {

        $row['total_item'] = $total_pages['num'];
        $row['user_id'] = $data['user_id'];
        $row['job_id'] = $data['job_id'];
        $row['id'] = get_job_info($data['job_id'], 'id');
        $row['cat_id'] = get_job_info($data['job_id'], 'cat_id');
        $row['city_id'] = get_job_info($data['job_id'], 'city_id');
        $row['job_type'] = get_job_info($data['job_id'], 'job_type');
        $row['job_name'] = get_job_info($data['job_id'], 'job_name');
        $row['job_designation'] = get_job_info($data['job_id'], 'job_designation');
        $row['job_desc'] = get_job_info($data['job_id'], 'job_desc');
        $row['job_salary'] = get_job_info($data['job_id'], 'job_salary');
        $row['job_salary_mode'] = get_job_info($data['job_id'], 'job_salary_mode');
        $row['job_company_name'] = get_job_info($data['job_id'], 'job_company_name');
        $row['job_company_website'] = get_job_info($data['job_id'], 'job_company_website');
        $row['job_phone_number'] = get_job_info($data['job_id'], 'job_phone_number');
        $row['job_country_code'] = get_job_info($data['job_id'], 'job_country_code');
        $row['job_mail'] = get_job_info($data['job_id'], 'job_mail');
        $row['job_vacancy'] = get_job_info($data['job_id'], 'job_vacancy');
        $row['job_address'] = get_job_info($data['job_id'], 'job_address');
        $row['job_qualification'] = get_job_info($data['job_id'], 'job_qualification');
        $row['job_skill'] = get_job_info($data['job_id'], 'job_skill');
        $row['job_experince'] = get_job_info($data['job_id'], 'job_experince');
        $row['job_work_day'] = get_job_info($data['job_id'], 'job_work_day');
        $row['job_work_time'] = get_job_info($data['job_id'], 'job_work_time');
        $row['job_map_latitude'] = get_job_info($data['job_id'], 'job_map_latitude');
        $row['job_map_longitude'] = get_job_info($data['job_id'], 'job_map_longitude');
        $row['job_image'] = get_job_info($data['job_id'], 'job_image');
        $row['job_image'] = $file_path . 'images/' . get_job_info($data['job_id'], 'job_image');
        $row['job_image_thumb'] = $file_path . 'images/thumbs/' . get_job_info($data['job_id'], 'job_image');
        $row['job_date'] = date('m/d/Y', get_job_info($data['job_id'], 'job_date'));

        $row['is_favourite'] = get_saved_info($get_method['user_id'], $data['job_id']);
        $row['is_applied'] = get_applied_info($get_method['user_id'], $data['id']);
        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "job_add") {

    /*    $subCheckQuery = "SELECT credits_remaining FROM tbl_users WHERE id = ".$get_method['user_id']." AND user_type=2";
        $subCheckResult = mysqli_query($mysqli,$subCheckQuery);
        $subCheckRow = mysqli_fetch_assoc($subCheckResult);

        if($subCheckRow["credits_remaining"]>0)
        {
        }else{
            $set['JOBS_APP'][] = array('msg' => "Failed !!!, Insufficient Credits please subscribe again", 'success' => '0');

        }*/

    $job_image = rand(0, 99999) . "_" . $_FILES['job_image']['name'];

    $ext = pathinfo($_FILES['job_image']['name'], PATHINFO_EXTENSION);

    $job_image = rand(0, 99999) . "." . $ext;
    //Main Image
    $tpath1 = 'images/' . $job_image;

    $tmp = $_FILES['job_image']['tmp_name'];
    move_uploaded_file($tmp, $tpath1);

    //Thumb Image
    $thumbpath = 'images/thumbs/' . $job_image;
    $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '200', '200');

    $data = array(
        'user_id' => $get_method['user_id'],
        'cat_id' => $get_method['cat_id'],
        'city_id' => $get_method['city_id'],
        'job_type' => $get_method['job_type'],
        'job_name' => addslashes($get_method['job_name']),
        'job_designation' => addslashes($get_method['job_designation']),
        'job_desc' => addslashes($get_method['job_desc']),
        'job_salary' => $get_method['job_salary'],
        'job_salary_mode' => $get_method['job_salary_mode'],
        'job_company_name' => $get_method['job_company_name'],
        'job_company_website' => $get_method['job_company_website'],
        'job_phone_number' => $get_method['job_phone_number'],
        'job_country_code' => $get_method['job_country_code'],
        'job_mail' => $get_method['job_mail'],
        'job_vacancy' => $get_method['job_vacancy'],
        'job_address' => addslashes($get_method['job_address']),
        'job_qualification' => addslashes($get_method['job_qualification']),
        'job_skill' => addslashes($get_method['job_skill']),
        'job_experince' => addslashes($get_method['job_experince']),
        'job_work_day' => addslashes($get_method['job_work_day']),
        'job_work_time' => addslashes($get_method['job_work_time']),
        'job_map_latitude' => addslashes($get_method['job_map_latitude']),
        'job_map_longitude' => addslashes($get_method['job_map_longitude']),
        'job_end_time' => addslashes($get_method['job_end_time']),
        'job_start_time' => addslashes($get_method['job_start_time']),
        'job_image' => $job_image,
        'job_date' => strtotime($get_method['job_date']),
        'status' => 1
    );
    $qry = Insert('tbl_jobs', $data);

    //mysqli_query($mysqli, "UPDATE tbl_users SET `credits_remaining` = `credits_remaining`-1 WHERE `id` = '".$get_method['user_id']."'");

    $set['JOBS_APP'][] = array('msg' => $app_lang['add_job'], 'success' => '1');



    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "edit_job") {
    if ($_FILES['job_image']['name'] != "") {

        $job_image = rand(0, 99999) . "_" . $_FILES['job_image']['name'];

        $ext = pathinfo($_FILES['job_image']['name'], PATHINFO_EXTENSION);

        $job_image = rand(0, 99999) . "." . $ext;
        //Main Image
        $tpath1 = 'images/' . $job_image;

        $tmp = $_FILES['job_image']['tmp_name'];
        move_uploaded_file($tmp, $tpath1);

        //Thumb Image
        $thumbpath = 'images/thumbs/' . $job_image;
        $thumb_pic1 = create_thumb_image($tpath1, $thumbpath, '200', '200');

        $data = array(
            'user_id' => $get_method['user_id'],
            'cat_id' => $get_method['cat_id'],
            'city_id' => $get_method['city_id'],
            'job_type' => $get_method['job_type'],
            'job_name' => addslashes($get_method['job_name']),
            'job_designation' => addslashes($get_method['job_designation']),
            'job_desc' => addslashes($get_method['job_desc']),
            'job_salary' => $get_method['job_salary'],
            'job_salary_mode' => $get_method['job_salary_mode'],
            'job_company_name' => $get_method['job_company_name'],
            'job_company_website' => $get_method['job_company_website'],
            'job_phone_number' => $get_method['job_phone_number'],
            'job_country_code' => $get_method['job_country_code'],
            'job_mail' => $get_method['job_mail'],
            'job_vacancy' => $get_method['job_vacancy'],
            'job_address' => addslashes($get_method['job_address']),
            'job_qualification' => addslashes($get_method['job_qualification']),
            'job_skill' => addslashes($get_method['job_skill']),
            'job_experince' => addslashes($get_method['job_experince']),
            'job_work_day' => addslashes($get_method['job_work_day']),
            'job_work_time' => addslashes($get_method['job_work_time']),
            'job_map_latitude' => addslashes($get_method['job_map_latitude']),
            'job_map_longitude' => addslashes($get_method['job_map_longitude']),
            'job_image' => $job_image,
            'job_date' => strtotime($get_method['job_date'])
        );

    } else {
        $data = array(
            'user_id' => $get_method['user_id'],
            'cat_id' => $get_method['cat_id'],
            'city_id' => $get_method['city_id'],
            'job_type' => $get_method['job_type'],
            'job_name' => addslashes($get_method['job_name']),
            'job_designation' => addslashes($get_method['job_designation']),
            'job_desc' => addslashes($get_method['job_desc']),
            'job_salary' => $get_method['job_salary'],
            'job_salary_mode' => $get_method['job_salary_mode'],
            'job_company_name' => $get_method['job_company_name'],
            'job_company_website' => $get_method['job_company_website'],
            'job_phone_number' => $get_method['job_phone_number'],
            'job_country_code' => $get_method['job_country_code'],
            'job_mail' => $get_method['job_mail'],
            'job_vacancy' => $get_method['job_vacancy'],
            'job_address' => addslashes($get_method['job_address']),
            'job_qualification' => addslashes($get_method['job_qualification']),
            'job_skill' => addslashes($get_method['job_skill']),
            'job_experince' => addslashes($get_method['job_experince']),
            'job_work_day' => addslashes($get_method['job_work_day']),
            'job_work_time' => addslashes($get_method['job_work_time']),
            'job_map_latitude' => addslashes($get_method['job_map_latitude']),
            'job_map_longitude' => addslashes($get_method['job_map_longitude']),
            'job_date' => strtotime($get_method['job_date'])
        );
    }


    $job_edit = Update('tbl_jobs', $data, "WHERE id = '" . $get_method['job_id'] . "'");

    $set['JOBS_APP'][] = array('msg' => $app_lang['edit_job'], 'success' => '1');


    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_company_details") {

    $jsonObj = array();

    $user_id = $get_method['user_id'];

    $query = "SELECT * FROM tbl_company  WHERE tbl_company.`user_id`='$user_id' ORDER BY tbl_company.`id` DESC";

    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    while ($data = mysqli_fetch_assoc($sql)) {

        $row['id'] = $data['id'];
        $row['company_name'] = $data['company_name'];
        $row['company_email'] = $data['company_email'];
        $row['mobile_no'] = $data['mobile_no'];
        $row['company_country_code'] = $data['company_country_code'];
        $row['company_address'] = $data['company_address'];
        $row['company_desc'] = $data['company_desc'];
        $row['company_website'] = $data['company_website'];
        $row['company_work_day'] = $data['company_work_day'];
        $row['company_work_time'] = $data['company_work_time'];
        $row['commercial_registration_number'] = $data['commercial_registration_number'];
        $row['company_logo'] = $file_path . 'images/' . $data['company_logo'];

        array_push($jsonObj, $row);

    }

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "user_register") {
    $register_date = strtotime(date('d-m-Y h:i A'));

    $user_type = $get_method['user_type'];
    $working_type = $get_method['working_type'];

    $name = filter_var($get_method['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($get_method['email'], FILTER_SANITIZE_STRING);
    $password = md5(trim($get_method['password']));
    $phone = filter_var($get_method['phone'], FILTER_SANITIZE_STRING);
    $country_code = $get_method['country_code'];
    $device_token = $get_method['device_token'];
    $city = $get_method['city'];

    $qry = "SELECT * FROM tbl_users WHERE `email` = '" . $email . "' OR `phone` = '" . $phone . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) == 0) {
        if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) {
            $set['JOBS_APP'][] = array('msg' => $app_lang['invalid_email_format'], 'success' => '0');
        } else if ($row['email'] != "") {
            $set['JOBS_APP'][] = array('msg' => $app_lang['email_exist'], 'success' => '0');
        } else {

            $thisOtp = getAndSendOtp($country_code . $phone);
            if ($thisOtp) {
                $data = array(
                    'user_type' => $user_type,
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'phone' => $phone,
                    'country_code' => $country_code,
                    'city' => $city,
                    'register_date' => $register_date,
                    'working_type' => $working_type,
                    'device_token' => $device_token,
                    'status' => '0'
                );

                $qry = Insert('tbl_users', $data);

                $user_id = mysqli_insert_id($mysqli);

                if ($user_type == '2' and strcmp($get_method['register_as'], 'company') == 0) {

                    //registation as company
                    $sql = "SELECT * FROM tbl_company WHERE `user_id`='$user_id'";
                    $res = mysqli_query($mysqli, $sql);

                    $data = array(
                        'user_id' => $user_id,
                        'company_name' => addslashes(trim($get_method['company_name'])),
                        'company_email' => cleanInput(trim($get_method['company_email'])),
                        'mobile_no' => cleanInput(trim($get_method['mobile_no'])),
                        'company_country_code' => addslashes(trim($get_method['company_country_code'])),
                        'city' => addslashes(trim($get_method['city'])),
                        'company_desc' => addslashes($get_method['company_desc']),
                        'company_work_day' => addslashes(trim($get_method['company_work_day'])),
                        'company_work_time' => (trim($get_method['company_work_time'])),
                        'commercial_registration_number' => (trim($get_method['commercial_registration_number'])),
                        'company_website' => (trim($get_method['company_website']))

                    );

                    $qry = Insert('tbl_company', $data);

                }
                $to = $get_method['email'];
                $recipient_name = $get_method['name'];
                // subject
                $subject = str_replace('###', APP_NAME, $app_lang['register_mail_lbl']);
                $message = '<div style="background-color: #eee;" align="center"><br />
    							  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
    							    <tbody>
    							      <tr>
    							        <td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="logo" /></td>
    							      </tr>
    							      <br>
    							      <br>
    							      <tr>
    							        <td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
    							          <img src="' . $file_path . 'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
    							        </td>
    							      </tr>
    							      <tr>
    							        <td width="600" valign="top" bgcolor="#FFFFFF">
    							          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
    							            <tbody>
    							              <tr>
    							                <td valign="top">
    							                  <table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
    							                    <tbody>
    							                      <tr>
    							                        <td>
    							                        	<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>' . $app_lang['welcome_lbl'] . ', ' . addslashes(trim($get_method['name'])) . '</strong></p>
    							                          	<br>
    							                          	<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">' . $app_lang['normal_register_msg'] . '<br /></p>
    							                          	<br/>
    							                          	<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">' . $app_lang['thank_you_lbl'] . ' ' . APP_NAME . '</p>
    							                            </td>
    							                      </tr>
    							                    </tbody>
    							                  </table>
    							                </td>
    							              </tr>
    							            </tbody>
    							          </table>
    							        </td>
    							      </tr>
    							      <tr>
    							        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">' . $app_lang['email_copyright'] . ' ' . APP_NAME . '.</td>
    							      </tr>
    							    </tbody>
    							  </table>
    							</div>';

                send_email($to, $recipient_name, $subject, $message);

                //$set['JOBS_APP'][]=array('msg' => $app_lang['register_success'],'success'=>'1');
                $set['JOBS_APP'][] = array('thisOtp' => $thisOtp,'working_type' => $working_type, 'user_type' => $user_type, 'country_code' => $country_code, 'user_id' => $user_id, 'name' => $name, 'email' => $email, 'phone' => $phone, 'otp' => $thisOtp, 'success' => '1');
            } else {
                $set['JOBS_APP'][] = array('msg' => $app_lang['invalid_mobile_number'], 'success' => '0');
            }
        }
    } else {

        if ($row["email"] == $email) {

            $set['JOBS_APP'][] = array('msg' => $app_lang['email_exist'], 'success' => '0');
        }

        if ($row["phone"] == $phone) {
            $set['JOBS_APP'][] = array('msg' => $app_lang['phone_exist'], 'success' => '0');
        }
    }
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "add_to_wallet_init") {
    $date = time()*1000;
    $transaction_id = getGUIDnoHash();
    $type = 1; // for credit transaction only //1-credit, 2-debit
    $user_id = $get_method['user_id'];
    $amount = $get_method['amount'];
    $status = 4; //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
    $mode = $get_method['mode'];//for diffrent type of payment gateway diffrent payment methods.. stripe,hyperpay
    $bank_trans_id = null;//to be filled at update only
    $bank_trans_response = null;//to be filled at update only
    $trans_type = 1;//to be filled at update only 	1-bank, 2- wallet
    $updated_at = $date;

    $data = array(
        'transaction_id' => $transaction_id,
        'type' => $type,
        'user_id' => $user_id,
        'amount' => $amount,
        'status' => $status,
        'mode' => $mode,
        'bank_trans_id' => $bank_trans_id,
        'bank_trans_response' => $bank_trans_response,
        'trans_type' => $trans_type,//1-bank, 2- wallet
        'trans_for' => 1,//1- wallet 2- subscription
        'updated_at' => $updated_at
    );

    $qry = Insert('tbl_transaction_details', $data);
    $trans_id = mysqli_insert_id($mysqli);

    if ($trans_id) {
        $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'amount' => $amount, 'success' => '1');
    } else {
        $set['JOBS_APP'][] = array('msg' => "Update Failed!!!  Try Again", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "add_to_wallet_update") {
    $date = time()*1000;
    $transaction_id = $get_method['transaction_id'];
    $user_id = $get_method['user_id'];
    $status = $get_method['status']; //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
    $bank_trans_id = $get_method['bank_trans_id'];//to be filled at update only
    $bank_trans_response = $get_method['bank_trans_response'];//to be filled at update only
    $updated_at = $date;//to be filled at update only 	1-bank, 2- wallet

    if ($get_method['transaction_id'] != "" && $get_method['user_id'] != "" && $get_method['bank_trans_id'] != "") {

        $data = array(
            'status' => $status,
            'bank_trans_id' => $bank_trans_id,
            'bank_trans_response' => $bank_trans_response,
            'updated_at' => $updated_at
        );
        $user_edit = Update('tbl_transaction_details', $data, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");


        $qry = "SELECT * FROM tbl_transaction_details WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = $user_id AND `status` = 1 AND `user_updated` = 0 AND `trans_for` = 1";
        $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $current_wallet_amount = $row["amount"];
            mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` +$current_wallet_amount WHERE `id` = '$user_id'");
            // $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");

            $dataUpdate = array(
                'user_updated' => 1 //0- not updated, 1-updated
            );
            Update('tbl_transaction_details', $dataUpdate, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");

            $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'current_wallet_amount' => $current_wallet_amount, 'success' => '1');
        } else {
            $set['JOBS_APP'][] = array('msg' => "Transaction update Failed!!", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Transaction update Failed!!", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "withdrawal_from_wallet_init") {
    if ($get_method['user_id'] != "") {

        $user_id = filter_var($get_method['user_id'], FILTER_SANITIZE_STRING);
        $amount = filter_var($get_method['amount'], FILTER_SANITIZE_STRING);

        $query1 = "SELECT * FROM tbl_users WHERE id = $user_id";
        $sql1 = mysqli_query($mysqli, $query1) or die(mysqli_error($mysqli));
        if(mysqli_num_rows($sql1))
        {
            $row = mysqli_fetch_assoc($sql1);
            $account_number = $row["account_number"];
            $ifsc_code = $row["ifsc_code"];
            $account_holder_name = $row["account_holder_name"];
            $linked_mobile = $row["linked_mobile"];
            $current_wallet_amount = $row["current_wallet_amount"];

            if($account_number && $ifsc_code && $account_holder_name && $linked_mobile)
            {
                if($current_wallet_amount>0)
                {
                    if($amount<=$current_wallet_amount)
                    {
                        $date = time()*1000;
                        $transaction_id = getGUIDnoHash();
                        $type = 2; // for credit transaction only //1-credit, 2-debit
                        $status = 4; //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
                        $mode = "";//for diffrent type of payment gateway diffrent payment methods.. stripe,hyperpay
                        $bank_trans_id = null;//to be filled at update only
                        $bank_trans_response = null;//to be filled at update only
                        $trans_type = 1;//to be filled at update only 	1-bank, 2- wallet
                        $updated_at = $date;

                        $data = array(
                            'transaction_id' => $transaction_id,
                            'type' => $type,
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'status' => $status,
                            'mode' => $mode,
                            'bank_trans_id' => $bank_trans_id,
                            'bank_trans_response' => $bank_trans_response,
                            'trans_type' => $trans_type,//1-bank, 2- wallet
                            'trans_for' => 1,//1- wallet 2- subscription
                            'updated_at' => $updated_at
                        );

                        $qry = Insert('tbl_transaction_details', $data);

                        $trans_id = mysqli_insert_id($mysqli);

                        if ($trans_id) {
                            $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'amount' => $amount, 'success' => '1');
                        } else {
                            $set['JOBS_APP'][] = array('msg' => "Update Failed!!!  Try Again", 'success' => '0');
                        }

                        /*$data = array(
                            "account_number" => $account_number,
                            "ifsc_code" => $ifsc_code,
                            "account_holder_name" => $account_holder_name,
                            "linked_mobile" => $linked_mobile
                        );
                        $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");
                        $set['JOBS_APP'][] = array('status' => "Account Details Successfully Updated", 'success' => '1');*/

                    }else{
                        $set['JOBS_APP'][] = array('msg' => "Failed !!  Invalid Amount", 'success' => '0');
                    }

                }else{
                    $set['JOBS_APP'][] = array('msg' => "Failed !! Wallet Is Empty", 'success' => '0');
                }
            }else{
                $set['JOBS_APP'][] = array('msg' => "Failed !! Account Details Invalid", 'success' => '0');
            }
        }else{
            $set['JOBS_APP'][] = array('msg' => "Failed !! User Details Not Valid", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed !! Please Provide User Details", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "withdrawal_from_wallet_update") {
    if ($get_method['transaction_id'] != "") {

        $transaction_id = filter_var($get_method['transaction_id'], FILTER_SANITIZE_STRING);
        $status = filter_var($get_method['status'], FILTER_SANITIZE_STRING);
        $bank_trans_id = filter_var($get_method['bank_trans_id'], FILTER_SANITIZE_STRING);
        $bank_trans_response = filter_var($get_method['bank_trans_response'], FILTER_SANITIZE_STRING);
        $updated_at = time()*1000;

        $query1 = "SELECT * FROM tbl_transaction_details WHERE transaction_id = $transaction_id AND trans_type = 1 AND  type = 2 AND status = 4 AND `user_updated` = 0 AND `trans_for`=1";
        $sql1 = mysqli_query($mysqli, $query1) or die(mysqli_error($mysqli));
        if(mysqli_num_rows($sql1))
        {
            $row = mysqli_fetch_assoc($sql1);
            $amount = $row["amount"];
            $user_id = $row["user_id"];
            $query2 = "SELECT * FROM tbl_users WHERE id = $user_id";
            $sql2 = mysqli_query($mysqli, $query2) or die(mysqli_error($mysqli));
            $row2 = mysqli_fetch_assoc($sql2);
            $current_wallet_amount = $row2["current_wallet_amount"];

            if($amount<=$current_wallet_amount)
            {
                $data = array(
                    'status' => $status,
                    'bank_trans_id' => $bank_trans_id,
                    'bank_trans_response' => $bank_trans_response,
                    'updated_at' => $updated_at
                );

                $user_edit = Update('tbl_transaction_details', $data, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");

                $qry = "SELECT * FROM tbl_transaction_details WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = $user_id AND `status` = 1 AND `user_updated` = 0 AND `trans_for`=1";
                $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $thisAmount = $row["amount"];
                    mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` -$thisAmount WHERE `id` = '$user_id'");

                    $dataUpdate = array(
                        'user_updated' => 1 //0- not updated, 1-updated
                    );

                    Update('tbl_transaction_details', $dataUpdate, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");

                    $query5 = "SELECT * FROM tbl_users WHERE id = $user_id";
                    $sqlw = mysqli_query($mysqli, $query5) or die(mysqli_error($mysqli));

                    $rows = mysqli_fetch_assoc($sqlw);
                    $current_wallet_amount_updated = $rows["current_wallet_amount"];

                    $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'current_wallet_amount' => $current_wallet_amount_updated, 'success' => '1');
                } else {
                    $set['JOBS_APP'][] = array('msg' => "Transaction update Failed!!", 'success' => '0');
                }

            }else{
                $set['JOBS_APP'][] = array('msg' => "Failed !!  Invalid Amount", 'success' => '0');
            }

        }else{
            $set['JOBS_APP'][] = array('msg' => "Failed !! Transaction Details Not Valid", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed !! Please Provide Transaction Details", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "wallet_transaction_list") {
    $user_id = filter_var($get_method['user_id'], FILTER_SANITIZE_STRING);

    $jsonObjCredit = array();
    $jsonObjDebit = array();
    if($user_id!="")
    {
        $queryUser = "SELECT current_wallet_amount FROM tbl_users  WHERE  `id`= $user_id";
        $sqlUser = mysqli_query($mysqli, $queryUser) or die(mysqli_error($mysqli));
        if(mysqli_num_rows($sqlUser))
        {
            $rowUser = mysqli_fetch_assoc($sqlUser);
            $current_wallet_amount = $rowUser["current_wallet_amount"];
            $query = "SELECT * FROM tbl_transaction_details  WHERE  `trans_for`=1 AND `user_id`= $user_id 
	 		 ORDER BY `id` DESC";
            $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
            if(mysqli_num_rows($sql))
            {
                while ($data = mysqli_fetch_assoc($sql)) {
                    $row['type'] = $data['type'];//1-credit, 2-debit
                    $row['amount'] = $data['amount'];
                    $row['status'] = $data['status'];//0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
                    $row['mode'] = $data['mode'];//diffrent payment methods.. stripe,hyperpay
                    $row['bank_trans_id'] = $data['bank_trans_id'];//diffrent payment methods.. stripe,hyperpay
                    $row['bank_trans_response'] = $data['bank_trans_response'];//diffrent payment methods.. stripe,hyperpay
                    $row['trans_type'] = $data['trans_type'];//1-bank, 2- wallet
                    $row['timestamp'] = $data['updated_at'];
                    $row['commission'] = $data['commission'];

                    if($data['type'] == 1)
                    {
                        array_push($jsonObjCredit, $row);
                    }else if($data['type'] == 2)
                    {
                        array_push($jsonObjDebit, $row);
                    }
                }

                $set['JOBS_APP']["debit"] = $jsonObjDebit;
                $set['JOBS_APP']["credit"] = $jsonObjCredit;
                $set['JOBS_APP']["current_wallet_amount"] = $current_wallet_amount;
                $set['JOBS_APP']["success"] = 1;
            }else{
                $set['JOBS_APP'][] = array('msg' => "No Transaction Data Available", 'success' => '0');
            }
        }else{
            $set['JOBS_APP'][] = array('msg' => "Please Provide Valid User Details", 'success' => '0');
        }
    }else{
        $set['JOBS_APP'][] = array('msg' => "Please Provide Valid User Details", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "subscription_plan_list") {

    $jsonObj = array();

    $querySubPlan = "SELECT * FROM tbl_subscription_plan WHERE  status = 1";
    $sqlSubPlan = mysqli_query($mysqli, $querySubPlan) or die(mysqli_error($mysqli));
    if(mysqli_num_rows($sqlSubPlan))
    {
        while ($data = mysqli_fetch_assoc($sqlSubPlan))
        {
            $row['name'] = $data["name"];
            $row['price'] = $data['price'];
            $row['credits'] = $data['credits'];
            $row['plan_id'] = $data['id'];
            array_push($jsonObj, $row);
        }
        $set['JOBS_APP'] = $jsonObj;

    }else{
        $set['JOBS_APP'][] = array('msg' => "No Data Available", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_location_list") {

    $jsonObj = array();

    $country_qry = "SELECT `id`, `name`, `country_code` FROM tbl_country WHERE status= 1 ORDER BY `name` ASC";
    $resultCountry = mysqli_query($mysqli, $country_qry);

    $wholeData= array();
    while ($dataCountry = mysqli_fetch_assoc($resultCountry))
    {
        $countryId = $dataCountry["id"];
        $countryName = $dataCountry["name"];
        $countryCode = $dataCountry["country_code"];

        $region_qry = "SELECT `id`, `name` FROM tbl_region WHERE status= 1 AND country_id = $countryId ORDER BY `name` ASC";
        $resultRegion = mysqli_query($mysqli, $region_qry);

        $regionData= array();
        while ($dataRegion = mysqli_fetch_assoc($resultRegion))
        {
            $regionId = $dataRegion["id"];
            $regionName = $dataRegion["name"];



            $city_qry = "SELECT `c_id`, `city_name`,`country_id`,`region_id` FROM tbl_city WHERE status= 1 AND country_id = $countryId AND region_id = $regionId ORDER BY `city_name` ASC";
            $resultCity = mysqli_query($mysqli, $city_qry);

            $cityData= array();
            while ($dataCity = mysqli_fetch_assoc($resultCity))
            {
                $cityId = $dataCity["c_id"];
                $cityName = $dataCity["city_name"];
                $country_id = $dataCity["country_id"];
                $region_id = $dataCity["region_id"];

                $cityData[] = array("id"=>$cityId,"name"=>$cityName);
            }

            $regionData[] = array("id"=>$regionId,"name"=>$regionName,"cities"=>$cityData);
        }

        $wholeData["country"][] = array("id"=>$countryId,"name"=>$countryName,"country_code"=>$countryCode,"regions"=>$regionData);
    }




    if($wholeData)
    {

        $set['JOBS_APP'] = $wholeData;

    }else{
        $set['JOBS_APP'][] = array('msg' => "No Data Available", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "set_user_ratings") {

    if ($get_method['user_id'] && $get_method['reviewer_id'] && $get_method['rating'] && $get_method['review'])
    {
        $review = $get_method['review'];
        $rating = $get_method['rating'];
        $reviewer_id = $get_method['reviewer_id'];
        $user_id = $get_method['user_id'];

        $data = array(
            'user_id' => $user_id,
            'reviewer_id' => $reviewer_id,
            'rating' => $rating,
            'review' => $review
        );
        $qry = Insert('tbl_reviews_ratings', $data);
        $entry_id = mysqli_insert_id($mysqli);

        if ($entry_id) {
            $set['JOBS_APP'][] = array('user_id' => $user_id, 'reviewer_id' => $reviewer_id, 'rating' => $rating, 'review' => $review, 'success' => '1');
        } else {
            $set['JOBS_APP'][] = array('msg' => "Failed!!!  Try Again", 'success' => '0');
        }

    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed!!! Invalid Data Provided", 'success' => '0');
    }


    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


}
else if ($get_method['method_name'] == "subscription_payment_init") {
    $date = time()*1000;
    $transaction_id = getGUIDnoHash();
    $type = 1; // for credit transaction only //1-credit, 2-debit
    $user_id = $get_method['user_id'];
    $plan_id = $get_method['plan_id'];
    $status = 4; //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
    $mode = $get_method['mode']; //for diffrent type of payment gateway diffrent payment methods.. stripe,hyperpay
    $bank_trans_id = null;//to be filled at update only
    $bank_trans_response = null;//to be filled at update only
    $trans_type = 1;//to be filled at update only 	1-bank, 2- wallet
    $trans_for = 2;//1- wallet 2- subscription
    $updated_at = $date;


    $qryPlan = "SELECT * FROM tbl_subscription_plan WHERE `id` = '" . $plan_id . "' AND `status` = 1";
    $resultPlan = mysqli_query($mysqli, $qryPlan) or die('Error in fetch data ->' . mysqli_error($mysqli));

    if (mysqli_num_rows($resultPlan) > 0)
    {
        $rowPlan = mysqli_fetch_assoc($resultPlan);
        $amount = $rowPlan["price"];

        $data = array(
            'transaction_id' => $transaction_id,
            'type' => $type,
            'user_id' => $user_id,
            'amount' => $amount,
            'status' => $status,
            'mode' => $mode,
            'bank_trans_id' => $bank_trans_id,
            'bank_trans_response' => $bank_trans_response,
            'trans_type' => $trans_type,
            'trans_for' => $trans_for,
            'plan_id' => $plan_id,
            'updated_at' => $updated_at
        );

        $qry = Insert('tbl_transaction_details', $data);
        $trans_id = mysqli_insert_id($mysqli);

        if ($trans_id) {
            $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'amount' => $amount, 'success' => '1');
        } else {
            $set['JOBS_APP'][] = array('msg' => "Failed!!!  Try Again", 'success' => '0');
        }

    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed!!! invalid Plan Selected", 'success' => '0');
    }


    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "subscription_payment_update") {
    $date = time()*1000;
    $transaction_id = $get_method['transaction_id'];
    $plan_id = $get_method["plan_id"];
    $user_id = $get_method["user_id"];
    $status = $get_method['status']; //0-null, 1-approved, 2-rejected, 3-failed, 4-init/pending
    $bank_trans_id = $get_method['bank_trans_id'];//to be filled at update only
    $bank_trans_response = $get_method['bank_trans_response'];//to be filled at update only
    $updated_at = $date;

    if ($get_method['plan_id'] != "" && $get_method['transaction_id'] != "" && $get_method['user_id'] != "" && $get_method['bank_trans_id'] != "")
    {
        $data = array(
            'status' => $status,
            'bank_trans_id' => $bank_trans_id,
            'bank_trans_response' => $bank_trans_response,
            'updated_at' => $updated_at
        );
        $user_edit = Update('tbl_transaction_details', $data, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=2");

        $qry = "SELECT * FROM tbl_transaction_details WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = $user_id AND `status` = 1 AND `user_updated` = 0 AND `trans_for` = 2";
        $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

        if (mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_assoc($result);


            $qryPlan = "SELECT * FROM tbl_subscription_plan WHERE `id` = '" . $plan_id . "' AND `status` = 1";
            $resultPlan = mysqli_query($mysqli, $qryPlan) or die('Error in fetch data ->' . mysqli_error($mysqli));

            if (mysqli_num_rows($resultPlan) > 0)
            {
                $rowPlan = mysqli_fetch_assoc($resultPlan);
                $credits_remaining = $rowPlan["credits"];
                $subscription_plan_id = $rowPlan["id"];
                $dataUpdate = array(
                    'subscription_plan_id' => $subscription_plan_id,
                    'credits_remaining' => $credits_remaining
                );
                Update('tbl_users', $dataUpdate, "WHERE `id` = '" . $user_id . "'");

                $data = array(
                    'transaction_id' => $transaction_id,
                    'user_id' => $user_id,
                    'plan_id' => $plan_id
                );

                $qryisd = Insert('tbl_user_subscription_details', $data);

                $dataUpdate = array(
                    'user_updated' => 1 //0- not updated, 1-updated
                );
                Update('tbl_transaction_details', $dataUpdate, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=2");

                $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'credits_remaining' => $credits_remaining, 'subscription_plan_id' => $subscription_plan_id, 'success' => '1');
            } else {
                $set['JOBS_APP'][] = array('msg' => "Transaction Failed!!", 'success' => '0');
            }
        } else {
            $set['JOBS_APP'][] = array('msg' => "Failed!!, invalid Transaction Data", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid Data", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "job_completed") {

    $date = time()*1000;
    $transaction_id = getGUIDnoHash();
    $job_id = $get_method['job_id'];
    $updated_at = $date;

    if ($get_method['job_id'] != "") {

        $qry = "SELECT user_id,job_salary,job_salary_mode,user_alloted FROM tbl_jobs WHERE `id` = '" . $job_id . "' AND `job_status` = 4 AND `status` = 1";
        $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));
        if(mysqli_num_rows($result))
        {
            $rowResult = mysqli_fetch_assoc($result);
            $job_salary = $rowResult["job_salary"];
            $job_salary_mode = $rowResult["job_salary_mode"];
            $user_id = $rowResult["user_id"];
            $user_alloted = $rowResult["user_alloted"];

            $qry1 = "SELECT `name`,current_wallet_amount FROM tbl_users WHERE `id` = '" . $user_id . "'";
            $result1 = mysqli_query($mysqli, $qry1) or die('Error in fetch data ->' . mysqli_error($mysqli));
            if(mysqli_num_rows($result1)) {
                $rowResult1 = mysqli_fetch_assoc($result1);
                $current_wallet_amount = $rowResult1["current_wallet_amount"];
                $provider_name = $rowResult1["name"];


                $qry2 = "SELECT `name` FROM tbl_users WHERE `id` = '" . $user_alloted . "'";
                $result2 = mysqli_query($mysqli, $qry2) or die('Error in fetch data ->' . mysqli_error($mysqli));
                $row2 = mysqli_fetch_assoc($result2);
                $seeker_name = $row2["name"];


                if($current_wallet_amount>=$job_salary)
                {
                    $data = array(
                        'job_status' => 1,
                        'job_end_time' => time()*1000
                    );
                    Update('tbl_jobs', $data, "WHERE `id` = '" . $job_id . "' AND `user_alloted` = '" . $user_alloted . "'");

                    $data = array(
                        'job_end_time' => time()*1000
                    );
                    Update('tbl_apply', $data, "WHERE `job_id` = '" . $job_id . "' AND `user_id` = '" . $user_id . "'");

                    mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` -$job_salary WHERE `id` = '$user_id'");
                    mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` +$job_salary WHERE `id` = '$user_alloted'");

                    $fcmMessage = "Part Time: Job Completed";
                    $fcmBody = "Your Job completed and payment proceeded!!";
                    $fcmClickIntent = "seeker_job_completed";
                    sendFcmNotification($user_alloted,$fcmMessage,$fcmBody,$fcmClickIntent);

                    $data1 = array(
                        'transaction_id' => $transaction_id,
                        'type' => 2,
                        'user_id' => $user_id,
                        'amount' => $job_salary,
                        'status' => 1,
                        'user_updated' => 1,
                        'mode' => "",
                        'bank_trans_id' => $seeker_name,
                        'bank_trans_response' => $user_alloted,
                        'trans_type' => 2,
                        'trans_for' => 1,
                        'job_id' => $job_id,
                        'updated_at' => $updated_at
                    );

                    $qry1 = Insert('tbl_transaction_details', $data1);
                    $trans_id1 = mysqli_insert_id($mysqli);



                    $query = "SELECT * FROM tbl_settings WHERE `id`='1'";
                    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
                    $data = mysqli_fetch_assoc($sql);
                    $commission = $data['commission'];
                    $commissionAmount = (($commission/100)*$job_salary);
                    $finalAmount = $job_salary - $commissionAmount;

                    $data2 = array(
                        'transaction_id' => $transaction_id,
                        'type' => 1,
                        'user_id' => $user_alloted,
                        'amount' => $finalAmount,
                        'user_updated' => 1,
                        'commission' => $commissionAmount,
                        'status' => 1,
                        'mode' => "",
                        'bank_trans_id' => $provider_name,
                        'bank_trans_response' => $user_id,
                        'trans_type' => 2,
                        'job_id' => $job_id,
                        'trans_for' => 1,
                        'updated_at' => $updated_at
                    );

                    $qry2 = Insert('tbl_transaction_details', $data2);
                    $trans_id2 = mysqli_insert_id($mysqli);
                    $set['JOBS_APP'][] = array('msg' => "Job updated Successfully", 'success' => '1');

                }else{
                    $set['JOBS_APP'][] = array('msg' => "Failed!! Insufficient Wallet Amount", 'success' => '0');
                }
            }else{
                $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid user", 'success' => '0');
            }
            /*$data = array(
                'status' => $status,
                'bank_trans_id' => $bank_trans_id,
                'bank_trans_response' => $bank_trans_response,
                'updated_at' => $updated_at
            );
            $user_edit = Update('tbl_transaction_details', $data, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");




            $qry = "SELECT * FROM tbl_transaction_details WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = $user_id AND `status` = 1 AND `user_updated` = 0 AND `trans_for`=1";
            $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $current_wallet_amount = $row["amount"];
                mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` +$current_wallet_amount WHERE `id` = '$user_id'");
                //$user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");

                $dataUpdate = array(
                    'user_updated' => 1 //0- not updated, 1-updated
                );
                Update('tbl_transaction_details', $dataUpdate, "WHERE `transaction_id` = '" . $transaction_id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");

                $set['JOBS_APP'][] = array('transaction_id' => $transaction_id, 'current_wallet_amount' => $current_wallet_amount, 'success' => '1');
            } else {
                $set['JOBS_APP'][] = array('msg' => "Transaction update Failed!!", 'success' => '0');
            }*/
        } else {
            $set['JOBS_APP'][] = array('msg' => "Failed!! Job Not Found", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid Job Details", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "extend_job_date") {

    if ($get_method['job_id'] != "") {

        $job_id = $get_method['job_id'];
        $job_end_time = $get_method['job_end_time'];

        $qry = "SELECT * FROM tbl_jobs WHERE `id` = '" . $job_id . "'";
        $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));
        if(mysqli_num_rows($result))
        {
            $rowResult = mysqli_fetch_assoc($result);
            $current_job_end_time = $rowResult["job_end_time"];
            $user_alloted = $rowResult["user_alloted"];

            if($job_end_time>$current_job_end_time)
            {


            $data = array(
                'job_end_time' => $current_job_end_time
            );
            Update('tbl_jobs', $data, "WHERE `id` = '" . $job_id . "' AND `user_alloted` = '" . $user_alloted . "'");

            $fcmMessage = "Part Time: Job Time Extended";
            $fcmBody = "Your Job Time has been Extended!!";
            $fcmClickIntent = "seeker_job_awarded";
            sendFcmNotification($user_alloted,$fcmMessage,$fcmBody,$fcmClickIntent);
                $set['JOBS_APP'][] = array('status' => "Job Time Sucessfully Extended", 'success' => '1');
            }else{
                $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid Date Time Provided", 'success' => '0');
            }
        } else {
            $set['JOBS_APP'][] = array('msg' => "Failed!! Job Not Found", 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed!! Invalid Job Details", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "resend_otp") {

    $phone = filter_var($get_method['phone'], FILTER_SANITIZE_STRING);
    $country_code = $get_method['country_code'];

    $thisOtp = getAndSendOtp($country_code . $phone);
    $set['JOBS_APP'][] = array('otp' => $thisOtp, 'success' => '1');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "verify_user") {

    $user_id = filter_var($get_method['user_id'], FILTER_SANITIZE_STRING);

    if ($get_method['user_id'] != "") {
        $data = array("status" => 2);
        $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");
        $set['JOBS_APP'][] = array('status' => "User Successfully Verified", 'success' => '1');
    } else {
        $set['JOBS_APP'][] = array('msg' => "Verification Failed", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "submit_account_details") {
    if ($get_method['user_id'] != "" && $get_method['account_number'] != "" && $get_method['account_holder_name'] != "" && $get_method['linked_mobile'] != "") {

        $account_number = filter_var($get_method['account_number'], FILTER_SANITIZE_STRING);
        $ifsc_code = filter_var($get_method['ifsc_code'], FILTER_SANITIZE_STRING);
        $account_holder_name = filter_var($get_method['account_holder_name'], FILTER_SANITIZE_STRING);
        $linked_mobile = filter_var($get_method['linked_mobile'], FILTER_SANITIZE_STRING);
        $user_id = filter_var($get_method['user_id'], FILTER_SANITIZE_STRING);

        $data = array(
            "account_number" => $account_number,
            "ifsc_code" => $ifsc_code,
            "account_holder_name" => $account_holder_name,
            "linked_mobile" => $linked_mobile
        );
        $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");
        $set['JOBS_APP'][] = array('msg' => "Account Details Successfully Updated", 'success' => '1');
    } else {
        $set['JOBS_APP'][] = array('msg' => "Failed !! Please Enter All Fields", 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_login") {

    $email = cleanInput($get_method['email']);
    $password = trim($get_method['password']);
    $device_token = trim($get_method['device_token']);

    $qry = "SELECT * FROM tbl_users WHERE `email` = '$email'";
    $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['user_image']) {
            $user_image = $file_path . 'images/' . $row['user_image'];
        } else {
            $user_image = '';
        }
        if ($row['status'] == '2') {

            if ($row['password'] == md5($password)) {

                $data = array("device_token" => $device_token);
                $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $row['id'] . "'");

                $set['JOBS_APP'][] = array('working_type' => $row['working_type'], 'current_wallet_amount' => $row['current_wallet_amount'], 'credits_remaining' => $row['credits_remaining'], 'subscription_plan_id' => $row['subscription_plan_id'], 'account_number' => $row['account_number'], 'ifsc_code' => $row['ifsc_code'], 'account_holder_name' => $row['account_holder_name'], 'account_holder_name' => $row['account_holder_name'], 'linked_mobile' => $row['linked_mobile'], 'user_type' => $row['user_type'], 'user_id' => $row['id'], 'name' => $row['name'], 'user_image' => $user_image, 'success' => '1');
            } else {
                $set['JOBS_APP'][] = array('msg' => $app_lang['invalid_password'], 'success' => '0');
            }
        } elseif ($row['status'] == '1') {
            // account is deactivated
            $set['JOBS_APP'][] = array('msg' => $app_lang['account_deactive'], 'success' => '0');
        } else {
            // account is not verified
            $set['JOBS_APP'][] = array('msg' => $app_lang['account_invalid'], 'success' => '0');
        }
    } else {
        $set['JOBS_APP'][] = array('msg' => $app_lang['email_not_found'], 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_profile") {

    $qry = "SELECT * FROM tbl_users WHERE `id` = '" . $get_method['id'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);

    if ($row['user_image']) {
        $user_image = $file_path . 'images/' . $row['user_image'];
    } else {
        $user_image = '';
    }

    if ($row['user_resume']) {
        $user_resume = $file_path . 'uploads/' . $row['user_resume'];
    } else {
        $user_resume = '';
    }

    $skills = $row['skills'] ? $row['skills'] : '';
    $gender = $row['gender'] ? $row['gender'] : '';

    if (is_null($row['date_of_birth'])) {

        $date_of_birth = '';

    } else {

        $date_of_birth = date('d-m-Y', $row['date_of_birth']);
    }

    $qry_company = "SELECT * FROM tbl_company WHERE `user_id` = '" . $get_method['id'] . "'";
    $result_company = mysqli_query($mysqli, $qry_company);
    $num_rows = mysqli_num_rows($result_company);
    $row_company = mysqli_fetch_assoc($result_company);

    if ($row_company['company_logo']) {
        $company_logo = $file_path . 'images/' . $row_company['company_logo'];
    } else {
        $company_logo = '';
    }

    if ($num_rows > 0) {
        $register_as = 'company';
    } else {
        $register_as = 'individual';
    }

    $company_name = $row_company['company_name'] ? $row_company['company_name'] : '';
    $company_email = $row_company['company_email'] ? $row_company['company_email'] : '';
    $mobile_no = $row_company['mobile_no'] ? $row_company['mobile_no'] : '';
    $company_country_code = $row_company['company_country_code'] ? $row_company['company_country_code'] : '';
    $company_address = $row_company['company_address'] ? $row_company['company_address'] : '';
    $company_website = $row_company['company_website'] ? $row_company['company_website'] : '';
    $company_work_day = $row_company['company_work_day'] ? $row_company['company_work_day'] : '';
    $company_work_time = $row_company['company_work_time'] ? $row_company['company_work_time'] : '';
    $commercial_registration_number = $row_company['commercial_registration_number'] ? $row_company['commercial_registration_number'] : '';
    $company_desc = $row_company['company_desc'] ? $row_company['company_desc'] : '';
    $previousJobs = array();
    if($get_method['job_profile'])
    {

        $qry_job = "SELECT * FROM tbl_jobs WHERE job_status = 1 AND `user_alloted`='" . $get_method['id'] . "'";
        $query1 = mysqli_query($mysqli, $qry_job);
        $num_rows1 = mysqli_num_rows($query1);
        if($num_rows1)
        {
            while ($row_job = mysqli_fetch_assoc($query1))
            {
                $previousJobs[] = $row_job;
            }
        }
    }


    $qryReviews = "SELECT tbl_reviews_ratings.*,tbl_users.name as reviewer_name FROM tbl_reviews_ratings,tbl_users WHERE  tbl_users.id = tbl_reviews_ratings.reviewer_id AND `user_id` = '" . $get_method['id'] . "'";
    $resultReviews = mysqli_query($mysqli, $qryReviews);
    $rating_reviews = array();
    while($row_reviews = mysqli_fetch_assoc($resultReviews))
    {
        $rating_reviews[]=$row_reviews;
    }

    $set['JOBS_APP'][] = array('rating_reviews' => $rating_reviews,'previous_jobs' => $previousJobs,'user_availability' => $row['user_availability'],'user_id' => $row['id'], 'working_type' => $row['working_type'], 'user_type' => $row['user_type'], 'name' => $row['name'], 'email' => $row['email'], 'country_code' => $row['country_code'], 'phone' => $row['phone'], 'date_of_birth' => $date_of_birth, 'gender' => $gender, 'city' => $row['city'], 'address' => stripslashes($row['address']), 'current_company_name' => stripslashes($row['current_company_name']), 'experiences' => stripslashes($row['experiences']), 'skills' => $skills, 'user_image' => $user_image, 'user_resume' => $user_resume, 'total_apply_job' => apply_job_count($row['id']), 'total_saved_job' => saved_job_count($row['id']), 'register_as' => $register_as, 'company_name' => $company_name, 'company_email' => $company_email, 'mobile_no' => $mobile_no, 'company_country_code' => $company_country_code, 'company_address' => $company_address, 'company_desc' => $company_desc, 'company_website' => $company_website, 'company_work_day' => $company_work_day, 'company_work_time' => $company_work_time, 'commercial_registration_number' => $commercial_registration_number, 'company_logo' => $company_logo, 'success' => '1');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "user_profile_update") {
    $path = '';

    $qry = "SELECT * FROM tbl_users WHERE `email` = '" . $get_method['email'] . "'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);

    $user_id = $get_method['user_id'];
    $working_type = $get_method['working_type'];

    if ($_FILES['user_image']['name'] != '') {

        if ($row['user_image'] != "") {
            unlink('images/' . $row['user_image']);
        }

        $user_image = rand(0, 99999) . "_" . $_FILES['user_image']['name'];

        //Main Image
        $tpath1 = 'images/' . $user_image;
        $pic1 = compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
        $path = $file_path . $tpath1;
    } else {
        $user_image = '';
    }

    if ($_FILES['user_resume']['name'] != '') {

        $img_res1 = mysqli_query($mysqli, 'SELECT * FROM tbl_users WHERE `id`=' . $user_id . '');
        $img_res_row1 = mysqli_fetch_assoc($img_res1);

        if ($img_res_row1['user_resume'] != "") {
            unlink('uploads/' . $img_res_row1['user_resume']);
        }
        $user_resume = rand(0, 99999) . "_" . $_FILES['user_resume']['name'];

        //Main Image
        $tpath1 = 'uploads/' . $user_resume;
        move_uploaded_file($_FILES["user_resume"]["tmp_name"], "uploads/" . $user_resume);

    } else {
        $user_resume = $row['user_resume'];
    }


    if (!filter_var($get_method['email'], FILTER_VALIDATE_EMAIL)) {
        $set['JOBS_APP'][] = array('msg' => $app_lang['invalid_email_format'], 'success' => '0');

        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($set);
        echo $json;
        exit;
    } else if ($row['email'] == $get_method['email'] and $row['id'] != $user_id) {
        $set['JOBS_APP'][] = array('msg' => $app_lang['email_exist'], 'success' => '0');

        header('Content-Type: application/json; charset=utf-8');
        $json = json_encode($set);
        echo $json;
        exit;
    } else if ($get_method['password'] != "") {
        $data = array(
            'name' => $get_method['name'],
            'email' => $get_method['email'],
            'working_type' => $working_type,
            'user_availability' => $get_method['user_availability'],
            'phone' => $get_method['phone'],
            'country_code' => $get_method['country_code'],
            'city' => $get_method['city'],
            'address' => addslashes($get_method['address']),
            'user_image' => $user_image,
            'user_resume' => $user_resume,
            'current_company_name' => addslashes($get_method['current_company_name']),
            'experiences' => addslashes($get_method['experiences']),
            'skills' => $get_method['skills'],
            'gender' => $get_method['gender'],
            'date_of_birth' => strtotime($get_method['date_of_birth'])
        );
    } else {
        $data = array(
            'name' => $get_method['name'],
            'email' => $get_method['email'],
            'phone' => $get_method['phone'],
            'country_code' => $get_method['country_code'],
            'working_type' => $working_type,
            'user_availability' => $get_method['user_availability'],
            'city' => $get_method['city'],
            'address' => addslashes($get_method['address']),
            'user_image' => $user_image,
            'user_resume' => $user_resume,
            'current_company_name' => addslashes($get_method['current_company_name']),
            'experiences' => addslashes($get_method['experiences']),
            'skills' => $get_method['skills'],
            'gender' => $get_method['gender'],
            'date_of_birth' => strtotime($get_method['date_of_birth'])
        );
    }

    if ($get_method['password'] != "") {
        $data = array_merge($data, array("password" => md5(trim($get_method['password']))));
    }

    $user_edit = Update('tbl_users', $data, "WHERE `id` = '" . $user_id . "'");

    if ($_FILES['company_logo']['name'] != "") {

        $img_res_company = mysqli_query($mysqli, 'SELECT * FROM tbl_company WHERE `id`=' . $user_id . '');
        $img_res_row_company = mysqli_fetch_assoc($img_res_company);

        if ($img_res_row_company['company_logo'] != "") {
            unlink('images/' . $img_res_row_company['company_logo']);
        }

        $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);

        $company_logo = rand(0, 99999) . "." . $ext;

        //Main Image
        $tpath = 'images/' . $company_logo;

        if ($ext != 'png') {
            $pic1 = compress_image($_FILES["company_logo"]["tmp_name"], $tpath, 80);
        } else {
            $tmp = $_FILES['company_logo']['tmp_name'];
            move_uploaded_file($tmp, $tpath);
        }

        $data = array(
            'company_name' => addslashes(trim($get_method['company_name'])),
            'company_email' => $get_method['company_email'],
            'mobile_no' => $get_method['mobile_no'],
            'company_country_code' => $get_method['company_country_code'],
            'company_address' => addslashes($get_method['company_address']),
            'company_desc' => addslashes($get_method['company_desc']),
            'company_website' => $get_method['company_website'],
            'company_work_day' => addslashes(trim($get_method['company_work_day'])),
            'company_work_time' => $get_method['company_work_time'],
            'commercial_registration_number' => $get_method['commercial_registration_number'],
            'company_logo' => $company_logo

        );

    } else {

        $data = array(
            'company_name' => addslashes(trim($get_method['company_name'])),
            'company_email' => $get_method['company_email'],
            'mobile_no' => $get_method['mobile_no'],
            'company_country_code' => $get_method['company_country_code'],
            'company_address' => addslashes($get_method['company_address']),
            'company_desc' => addslashes($get_method['company_desc']),
            'company_work_day' => addslashes(trim($get_method['company_work_day'])),
            'company_work_time' => $get_method['company_work_time'],
            'commercial_registration_number' => $get_method['commercial_registration_number'],
            'company_website' => $get_method['company_website']
        );
    }

    $user_edit = Update('tbl_company', $data, " WHERE `user_id` = '" . $user_id . "'");

    $set['JOBS_APP'][] = array('user_image' => $path, 'msg' => $app_lang['update_success'], 'success' => '1');

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();

}
else if ($get_method['method_name'] == "forgot_pass") {

    $email = htmlentities(trim($get_method['email']));

    $qry = "SELECT * FROM tbl_users WHERE `email` = '$email'";
    $result = mysqli_query($mysqli, $qry);
    $row = mysqli_fetch_assoc($result);

    if ($result->num_rows > 0) {
        $password = generateRandomPassword(7);

        $new_password = md5($password);

        $to = $row['email'];
        $recipient_name = $row['name'];
        // subject
        $subject = '[IMPORTANT] ' . APP_NAME . ' Forgot Password Information';

        $message = '<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="' . $file_path . 'images/' . APP_LOGO . '" alt="header" width="120"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td><p style="color: #262626; font-size: 28px; margin-top:0px;"><strong>Dear ' . $row['name'] . '</strong></p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Thank you for using ' . APP_NAME . ',<br>
					                            Your password is: ' . $password . '</p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
					                            ' . APP_NAME . '.</p></td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © ' . APP_NAME . '.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';

        send_email($to, $recipient_name, $subject, $message);

        $sql = "UPDATE tbl_users SET `password`='$new_password' WHERE `id`='" . $row['id'] . "'";
        mysqli_query($mysqli, $sql);

        $set = array('msg' => $app_lang['password_sent_mail'], 'success' => '1');
    } else {
        $set = array('msg' => $app_lang['email_not_found'], 'success' => '0');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_app_update") {

    $jsonObj = array();

    $query = "SELECT * FROM tbl_settings WHERE id='1'";
    $sql = mysqli_query($mysqli, $query);

    $data = mysqli_fetch_assoc($sql);

    $row['update_status'] = $data['update_status'];
    $row['cancel_status'] = $data['cancel_status'];
    $row['new_app_version'] = $data['new_app_version'];
    $row['app_link'] = $data['app_link'];
    $row['commission'] = $data['commission'];
    $row['app_update_desc'] = $data['app_update_desc'];

    array_push($jsonObj, $row);

    $set['JOBS_APP'] = $jsonObj;

    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else if ($get_method['method_name'] == "get_app_details") {
    //User status
    $qry_user = "SELECT * FROM tbl_users WHERE `id` = '" . $get_method['user_id'] . "'";
    $result_user = mysqli_query($mysqli, $qry_user);
    $num_rows = mysqli_num_rows($result_user);
    $row1 = mysqli_fetch_assoc($result_user);

    if ($num_rows > 0) {
        if ($row1['status'] == '2') {
            $set['user_status'] = 'true';
        } else {
            $set['user_status'] = 'false';
        }
    } else {
        $set['user_status'] = 'false';
    }

    //App settings
    $jsonObj = array();

    $query = "SELECT * FROM tbl_settings WHERE `id`='1'";
    $sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

    $row['package_name'] = $settings_details['package_name'];

    while ($data = mysqli_fetch_assoc($sql)) {
        $row['app_name'] = $data['app_name'];
        $row['app_logo'] = $data['app_logo'];
        $row['app_version'] = $data['app_version'];
        $row['app_author'] = $data['app_author'];
        $row['app_contact'] = $data['app_contact'];
        $row['commission'] = $data['commission'];
        $row['app_email'] = $data['app_email'];
        $row['app_website'] = $data['app_website'];
        $row['app_description'] = $data['app_description'];
        $row['app_developed_by'] = $data['app_developed_by'];
        $row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);
        $row['publisher_id'] = $data['publisher_id'];
        $row['interstital_ad'] = $data['interstital_ad'];
        $row['interstital_ad_type'] = $data['interstital_ad_type'];
        $row['interstital_ad_id'] = ($data['interstital_ad_type'] == 'facebook') ? $data['interstital_facebook_id'] : $data['interstital_ad_id'];
        $row['interstital_ad_click'] = $data['interstital_ad_click'];
        $row['banner_ad'] = $data['banner_ad'];
        $row['banner_ad_type'] = $data['banner_ad_type'];
        $row['banner_ad_id'] = ($data['banner_ad_type'] == 'facebook') ? $data['banner_facebook_id'] : $data['banner_ad_id'];
        $row['update_status'] = $data['update_status'];
        $row['cancel_status'] = $data['cancel_status'];
        $row['new_app_version'] = $data['new_app_version'];
        $row['app_link'] = $data['app_link'];
        $row['app_update_desc'] = $data['app_update_desc'];
        array_push($jsonObj, $row);
    }

    $set['JOBS_APP'] = $jsonObj;
    header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();
}
else {
    $get_method = checkSignSalt($_POST['data']);
}
