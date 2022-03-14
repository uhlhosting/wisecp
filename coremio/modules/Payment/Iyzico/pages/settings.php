<?php
    if(!defined("CORE_FOLDER")) die();
    $LANG       = $module->lang;
    $CONFIG     = $module->config;
?>
<form action="<?php echo Controllers::$init->getData("links")["controller"]; ?>" method="post" id="Iyzico">
    <input type="hidden" name="operation" value="module_controller">
    <input type="hidden" name="module" value="Iyzico">
    <input type="hidden" name="controller" value="settings">

    <div class="blue-info" style="margin-bottom:20px;">
        <div class="padding15">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <p><?php echo $LANG["description"]; ?></p>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">Api Key</div>
        <div class="yuzde70">
            <input type="text" name="api_key" value="<?php echo $CONFIG["settings"]["api_key"]; ?>">
            <span class="kinfo"><?php echo $LANG["api-key-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">Secret Key</div>
        <div class="yuzde70">
            <input type="text" name="secret_key" value="<?php echo $CONFIG["settings"]["secret_key"]; ?>">
            <span class="kinfo"><?php echo $LANG["secret-key-desc"]; ?></span>
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30"><?php echo $LANG["commission-rate"]; ?></div>
        <div class="yuzde70">
            <input type="text" name="commission_rate" value="<?php echo $CONFIG["settings"]["commission_rate"]; ?>" style="width: 80px;">
            <span class="kinfo"><?php echo $LANG["commission-rate-desc"]; ?></span>
        </div>
    </div>


    <div style="float:right;" class="guncellebtn yuzde30"><a id="Iyzico_submit" href="javascript:void(0);" class="yesilbtn gonderbtn"><?php echo $LANG["save-button"]; ?></a></div>

</form>


<script type="text/javascript">
    $(document).ready(function(){

        $("#Iyzico_submit").click(function(){
            MioAjaxElement($(this),{
                waiting_text:waiting_text,
                progress_text:progress_text,
                result:"Iyzico_handler",
            });
        });

    });

    function Iyzico_handler(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status == "error"){
                    if(solve.for != undefined && solve.for != ''){
                        $("#Iyzico "+solve.for).focus();
                        $("#Iyzico "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                        $("#Iyzico "+solve.for).change(function(){
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