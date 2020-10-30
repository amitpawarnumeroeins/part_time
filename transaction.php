<?php $page_title="Manage City";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$qry = "SELECT tbl_transaction_details.*,tbl_users.name as user_name FROM tbl_transaction_details
		LEFT JOIN tbl_users ON tbl_users.`id`= tbl_transaction_details.`user_id` 
		WHERE 1
		ORDER BY tbl_transaction_details.`id` DESC LIMIT 10";

$result = mysqli_query($mysqli, $qry) or die('Error in fetch data ->' . mysqli_error($mysqli));

$transDataDisp = "";
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result))
    {
        $transaction_id = $row["transaction_id"];
        $type = $row["type"] == 1 ? "+Credit" : "-Debit";
        $typeColor = $row["type"] == 1 ? "text-success" : "text-danger";
        $user_id = $row["user_id"];
        $user_name = $row["user_name"];
        $amount = $row["amount"];
        $status = $row["status"];
        $mode = $row["mode"];
        $bank_trans_id = $row["bank_trans_id"];
        $bank_trans_response = $row["bank_trans_response"];
        $trans_type = $row["trans_type"] == 1 ? "Bank" : "Wallet";
        $user_updated = $row["user_updated"];
        $created_at = $row["created_at"];

        switch ($status)
        {
            case 1:
                $statusText = "Approved";
                $statusColor = "text-success";
            case 2:
                $statusText = "Rejected";
                $statusColor = "text-danger";
            case 3:
                $statusText = "Failed";
                $statusColor = "text-danger";
            case 4:
                $statusText = "Pending";
                $statusColor = "text-warning";
            default: "Pending";
        }

        $transDataDisp .=<<<AAA
                        <tr>
                            <td class="$typeColor text-center">$type</td>
                            <td class="text-center">$amount</td>
                            <td class="$statusColor text-center">$transaction_id <br> $statusText</td>
                            <td class="text-center text-capitalize">$user_name</td>
                            <td class="text-center">$trans_type <br> $mode</td>
                            <td class="text-center">$created_at</td>
                        </tr>
AAA;

    }
}
?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4> Transaction Details</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th class="text-center">Type</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Transaction Id</th>
                            <th class="text-center">User</th>
                            <th class="text-center">trans_type mode</th>
                            <th class="text-center">created_at</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?=$transDataDisp?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
