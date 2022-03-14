<script type="text/javascript">
    $(document).ready(function(){

        $("#ConnectFacebook_submit_button").click(function(){
            MioAjaxElement($(this),{
                waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                result:"ConnectFacebook_settingsForm_handler",
            });
        });

    });

    function ConnectFacebook_settingsForm_handler(result){
        if(result != ''){
            var solve = getJson(result);
            if(solve !== false){
                if(solve.status == "error"){
                    if(solve.for != undefined && solve.for != ''){
                        $("#ConnectFacebook_settingsForm "+solve.for).focus();
                        $("#ConnectFacebook_settingsForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                        $("#ConnectFacebook_settingsForm "+solve.for).change(function(){
                            $(this).removeAttr("style");
                        });
                    }
                    if(solve.message != undefined && solve.message != '')
                        alert_error(solve.message,{timer:10000});
                }else if(solve.status == "successful"){
                    if(solve.message != undefined){
                        alert_success(solve.message,{timer:3000});
                    }
                    if(solve.redirect != undefined && solve.redirect != '') window.location.href = solve.redirect;
                }
            }else
                console.log(result);
        }
    }
</script>
<form action="<?php echo $request_uri; ?>?module=ConnectFacebook" method="post" id="ConnectFacebook_settingsForm">

    <div class="padding20">

    <input type="hidden" name="operation" value="get_addon_content">
    <input type="hidden" name="module_operation" value="save_config">


    <div class="formcon">
        <div class="yuzde30">App ID</div>
        <div class="yuzde70">
            <input type="text" name="app_id" value="<?php echo $config["settings"]["app_id"]; ?>">
        </div>
    </div>

    <div class="formcon">
        <div class="yuzde30">App Secret</div>
        <div class="yuzde70">
            <input type="text" name="app_secret" value="<?php echo $config["settings"]["app_secret"]; ?>">
        </div>
    </div>
        

    </div>

    <div class="modal-foot-btn">
        <a href="javascript:void 0;" style="float:right;" class="green lbtn" id="ConnectFacebook_submit_button"><?php echo ___("needs/button-save"); ?></a>
    </div>

</form>