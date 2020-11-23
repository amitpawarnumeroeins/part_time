<?php
include("includes/connection.php");

require("includes/function.php");
require("language/language.php");

error_log(0);
/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/
$invoice = file_get_contents("invoice.html");
$getCompany = mysqli_query($mysqli,"SELECT * FROM `tbl_settings` WHERE 1") or die(mysqli_error($con));
$rowComp= mysqli_fetch_array($getCompany);

$jobId = $_GET['job_id'];

$getJobs = mysqli_query($mysqli,"SELECT * FROM `tbl_jobs` WHERE `id` = '$jobId' AND `job_status` = 1") or die(mysqli_error($con));
if(mysqli_num_rows($getJobs))
{
    $rowJobs= mysqli_fetch_array($getJobs);
    $user_id = $rowJobs["user_id"];
    $cat_id = $rowJobs["cat_id"];
    $city_id = $rowJobs["city_id"];
    $job_name = $rowJobs["job_name"];
    $job_designation = $rowJobs["job_designation"];
    $job_desc = $rowJobs["job_desc"];
    $job_salary = $rowJobs["job_salary"];
    $job_salary_mode = $rowJobs["job_salary_mode"];
    $payment_mode = $rowJobs["payment_mode"];
    $job_company_name = $rowJobs["job_company_name"];
    $job_company_website = $rowJobs["job_company_website"];
    $job_phone_number = $rowJobs["job_phone_number"];
    $job_country_code = $rowJobs["job_country_code"];
    $job_mail = $rowJobs["job_mail"];
    $job_vacancy = $rowJobs["job_vacancy"];
    $job_address = $rowJobs["job_address"];
    $job_qualification = $rowJobs["job_qualification"];
    $job_skill = $rowJobs["job_skill"];
    $job_image = $rowJobs["job_image"];
    $job_map_latitude = $rowJobs["job_map_latitude"];
    $job_map_longitude = $rowJobs["job_map_longitude"];
    $job_date = $rowJobs["job_date"];
    $job_type = $rowJobs["job_type"];
    $job_experince = $rowJobs["job_experince"];
    $job_work_day = $rowJobs["job_work_day"];
    $job_work_time = $rowJobs["job_work_time"];
    $status = $rowJobs["status"];
    $job_status = $rowJobs["job_status"];
    $user_alloted = $rowJobs["user_alloted"];
    $job_start_time = $rowJobs["job_start_time"];
    $job_end_time = $rowJobs["job_end_time"];

    $providerData = get_user_full_data($user_id);
    $seekerData = get_user_full_data($user_alloted);
    $jobTransData = getTransactionDetails($jobId);

    //$invno = "INV".$id;
    $invno = $jobTransData[0]["transaction_id"];



    $paymentMode = array(1=>"hourly", 2=>"daily", 3=>"weekly", 4=>"monthly");

    $invoice = str_ireplace("{[##COMPANY_NAME##]}",$rowComp['app_name'],$invoice);
    $invoice = str_ireplace("{[##COMPANY_PHONE##]}",$rowComp['app_contact'],$invoice);
    $invoice = str_ireplace("{[##COMPANY_EMAIL##]}",$rowComp['app_email'],$invoice);
    $invoice = str_ireplace("{[##COMPANY_WEBSITE##]}",$rowComp['app_website'],$invoice);
    $invoice = str_ireplace("{[##CLIENT_NAME1##]}",$providerData["name"],$invoice);
    $invoice = str_ireplace("{[##CLIENT_ADDRESS1##]}","PROVIDER",$invoice);
    $invoice = str_ireplace("{[##CLIENT_PHONE1##]}",$providerData["phone"],$invoice);
    $invoice = str_ireplace("{[##CLIENT_EMAIL1##]}",$providerData["email"],$invoice);
    $invoice = str_ireplace("{[##CLIENT_NAME2##]}",$seekerData["name"],$invoice);
    $invoice = str_ireplace("{[##CLIENT_ADDRESS2##]}","USER",$invoice);
    $invoice = str_ireplace("{[##CLIENT_PHONE2##]}",$seekerData["phone"],$invoice);
    $invoice = str_ireplace("{[##CLIENT_EMAIL2##]}",$seekerData["email"],$invoice);
    $invoice = str_ireplace("{[##INVOICENUMBER##]}",$invno,$invoice);
    $invoice = str_ireplace("{[##SERVICEMODE##]}","ONLINE",$invoice);
    $invoice = str_ireplace("{[##DATE##]}",$job_end_time,$invoice);
    $invoice = str_ireplace("{[##PROJECT_NAME##]}",$job_name,$invoice);
    $invoice = str_ireplace("{[##PROJECT_DURATION##]}",$job_work_day."<br>".$job_work_time,$invoice);
    $invoice = str_ireplace("{[##AMOUNT##]}",$job_salary,$invoice);
    $invoice = str_ireplace("{[##PAYMENT_MODE##]}",$paymentMode[$job_salary_mode],$invoice);
    $invoice = str_ireplace("{[##GRANDTOTAL##]}",$job_salary."*".$job_salary_mode,$invoice);
    $invoice = str_ireplace("{[##REMARK##]}","No Remarks",$invoice);
    $invoice = str_ireplace("{[##COMISSION_PERCENTAGE##]}",$rowComp['commission'],$invoice);
    $invoice = str_ireplace("{[##FOOTNOTE##]}","*T&C applied",$invoice);
    $html = $invoice;
}

?>

<style type="text/css" media="print">
    @page {
        size: auto;   /* auto is the initial value */
        margin: 10;  /* this affects the margin in the printer settings */

    }
    input{display:none}

</style>



<?php
echo $html;
echo "   <script>
function myFunction() {
    window.print();
}
myFunction();
</script>";

?>

