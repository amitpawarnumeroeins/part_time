<?php
include("includes/connection.php");
include("includes/function.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$job_search = "flutter";
/*$cat_id = $get_method['cat_id'];
$city_id = $get_method['city_id'];
$budget_range = $get_method['budget_range'];
$day_range = $get_method['day_range'];
$time_range = $get_method['time_range'];
$job_type = $get_method['job_type'];*/


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
        $sqlFilter .= " AND tbl_jobs.`job_work_time` LIKE '%".$day_range."%' ";
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
        $sqlFilter .= " AND tbl_jobs.`job_type` = '$job_type' ";
    }


    $query_rec = "SELECT COUNT(*) as num FROM tbl_jobs
		LEFT JOIN tbl_category ON tbl_jobs.`cat_id`= tbl_category.`cid` 
		LEFT JOIN tbl_city ON tbl_jobs.`city_id`= tbl_city.`c_id`
		WHERE tbl_jobs.`status`=1 AND tbl_jobs.`job_status`= 0 $sqlFilter";
    $total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query_rec));

    $page_limit = 10;

    $limit = (1 - 1) * $page_limit;

    echo $query = "SELECT * FROM tbl_jobs
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

   // header('Content-Type: application/json; charset=utf-8');
    echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    die();


    ?>

