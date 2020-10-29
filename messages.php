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
    $dt = new DateTime("@$unixTime");
    return $dt->format('H:i:s | d-M-Y');
}


?>
<style>
    .container{max-width:1170px; margin:auto;}
    img{ max-width:100%;}
    .inbox_people {
        background: #f8f8f8 none repeat scroll 0 0;
        float: left;
        overflow: hidden;
        width: 40%; border-right:1px solid #c4c4c4;
    }
    .inbox_msg {
        border: 1px solid #c4c4c4;
        clear: both;
        overflow: hidden;
    }
    .top_spac{ margin: 20px 0 0;}


    .recent_heading {float: left; width:40%;}
    .srch_bar {
        display: inline-block;
        text-align: right;
        width: 60%; padding:
    }
    .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

    .recent_heading h4 {
        color: #05728f;
        font-size: 21px;
        margin: auto;
    }
    .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
    .srch_bar .input-group-addon button {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        padding: 0;
        color: #707070;
        font-size: 18px;
    }
    .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

    .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
    .chat_ib h5 span{ font-size:13px; float:right;}
    .chat_ib p{ font-size:14px; color:#989898; margin:auto}
    .chat_img {
        float: left;
        width: 11%;
    }
    .chat_ib {
        float: left;
        padding: 0 0 0 15px;
        width: 88%;
    }

    .chat_people{ overflow:hidden; clear:both;}
    .chat_list {
        border-bottom: 1px solid #c4c4c4;
        margin: 0;
        padding: 18px 16px 10px;
        cursor: pointer;
    }
    .inbox_chat { height: 550px;
        height: calc(100vh - 150px); overflow-y: scroll;}

    .active_chat{ background:#ebebeb;}

    .incoming_msg_img,.outgoing_msg_img {
        display: inline-block;
        width: 6%;
    }
    .received_msg {
        display: inline-block;
        padding: 0 0 0 10px;
        vertical-align: top;
        width: 92%;
    }
    .received_withd_msg p {
        background: #d2d2d2 none repeat scroll 0 0;
        border-radius: 3px;
        color: #646464;
        font-size: 14px;
        margin: 0;
        padding: 5px 10px 5px 12px;
        width: 100%;
    }
    .time_date {
        color: #747474;
        display: block;
        font-size: 12px;
        margin: 8px 0 0;
    }
    .received_withd_msg { width: 57%;}
    .send_withd_msg { width: 100%; float: right;text-align: right}
    .mesgs {
        float: left;
        padding: 30px 15px 0 25px;
        width: 60%;
    }

    .sent_msg p {
        background: #05728f none repeat scroll 0 0;
        border-radius: 3px;
        font-size: 14px;
        margin: 0; color:#fff;
        padding: 5px 10px 5px 12px;
        width:100%;
    }
    .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
    .outgoing_msg_img{ float: right}
    .sent_msg {
        float: right;
        width: 57%;
    }
    .input_msg_write input {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        color: #4c4c4c;
        font-size: 15px;
        min-height: 48px;
        width: 100%;
    }

    .type_msg {border-top: 1px solid #c4c4c4;position: relative;}
    .msg_send_btn {
        background: #05728f none repeat scroll 0 0;
        border: medium none;
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        font-size: 17px;
        height: 33px;
        position: absolute;
        right: 0;
        top: 11px;
        width: 33px;
    }
    .messaging { padding: 0 0 50px 0;}
    .msg_history {
        height: 516px;
        height: calc(100vh - 150px);
        overflow-y: auto;
    }
</style>
<div class="row">
    <?php
    include ("plugin/dbconfig.php");

    $allUserData = $database->getReference("users")->getValue();
    $allMessages = $database->getReference("messages")->getValue();
    //print_r($allMessages);
    if(sizeof($allMessages)>0)
    {
        $allnamesList="";
        $allmessagessList="";
        $thisCounter = 0;
        foreach ($allMessages as $allMessagesKey => $allMessagesVal)
        {
            $thisUserId = explode("-",$allMessagesKey);
            $firstName = array_values($allUserData[$thisUserId[0]])[0]["name"];
            $secondName = array_values($allUserData[$thisUserId[1]])[0]["name"];


            $thisAct = "";

            $thisValuesArray = array_values($allMessagesVal);

            if($thisCounter==0 || $thisFromClick == $allMessagesKey)
            {
                if($thisFromClick == $allMessagesKey)
                {
                    $thisAct = "active_chat";
                }

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
                                <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                                <div class="chat_ib">
                                    <h5>$firstName  & $secondName</h5>
                                    <span class="chat_date">Dec 25</span>
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
