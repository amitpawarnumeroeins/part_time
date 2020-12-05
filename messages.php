<?php $page_title="Manage City";

include('includes/header.php');

include('includes/function.php');
include('language/language.php');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$thisFromClick = "";
if(isset($_REQUEST["filter"]))
{
    $thisFromClick = $_REQUEST["filter"];
}



function convertDateTime($unixTime) {
    $seconds = $unixTime / 1000;
    return date("H:i:s | d-M-y", $seconds);
}


?>

<div class="row">
    <?php
    include ("plugin/dbconfig.php");
    $allnamesList = "No Data Available";
    $allmessagessList = "No Data Available";
    $allUserData = $database->getReference("users")->getValue();
    $allJobsData = $database->getReference("jobs")->getValue();
    $allMessages = $database->getReference("messages")->getValue();//->orderByChild('timestamp')

    /*print_r($allUserData);
    print_r($allMessages);
    print_r($allJobsData);*/

    if(is_array($allMessages) AND is_array($allUserData) AND is_array($allJobsData) AND sizeof($allMessages)>0 AND sizeof($allUserData)>0 AND sizeof($allJobsData)>0)
    {
        $allnamesList="";
        $allmessagessList="";
        $thisCounter = 0;
        foreach ($allMessages as $allMessagesKey => $allMessagesVal)
        {
            $thisUserId = explode("-",$allMessagesKey);
            $jobId=0;
            $jobName="";
            $jobImage="";

            $jobId = $thisUserId[2];
            $jobName = $allJobsData[$jobId]["name"];
            $jobImage = $allJobsData[$jobId]["image"];

            $firstName = $allUserData[$thisUserId[0]]["name"];
            $secondName = $allUserData[$thisUserId[1]]["name"];

            $thisAct = "";

            $thisValuesArray = array_values($allMessagesVal);
            $lastTimestamp = convertDateTime($thisValuesArray[sizeof($thisValuesArray)-1]["timestamp"]);

            if($thisFromClick == $allMessagesKey)
            {
                $thisAct = "active_chat";

                $thisMsgCounter = 0;
                foreach($thisValuesArray as $thisValuesArrayKey=>$thisValuesArrayVal)
                {
                    $receiverid = $thisValuesArrayVal["receiverid"];
                    $receivername = $thisValuesArrayVal["receivername"];
                    $sendername = $thisValuesArrayVal["sendername"];
                    $senderphoto = $thisValuesArrayVal["senderphoto"];
                    if(isset($thisValuesArrayVal["receiverphoto"]))
                    {
                        $receiverphoto = $thisValuesArrayVal["receiverphoto"];
                    }
                    $uploadImage = $thisValuesArrayVal["uploadImage"];
                    $timestamp = $thisValuesArrayVal["timestamp"];

                    $text = $thisValuesArrayVal["text"];

                    $thisType = "outgoing_msg";
                    $thisType1 = "sent_msg";
                    $thisType2 = "send_withd_msg";
                    $style = "float:right";
                    $thisType3 = "outgoing_msg_img";

                    if($thisMsgCounter%2==0)
                    {
                        $thisType = "incoming_msg";
                        $thisType1 = "received_msg";
                        $thisType2 = "received_withd_msg";
                        $thisType3 = "incoming_msg_img";
                        $style = "";
                    }

                    $imageContent = "";
                    if($uploadImage)
                    {
                        $imageContent = <<<AAA
                              <br>  <img src="$uploadImage"/>
AAA;

                    }

                    $dateVarName = convertDateTime($timestamp);

                    $thisMsgCounter++;
                    $allmessagessList .=<<<AAA
                        <div class="$thisType">
                            <div class="$thisType3"> 
                            <img src="https://ptetutorials.com/images/user-profile.png"> 
                            </div>
                            <div class="$thisType1">
                                <div class="$thisType2">
                                    <p>
                                        <b>From: $sendername</b><br>
                                        <b>To: $receivername</b><br><br>
                                        $text<br>
                                        $imageContent
                                    </p>
                                    <span class="time_date">$dateVarName</span></div>
                            </div>
                        </div>
AAA;

                }

            }

            $allnamesList .=<<<AAA
                    <div class="chat_list $thisAct">
                        <a  href="messages.php?filter=$allMessagesKey">
                            <div class="chat_people">
                                <div class="chat_img"> 
                                    <img src="$jobImage"> 
                                </div>
                                <div class="chat_ib">
                                    <h4 style="margin: 0">$jobName</h4>
                                    <h5>$firstName  & $secondName</h5>
                                    <span class="chat_date">Last Chat: $lastTimestamp</span>
                                </div>
                            </div>
                        </a>
                    </div>
AAA;

            $thisCounter++;
        }
    }
    ?>
    <div class="messaging">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h4>Messages</h4>
                    </div>
                    <div class="srch_bar " style="display: none">
                        <div class="stylish-input-group">
                            <input type="text" class="search-bar"  placeholder="Search" >
                            <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span> </div>
                    </div>
                </div>
                <div class="inbox_chat" id="inbox_chat">
                    <?=$allnamesList?>
                </div>
            </div>
            <div class="mesgs">
                <div class="msg_history">
                    <?=$allmessagessList?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include('includes/footer.php'); ?>

<script>
    // $("#inbox_chat").css("height", ($(document).height()-300) + "px");
    //$("#msg_history").css("height", ($(document).height()-300) + "px");

</script>
