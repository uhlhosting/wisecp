<?php
    if(!defined("CORE_FOLDER")) die();
    $LANG       = $module->lang;
    $CONFIG     = $module->config;
    $notify_url = Controllers::$init->CRLink("payment",['PayTR_BankTransfer',$module->get_auth_token(),'callback'],"none");
?>

<form action="<?php echo Controllers::$init->getData("links")["controller"]; ?>" method="post" id="PayTR_BankTransfer">
    <input type="hidden" name="operation" value="module_controller">
    <input type="hidden" name="module" value="PayTR_BankTransfer">
    <input type="hidden" name="controller" value="settings">

    <div class="blue-info" style="margin-bottom:20px;">
        <div class="padding15">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <p><?php echo $LANG["description"]; ?></p>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">Merchant ID</div>
        <div class="yuzde70">
            <input type="text" name="merchant_id" value="<?php echo $CONFIG["settings"]["merchant_id"]; ?>">
            <span class="kinfo"><?php echo $LANG["merchant-id-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">Merchant Key</div>
        <div class="yuzde70">
            <input type="text" name="merchant_key" value="<?php echo $CONFIG["settings"]["merchant_key"]; ?>">
            <span class="kinfo"><?php echo $LANG["merchant-key-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">Merchant Salt</div>
        <div class="yuzde70">
            <input type="text" name="merchant_salt" value="<?php echo $CONFIG["settings"]["merchant_salt"]; ?>">
            <span class="kinfo"><?php echo $LANG["merchant-salt-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30"><?php echo $LANG["commission-rate"]; ?></div>
        <div class="yuzde70">
            <input type="text" name="commission_rate" value="<?php echo $CONFIG["settings"]["commission_rate"]; ?>" style="width: 80px;">
            <span class="kinfo"><?php echo $LANG["commission-rate-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30"><?php echo $LANG["test-mode"]; ?></div>
        <div class="yuzde70">
            <input<?php echo $CONFIG["settings"]["test_mode"] ? ' checked' : ''; ?> type="checkbox" name="test_mode" class="sitemio-checkbox" value="1" id="paytr_banktransfer_test_mode">
            <label for="paytr_banktransfer_test_mode" class="sitemio-checkbox-label"></label>
            <span class="kinfo"><?php echo $LANG["test-mode-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30"><?php echo $LANG["debug-mode"]; ?></div>
        <div class="yuzde70">
            <input<?php echo $CONFIG["settings"]["debug_on"] ? ' checked' : ''; ?> type="checkbox" name="debug_on" class="sitemio-checkbox" value="1" id="paytr_banktransfer_debug_on">
            <label for="paytr_banktransfer_debug_on" class="sitemio-checkbox-label"></label>
            <span class="kinfo"><?php echo $LANG["debug-mode-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30" style=" color: #f44336;"><?php echo $LANG["notify-url"]; ?></div>
        <div class="yuzde70">
            <span class="selectalltext" style="font-size:13px;font-weight:600;    color: #f44336;"><?php echo $notify_url; ?></span>
            <div class="clear"></div>
            <span class="kinfo"><?php echo $LANG["notify-url-desc"]; ?></span>
        </div>
    </div>



    <div style="float:right;" class="guncellebtn yuzde30"><a id="PayTR_BankTransfer_submit" href="javascript:void(0);" class="yesilbtn gonderbtn"><?php echo $LANG["save-button"]; ?></a></div>

</form>


<script type="text/javascript">
    $(document).ready(function(){

        $("#PayTR_BankTransfer_submit").click(function(){
            MioAjaxElement($(this),{
                waiting_text:waiting_text,
                progress_text:progress_text,
                result:"PayTR_BankTransfer_handler",
            });
        });

    });

    function PayTR_BankTransfer_handler(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status == "error"){
                    if(solve.for != undefined && solve.for != ''){
                        $("#PayTR_BankTransfer "+solve.for).focus();
                        $("#PayTR_BankTransfer "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                        $("#PayTR_BankTransfer "+solve.for).change(function(){
                            $(this).removeAttr("style");
                        });
                    }
                    if(solve.message != undefined && solve.message != '')
                        alert_error(solve.message,{timer:5000});
                }else if(solve.status == "successful"){
                    alert_success(solve.message,{timer:2500});
                }
            }else
                console.log(result);
        }
    }
</script>