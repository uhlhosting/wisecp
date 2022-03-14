<?php
    if(!defined("CORE_FOLDER")) die();
    $LANG   = $module->lang;
    $CONFIG = $module->config;
    Helper::Load("Money");
?>
<script type="text/javascript">
function NameSilo_open_tab(elem, tabName){
    var owner = "NameSilo_tab";
    $("#"+owner+" .modules-tabs-content").css("display","none");
    $("#"+owner+" .modules-tabs .modules-tab-item").removeClass("active");
    $("#"+owner+"-"+tabName).css("display","block");
    $("#"+owner+" .modules-tabs .modules-tab-item[data-tab='"+tabName+"']").addClass("active");
}
</script>

<div id="NameSilo_tab">
    <ul class="modules-tabs">
        <li><a href="javascript:NameSilo_open_tab(this,'detail');" data-tab="detail" class="modules-tab-item active"><?php echo $LANG["tab-detail"]; ?></a></li>
        <li><a href="javascript:NameSilo_open_tab(this,'import');" data-tab="import" class="modules-tab-item"><?php echo $LANG["tab-import"]; ?></a></li>
    </ul>

    <div id="NameSilo_tab-detail" class="modules-tabs-content" style="display: block">

        <form action="<?php echo Controllers::$init->getData("links")["controller"]; ?>" method="post" id="NameSiloSettings">
            <input type="hidden" name="operation" value="module_controller">
            <input type="hidden" name="module" value="NameSilo">
            <input type="hidden" name="controller" value="settings">

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["api-key"]; ?></div>
                <div class="yuzde70">
                    <input type="password" name="api-key" value="<?php echo $CONFIG["settings"]["api-key"] ? "*****" : ""; ?>">
                    <span class="kinfo"><?php echo $LANG["desc"]["api-key"]; ?></span>
                </div>
            </div>

            <div class="formcon" id="NameSilo_sandbox_api_key_con" style="<?php echo $CONFIG["settings"]["test-mode"] ? '' : 'display:none;'; ?>">
                <div class="yuzde30"><?php echo $LANG["fields"]["api-key-sandbox"]; ?></div>
                <div class="yuzde70">
                    <input type="password" name="api-key-sandbox" value="<?php echo $CONFIG["settings"]["api-key-sandbox"] ? "*****" : ""; ?>">
                    <span class="kinfo"><?php echo $LANG["desc"]["api-key-sandbox"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["payment-id"]; ?></div>
                <div class="yuzde70">
                    <input type="text" name="payment-id" value="<?php echo $CONFIG["settings"]["payment-id"]; ?>">
                    <span class="kinfo"><?php echo $LANG["desc"]["payment-id"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["coupon"]; ?></div>
                <div class="yuzde70">
                    <input type="text" name="coupon" value="<?php echo $CONFIG["settings"]["coupon"]; ?>">
                    <span class="kinfo"><?php echo $LANG["desc"]["coupon"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["test-mode"]; ?></div>
                <div class="yuzde70">
                    <input<?php echo $CONFIG["settings"]["test-mode"] ? ' checked' : ''; ?> type="checkbox" name="test-mode" value="1" class="sitemio-checkbox" id="NameSilo_test-mode" onchange="if($(this).prop('checked')) $('#NameSilo_sandbox_api_key_con').css('display','block'); else $('#NameSilo_sandbox_api_key_con').css('display','none');">
                    <label class="sitemio-checkbox-label" for="NameSilo_test-mode"></label>
                    <span class="kinfo"><?php echo $LANG["desc"]["test-mode"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["auto-renew"]; ?></div>
                <div class="yuzde70">
                    <input<?php echo $CONFIG["settings"]["auto-renew"] ? ' checked' : ''; ?> type="checkbox" name="auto-renew" value="1" class="sitemio-checkbox" id="NameSilo_auto-renew">
                    <label class="sitemio-checkbox-label" for="NameSilo_auto-renew"></label>
                    <span class="kinfo"><?php echo $LANG["desc"]["auto-renew"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["WHiddenAmount"]; ?></div>
                <div class="yuzde70">
                    <input type="text" name="whidden-amount" value="<?php echo Money::formatter($CONFIG["settings"]["whidden-amount"],$CONFIG["settings"]["whidden-currency"]); ?>" style="width: 100px;" onkeypress='return event.charCode==46  || event.charCode>= 48 &&event.charCode<= 57'>
                    <select name="whidden-currency" style="width: 150px;">
                        <?php
                            foreach(Money::getCurrencies($CONFIG["settings"]["whidden-currency"]) AS $currency){
                                ?>
                                <option<?php echo $currency["id"] == $CONFIG["settings"]["whidden-currency"] ? ' selected' : ''; ?> value="<?php echo $currency["id"]; ?>"><?php echo $currency["name"]." (".$currency["code"].")"; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <span class="kinfo"><?php echo $LANG["desc"]["WHiddenAmount"]; ?></span>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["adp"]; ?></div>
                <div class="yuzde70">
                    <input<?php echo isset($CONFIG["settings"]["adp"]) && $CONFIG["settings"]["adp"] ? ' checked' : ''; ?> type="checkbox" name="adp" value="1" id="NameSilo_adp" class="checkbox-custom">
                    <label class="checkbox-custom-label" for="NameSilo_adp">
                        <span class="kinfo"><?php echo $LANG["desc"]["adp"]; ?></span>
                    </label>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo __("admin/products/profit-rate-for-registrar-module"); ?></div>
                <div class="yuzde70">
                    <input type="text" name="profit-rate" value="<?php echo Config::get("options/domain-profit-rate"); ?>" style="width: 50px;" onkeypress='return event.charCode==44 || event.charCode==46 || event.charCode>= 48 &&event.charCode<= 57'>
                </div>
            </div>

            <div class="formcon">
                <div class="yuzde30"><?php echo $LANG["fields"]["import-tld"]; ?></div>
                <div class="yuzde70">
                    <a class="lbtn" href="javascript:open_modal('NameSilo_import_tld');void 0;"><?php echo $LANG["import-tld-button"]; ?></a>
                    <div class="clear"></div>
                    <span class="kinfo"><?php echo $LANG["desc"]["import-tld-1"]; ?></span>
                </div>
            </div>


            <div class="clear"></div>
            <br>

            <div style="float:left;" class="guncellebtn yuzde30"><a id="NameSilo_testConnect" href="javascript:void(0);" class="lbtn"><i class="fa fa-plug" aria-hidden="true"></i> <?php echo $LANG["test-button"]; ?></a></div>

            <div style="float:right;" class="guncellebtn yuzde30"><a id="NameSilo_submit" href="javascript:void(0);" class="yesilbtn gonderbtn"><?php echo $LANG["save-button"]; ?></a></div>

        </form>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#NameSilo_testConnect").click(function(){
                    $("#NameSiloSettings input[name=controller]").val("test_connection");
                    MioAjaxElement($(this),{
                        waiting_text:waiting_text,
                        progress_text:progress_text,
                        result:"NameSilo_handler",
                    });
                });

                $("#NameSilo_submit").click(function(){
                    $("#NameSiloSettings input[name=controller]").val("settings");
                    MioAjaxElement($(this),{
                        waiting_text:waiting_text,
                        progress_text:progress_text,
                        result:"NameSilo_handler",
                    });
                });
            });

            function NameSilo_handler(result){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "error"){
                            if(solve.for != undefined && solve.for != ''){
                                $("#NameSiloSettings "+solve.for).focus();
                                $("#NameSiloSettings "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                $("#NameSiloSettings "+solve.for).change(function(){
                                    $(this).removeAttr("style");
                                });
                            }
                            if(solve.message != undefined && solve.message != '')
                                alert_error(solve.message,{timer:5000});
                        }else if(solve.status == "successful")
                            alert_success(solve.message,{timer:2500});
                    }else
                        console.log(result);
                }
            }
        </script>

    </div>

    <div id="NameSilo_tab-import" class="modules-tabs-content" style="display: none;">

        <div class="blue-info">
            <div class="padding15">
                <?php echo $LANG["import-note"]; ?>
            </div>
        </div>

        <script type="text/javascript">

            function NameSilo_import_handler(result){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "error"){
                            if(solve.for != undefined && solve.for != ''){
                                $("#NameSiloImport "+solve.for).focus();
                                $("#NameSiloImport "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                $("#NameSiloImport "+solve.for).change(function(){
                                    $(this).removeAttr("style");
                                });
                            }
                            if(solve.message != undefined && solve.message != '')
                                alert_error(solve.message,{timer:5000});
                        }else if(solve.status == "successful"){
                            alert_success(solve.message,{timer:2500});
                            setTimeout(function(){
                                window.location.href = window.location.href;
                            },2500);
                        }
                    }else
                        console.log(result);
                }
            }
            $(document).ready(function(){

                $("#NameSilo_import_submit").click(function(){
                    MioAjaxElement($(this),{
                        waiting_text:waiting_text,
                        progress_text:progress_text,
                        result:"NameSilo_import_handler",
                    });
                });

                $('#NameSilo_list_domains').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible":false,
                        },
                    ],
                    "lengthMenu": [
                        [10, 25, 50, -1], [10, 25, 50, "<?php echo ___("needs/allOf"); ?>"]
                    ],
                    responsive: true,
                    "language":{"url":"<?php echo APP_URI; ?>/<?php echo ___("package/code"); ?>/datatable/lang.json"}
                });

                $(".select-user").select2({
                    placeholder: "<?php echo __("admin/invoices/create-select-user"); ?>",
                    ajax: {
                        url: '<?php echo Controllers::$init->AdminCRLink("orders"); ?>?operation=select-users.json',
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public',
                            };
                            return query;
                        }
                    }
                });

                $(".select2-element").select2({
                    placeholder: "<?php echo ___("needs/select-your"); ?>",
                });

            });
        </script>
        <form action="<?php echo Controllers::$init->getData("links")["controller"]; ?>" method="post" id="NameSiloImport">
            <input type="hidden" name="operation" value="module_controller">
            <input type="hidden" name="module" value="NameSilo">
            <input type="hidden" name="controller" value="import">

            <table width="100%" id="NameSilo_list_domains" class="table table-striped table-borderedx table-condensed nowrap">
                <thead style="background:#ebebeb;">
                <tr>
                    <th align="center" data-orderable="false">#</th>
                    <th align="left" data-orderable="false"><?php echo __("admin/products/hosting-shared-servers-import-accounts-domain"); ?></th>
                    <th align="center" data-orderable="false"><?php echo __("admin/products/hosting-shared-servers-import-accounts-user"); ?></th>
                    <th align="center" data-orderable="false"><?php echo __("admin/products/hosting-shared-servers-import-accounts-start"); ?></th>
                    <th align="center" data-orderable="false"><?php echo __("admin/products/hosting-shared-servers-import-accounts-end"); ?></th>
                </tr>
                </thead>
                <tbody align="center" style="border-top:none;">
                <?php
                    $list   = $module->domains();
                    $i = 0;
                    if($list){
                        foreach($list AS $row){
                            $i++;
                            ?>
                            <tr<?php echo isset($row["order_id"]) && $row["order_id"] ? ' style="background: #c2edc2;opacity: 0.7;    filter: alpha(opacity=70);"' : ''; ?>>
                                <td align="left"><?php echo $i; ?></td>
                                <td align="left"><?php echo $row["domain"]; ?></td>
                                <td align="center">
                                    <?php
                                        if(isset($row["user_data"]) && $row["user_data"]){
                                            $user_link = Controllers::$init->AdminCRLink("users-2",['detail',$row["user_data"]["id"]]);
                                            $user_name           = Utility::short_text($row["user_data"]["full_name"],0,21,true);
                                            $user_company_name   = Utility::short_text($row["user_data"]["company_name"],0,21,true);

                                            $user_detail         = '<a href="'.$user_link.'" target="_blank"><strong title="'.$row["user_data"]["full_name"].'">'.$user_name.'</strong></a><br><span class="mobcomname" title="'.$row["user_data"]["company_name"].'">'.$user_company_name.'</span>';
                                            echo $user_detail;
                                        }else{
                                            ?>
                                            <select class="width200 select-user" name="data[<?php echo $row["domain"]; ?>][user_id]"></select>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td align="center">
                                    <?php echo $row["creation_date"] ? DateManager::format(Config::get("options/date-format"),$row["creation_date"]) : '-'; ?>
                                </td>
                                <td align="center">
                                    <?php echo $row["end_date"] ? DateManager::format(Config::get("options/date-format"),$row["end_date"]) : '-'; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                ?>
                </tbody>
            </table>

            <div class="clear"></div>
            <div class="guncellebtn yuzde20" style="float: right;">
                <a href="javascript:void(0);" id="NameSilo_import_submit" class="gonderbtn mavibtn"><?php echo $LANG["import-button"]; ?></a>
            </div>

        </form>

    </div>
</div>

<div id="NameSilo_import_tld" style="display: none;" data-izimodal-title="<?php echo $LANG["fields"]["import-tld"]; ?>">
    <script type="text/javascript">
        $(document).ready(function(){

            $("#NameSilo_import_tld_submit").on("click",function(){
                var request = MioAjax({
                    button_element:this,
                    action:"<?php echo Controllers::$init->getData("links")["controller"]; ?>",
                    method:"POST",
                    waiting_text:waiting_text,
                    progress_text:progress_text,
                    data:{
                        operation: "module_controller",
                        module: "NameSilo",
                        controller: "import-tld",
                    }
                },true,true);

                request.done(function(result){
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.for != undefined && solve.for != ''){
                                    $("#detailForm "+solve.for).focus();
                                    $("#detailForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                    $("#detailForm "+solve.for).change(function(){
                                        $(this).removeAttr("style");
                                    });
                                }
                                if(solve.message != undefined && solve.message != '')
                                    alert_error(solve.message,{timer:5000});
                            }else if(solve.status == "successful"){
                                alert_success(solve.message,{timer:2000});
                                if(solve.redirect != undefined && solve.redirect != ''){
                                    setTimeout(function(){
                                        window.location.href = solve.redirect;
                                    },2000);
                                }
                            }
                        }else
                            console.log(result);
                    }
                });

            });

        });
    </script>
    <div class="padding20">

        <p style="text-align: center; font-size: 17px;">
            <?php echo $LANG["desc"]["import-tld-2"]; ?>
        </p>

        <div align="center">
            <div class="yuzde50">
                <a class="yesilbtn gonderbtn" href="javascript:void 0;" id="NameSilo_import_tld_submit"><i class="fa fa-check" aria-hidden="true"></i> <?php echo ___("needs/ok"); ?></a>
            </div>
        </div>


    </div>
</div>