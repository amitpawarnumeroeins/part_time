<?php
require("includes/connection.php");
require("includes/function.php");
require("language/language.php");

error_reporting(0);

$file_path = getBaseUrl();

$response=array();

switch ($_POST['action']) {
    case 'toggle_status':
        $id=$_POST['id'];
        $for_action=$_POST['for_action'];
        $column=$_POST['column'];
        $tbl_id=$_POST['tbl_id'];
        $table_nm=$_POST['table'];
        if($table_nm == "tbl_users")
        {
            if($for_action=='active' OR $for_action=='verify'){
                $data = array($column  =>  '2');
                $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");
                $_SESSION['msg']="13";
            }else{
                $data = array($column  =>  '1');
                $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");
                $_SESSION['msg']="14";
            }
        }elseif($table_nm == "tbl_transaction_details")
        {
            if($for_action=='active')
            {

                $status = 1;
                $bank_trans_id = "From Admin";
                $bank_trans_response = "From Admin";
                $updated_at = strtotime(date('d-m-Y h:i A'));

                $query1 = "SELECT * FROM tbl_transaction_details WHERE id = $id AND trans_type = 1 AND  type = 2 AND status = 4 AND `user_updated` = 0 AND `trans_for`=1";
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

                        $user_edit = Update('tbl_transaction_details', $data, "WHERE `id` = '" . $id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");

                        $qry = "SELECT * FROM tbl_transaction_details WHERE `id` = '" . $id . "' AND `user_id` = $user_id AND `status` = 1 AND `user_updated` = 0 AND `trans_for`=1";
                        $result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

                        if (mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $thisAmount = $row["amount"];
                            mysqli_query($mysqli, "UPDATE tbl_users SET `current_wallet_amount` = `current_wallet_amount` -$thisAmount WHERE `id` = '$user_id'");

                            $dataUpdate = array(
                                'user_updated' => 1 //0- not updated, 1-updated
                            );

                            Update('tbl_transaction_details', $dataUpdate, "WHERE `id` = '" . $id . "' AND `user_id` = '" . $user_id . "' AND `trans_for`=1");


                            $fcmMessage = "Part Time: Withdrawal Approved";
                            $fcmBody = "Your Wallet to Account Withdrawal Request Approved!!";
                            if($row2["user_type"]==1)
                            {
                                $fcmClickIntent = "seeker_wallet_withdrawal";

                            }else{
                                $fcmClickIntent = "provider_wallet_withdrawal";

                            }
                            sendFcmNotification($user_id,$fcmMessage,$fcmBody,$fcmClickIntent);


                            $_SESSION['msg']="25";
                        } else {
                            $_SESSION['msg']="26";
                        }
                    }else{
                        $_SESSION['msg']="27";
                    }
                }
            }else{
                $data = array($column  =>  '2');
                $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");
                $_SESSION['msg']="14";
            }
        }else{
            if($for_action=='active'){
                $data = array($column  =>  '1');
                $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");
                $_SESSION['msg']="13";
            }else{
                $data = array($column  =>  '0');
                $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");
                $_SESSION['msg']="14";
            }
        }

        //$_SESSION['msg']="21";
        $response['status']=1;
        echo json_encode($response);
        break;

    case 'multi_delete':

        $ids=implode(",", $_POST['id']);

        if($ids==''){
            $ids=$_POST['id'];
        }

        $tbl_nm=$_POST['tbl_nm'];

        if($tbl_nm=='tbl_category'){

            $sql="SELECT * FROM tbl_jobs WHERE `cat_id` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);

            while ($row=mysqli_fetch_assoc($res)) {
                if($row['job_image']!="")
                {
                    unlink('images/'.$row['job_image']);
                    unlink('images/thumbs/'.$row['job_image']);
                }

            }

            $deleteSql="DELETE FROM tbl_jobs WHERE `cat_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $sql="SELECT * FROM $tbl_nm WHERE `cid` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);
            while ($row=mysqli_fetch_assoc($res)){
                if($row['category_image']!="")
                {
                    unlink('images/'.$row['category_image']);
                    unlink('images/thumbs/'.$row['category_image']);
                }

            }

            $deleteSql="DELETE FROM $tbl_nm WHERE `cid` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);
        }

        else if($tbl_nm=='tbl_jobs'){

            $sql="SELECT * FROM tbl_jobs WHERE `id` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);

            while ($row=mysqli_fetch_assoc($res)) {
                if($row['job_image']!="")
                {
                    unlink('images/'.$row['job_image']);
                    unlink('images/thumbs/'.$row['job_image']);
                }

            }

            $deleteSql="DELETE FROM tbl_jobs WHERE `id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_recent WHERE `job_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_saved WHERE `job_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_apply WHERE `job_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

        }

        else if($tbl_nm=='tbl_users'){

            $sql="SELECT * FROM tbl_users WHERE `id` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);

            while ($row=mysqli_fetch_assoc($res)) {
                if($row['user_image']!="")
                {
                    unlink('images/'.$row['user_image']);
                }
                if($row['user_resume']!="")
                {
                    unlink('uploads/'.$row['user_resume']);
                }

            }

            $sql="DELETE FROM tbl_users WHERE `id` IN ($ids)";
            mysqli_query($mysqli, $sql);

            $deleteSql="DELETE FROM tbl_saved WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_apply WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_recent WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_jobs WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_company WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

            $deleteSql="DELETE FROM tbl_advertisements_request WHERE `user_id` IN ($ids)";

            mysqli_query($mysqli, $deleteSql);

        }
        else if($tbl_nm=='tbl_city'){

            $sql="SELECT * FROM tbl_city WHERE `c_id` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);

            $sql="DELETE FROM tbl_city WHERE `c_id` IN ($ids)";
            mysqli_query($mysqli, $sql);

            $deleteSql="DELETE FROM tbl_jobs WHERE `city_id` IN ($ids)";
            mysqli_query($mysqli, $deleteSql);

        }
        else if($tbl_nm=='tbl_saved'){

            $sql="SELECT * FROM tbl_saved WHERE `sa_id` IN ($ids)";
            $res=mysqli_query($mysqli, $sql);

            $sql="DELETE FROM tbl_saved WHERE `sa_id` IN ($ids)";
            mysqli_query($mysqli, $sql);

        }

        $_SESSION['msg']="12";

        $response['status']=1;
        echo json_encode($response);
        break;

    case 'removeCompany':

        $ids=is_array($_POST['ids']) ? implode(',', $_POST['ids']) : $_POST['ids'];

        $sqlDelete="DELETE FROM tbl_company WHERE `id` IN ($ids)";

        if(mysqli_query($mysqli, $sqlDelete)){
            $response['status']=1;
        }
        else{
            $response['status']=0;
        }


        $response['status']=1;
        $_SESSION['msg']="12";
        echo json_encode($response);
        break;

    case 'removeContact':

        $ids=is_array($_POST['ids']) ? implode(',', $_POST['ids']) : $_POST['ids'];

        $sqlDelete="DELETE FROM tbl_contact WHERE `id` IN ($ids)";

        if(mysqli_query($mysqli, $sqlDelete)){
            $response['status']=1;
        }
        else{
            $response['status']=0;
        }


        $response['status']=1;
        $_SESSION['msg']="12";
        echo json_encode($response);
        break;

    case 'removeapply':

        $ids=is_array($_POST['ids']) ? implode(',', $_POST['ids']) : $_POST['ids'];

        $sqlDelete="DELETE FROM tbl_apply WHERE `ap_id` IN ($ids)";

        if(mysqli_query($mysqli, $sqlDelete)){
            $response['status']=1;
        }
        else{
            $response['status']=0;
        }


        $response['status']=1;
        $_SESSION['msg']="21";
        echo json_encode($response);
        break;

    case 'removebuyads':

        $ids=is_array($_POST['ids']) ? implode(',', $_POST['ids']) : $_POST['ids'];

        $sql="SELECT * FROM tbl_advertisements_request WHERE `a_id` IN ($ids)";
        $res=mysqli_query($mysqli, $sql);

        while ($row=mysqli_fetch_assoc($res)) {
            if($row['ads_image']!="")
            {
                unlink('../assets/img/'.$row['ads_image']);
            }
            if($row['ads_image_sidebar']!="")
            {
                unlink('../assets/img/'.$row['ads_image_sidebar']);
            }
        }
        $sqlDelete="DELETE FROM tbl_advertisements_request WHERE `a_id` IN ($ids)";

        if(mysqli_query($mysqli, $sqlDelete)){
            $response['status']=1;
        }
        else{
            $response['status']=0;
        }

        $response['status']=1;
        $_SESSION['msg']="12";
        echo json_encode($response);
        break;

    default:
        # code...
        break;
}
