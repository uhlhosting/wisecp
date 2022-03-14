<!DOCTYPE html>
<html>
<head>
    <?php
        $options            = $order["options"];
        Utility::sksort($lang_list,"local");
        $plugins    = ['jquery-ui','select2','dataTables','voucher_codes'];
        include __DIR__.DS."inc".DS."head.php";

        $cancellation_request = false;

        if(isset($pending_events) && $pending_events)
        {
            foreach($pending_events AS $k=>$event)
            {
                if($event['name'] == "cancelled-product-request")
                {
                    $cancellation_request = $event;
                    unset($pending_events[$k]);
                }
            }
        }

    ?>

    <script type="text/javascript">
        $(document).ready(function(){

            var tab = _GET("content");
            if (tab != '' && tab != undefined) {
                $("#tab-content .tablinks[data-tab='" + tab + "']").click();
            } else {
                $("#tab-content .tablinks:eq(0)").addClass("active");
                $("#tab-content .tabcontent:eq(0)").css("display", "block");
            }

            $("#transferUser").select2({
                placeholder: "<?php echo __("admin/orders/detail-transfer-to-another-user-select"); ?>",
                ajax: {
                    url: '<?php echo $links["select-users.json"]; ?>',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    }
                }
            });

            $("#linkedProduct").select2({
                placeholder: "<?php echo ___("needs/none"); ?>",
                ajax: {
                    url: '<?php echo $links["controller"]; ?>?operation=select-linked-products.json',
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public',
                            none: 'true',
                        }
                        return query;
                    }
                }
            });

            $("#detailForm_submit").on("click",function(){
                MioAjaxElement($(this),{
                    waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                    progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                    result:"detailForm_handler",
                });
            });

            <?php if(!$privOperation): ?>
            $("#detailForm input,#detailForm select,textarea").attr("disabled",true);
            <?php endif; ?>

        });

        function detailForm_handler(result){
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
        }

        function delete_delivery_file(){
            $("#delivery-file").val('');
            $("#delivery-file-button").css("display","none");
            $("#delivery-file-click").remove();

            var request = MioAjax({
                action:"<?php echo $links["controller"]; ?>",
                method:"POST",
                data:{
                    operation:"delete_delivery_file",
                }
            },true,true);

            request.done(function(result){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "successful"){
                            alert_success(solve.message,{timer:2000});
                        }
                    }else
                        console.log(result);
                }
            });
        }

        function applyOperation(type){
            $("#content-detail").addClass("tab-blur-content");
            $("#operation-loading").fadeIn(500,function(){
            });

            if(type == "cancelled") close_modal("cancelled_confirm");

            var request = MioAjax({
                action: "<?php echo $links["list"]; ?>",
                method: "POST",
                data: {operation:"apply_operation",from:"detail",type:type,id:<?php echo $order["id"]; ?>}
            },true,true);

            request.done(function(result){

                $("#operation-loading").fadeOut(500,function(){
                    $("#content-detail").removeClass("tab-blur-content");
                });

                if(result){
                    if(result != ''){
                        var solve = getJson(result);
                        if(solve !== false){
                            if(solve.status == "error"){
                                if(solve.message != undefined && solve.message != ''){

                                    alert_error(solve.message,{timer:5000});

                                    if(solve.for != undefined && solve.for == "status"){
                                        $("#statusMsg").css("display","block");
                                        $("#statusMsg .statusMsg_text").html(solve.message);
                                    }
                                }

                            }else if(solve.status == "successful"){
                                $("#statusMsg").css("display","none");
                                alert_success(solve.message,{timer:3000});
                                if(solve.redirect != undefined && solve.redirect){
                                    setTimeout(function(){
                                        window.location.href = solve.redirect;
                                    },3000);
                                }
                            }
                        }else
                            console.log(result);
                    }
                }else console.log(result);
            });

        }

        function applyDelete(){
            open_modal("deleteModal",{
                title:"<?php echo __("admin/orders/delete-modal-title-list"); ?>"
            });

            $("#delete_ok").click(function(){
                var password = $('#password1').val();
                var request = MioAjax({
                    action: "<?php echo $links["controller"]; ?>",
                    method: "POST",
                    data: {operation:"apply_operation",from:"detail",type:"delete",id:<?php echo $order["id"]; ?>}
                },true,true);

                request.done(function(result){
                    if(result){
                        if(result != ''){
                            var solve = getJson(result);
                            if(solve !== false){
                                if(solve.status == "error"){

                                    if(solve.message != undefined && solve.message != ''){

                                        alert_error(solve.message,{timer:5000});

                                        if(solve.for != undefined && solve.for == "status"){
                                            $("#statusMsg").css("display","block");
                                            $("#statusMsg .statusMsg_text").html(solve.message);
                                        }
                                    }
                                }else if(solve.status == "successful"){
                                    $("#statusMsg").css("display","none");
                                    alert_success(solve.message,{timer:3000});
                                    close_modal("deleteModal");
                                    if(solve.redirect != undefined && solve.redirect){
                                        setTimeout(function(){
                                            window.location.href = solve.redirect;
                                        },3000);
                                    }
                                }
                            }else
                                console.log(result);
                        }
                    }else console.log(result);
                });

            });

            $("#delete_no").click(function(){
                close_modal("deleteModal");
                $("#password1").val('');
            });

        }

        function _EventOK(id,el){
            var request = MioAjax({
                waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                button_element:el !== undefined ? el : $("#event_"+id+" .event-ok-button"),
                action:"<?php echo $links["controller"]; ?>",
                method:"POST",
                data:{operation:"event_ok",id:id}
            },true,true);
            request.done(function(result){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "error")
                        {
                            if(solve.message != undefined && solve.message != '')
                                alert_error(solve.message,{timer:5000});
                        }
                        else if(solve.status == "successful"){
                            $("#event_"+id).fadeOut(300);
                            if(id === <?php echo  $cancellation_request ? $cancellation_request["id"] : 0; ?>)
                                $(".cancellation_request_operations").fadeOut(300);
                        }
                    }else
                        console.log(result);
                }
            });
        }
        function _EventDel(id,el){
            var request = MioAjax({
                waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                button_element:el !== undefined ? el : $("#event_"+id+" .event-ok-button"),
                action:"<?php echo $links["controller"]; ?>",
                method:"POST",
                data:{operation:"event_del",id:id}
            },true,true);
            request.done(function(result){
                if(result != ''){
                    var solve = getJson(result);
                    if(solve !== false){
                        if(solve.status == "error")
                        {
                            if(solve.message != undefined && solve.message != '')
                                alert_error(solve.message,{timer:5000});
                        }
                        else if(solve.status == "successful"){
                            $("#event_"+id).fadeOut(300);
                            if(id === <?php echo  $cancellation_request ? $cancellation_request["id"] : 0; ?>)
                                $("#CancellationRequestWrap").fadeOut(300);
                        }
                    }else
                        console.log(result);
                }
            });
        }
    </script>
</head>
<body>

<div id="invoices_modal" style="display: none;" data-izimodal-title="<?php echo __("admin/orders/detail-all-invoices"); ?>">
    <script type="text/javascript">
        var invoices;
        function view_all_invoices_btn(){
            open_modal("invoices_modal",{width:'1200px'});
            if(invoices) invoices.destroy();
            if(!invoices){
                invoices = $('#invoicesTable').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible":false,
                        },
                    ],
                    "lengthMenu": [
                        [10, 25, 50, -1], [10, 25, 50, "<?php echo ___("needs/allOf"); ?>"]
                    ],
                    "searching" : false,
                    responsive: true,
                    "language":{"url":"<?php echo APP_URI; ?>/<?php echo ___("package/code"); ?>/datatable/lang.json"}
                });
            }
        }
    </script>
    <div class="padding20">
        <table width="100%" id="invoicesTable">
            <thead style="background:#ebebeb;">
            <tr>
                <th align="left">#</th>
                <th align="center" data-orderable="false"><?php echo __("admin/invoices/bills-th-id"); ?></th>
                <th align="center" data-orderable="false"><?php echo __("admin/invoices/bills-th-amount"); ?></th>
                <th align="center" data-orderable="false"><?php echo __("admin/invoices/bills-th-date"); ?></th>
                <th align="center" data-orderable="false"><?php echo __("admin/invoices/bills-th-status"); ?></th>
                <th align="center" data-orderable="false"></th>
            </tr>
            </thead>
            <tbody align="center" style="border-top:none;">
            <?php
                if(isset($invoices) && $invoices){
                    foreach($invoices AS $k=>$row){
                        $id     = $row["id"];
                        $amount_detail       = Money::formatter_symbol($row["subtotal"],$row["currency"]);
                        if($row["status"] != "unpaid")
                            $amount_detail   = Money::formatter_symbol($row["total"],$row["currency"]);

                        if($row["status"] == "paid" || $row["status"] == "taxed" || $row["status"] == "untaxed")
                            $date_detail = DateManager::format("d/m/Y - H:i",$row["datepaid"]);
                        elseif($row["status"] == "unpaid")
                            $date_detail = DateManager::format("d/m/Y - H:i",$row["duedate"]);
                        elseif($row["status"] == "cancelled-refund"){
                            if(substr($row["refunddate"],0,4) == "1881")
                                $date_detail = DateManager::format("d/m/Y - H:i",$row["duedate"]);
                            else
                                $date_detail = DateManager::format("d/m/Y - H:i",$row["refunddate"]);
                        }
                        else{
                            if($row["status"] == "paid") $date_detail = '<strong>'.__("admin/invoices/bills-th-datepaid").'</strong><br>'.DateManager::format("d/m/Y - H:i",$row["datepaid"]);
                            elseif($row["status"] == "unpaid" || $row["status"] == "waiting") $date_detail = '<strong>'.__("admin/invoices/bills-th-duedate").'</strong><br>'.DateManager::format("d/m/Y - H:i",$row["duedate"]);
                            elseif($row["status"] == "refund") $date_detail = '<strong>'.__("admin/invoices/bills-th-refunddate").'</strong><br>'.DateManager::format("d/m/Y - H:i",$row["refunddate"]);
                            else
                                $date_detail = '<strong>'.__("admin/invoices/bills-th-cdate").'</strong><br>'.DateManager::format("d/m/Y - H:i",$row["cdate"]);
                        }

                        $detail_link = Controllers::$init->AdminCRLink("invoices-2",["detail",$id]);
                        ?>
                        <tr>
                            <td><?php echo $k; ?></td>
                            <td><?php echo "#".$id; ?></td>
                            <td><?php echo $amount_detail; ?></td>
                            <td><?php echo $date_detail; ?></td>
                            <td><?php echo $invoice_situations[$row["status"]]; ?></td>
                            <td><a href="<?php echo $detail_link; ?>" target="_blank" data-tooltip="<?php echo ___("needs/button-edit"); ?>" class="sbtn"><i class="fa fa-edit" aria-hidden="true"></i></a></td>
                        </tr>
                        <?php
                    }
                }
            ?>
            </tbody>
        </table>

    </div>
</div>

<div id="deleteModal" style="display: none;">
    <div class="padding20">
        <div align="center">

            <p id="deleteModal_text1"><?php echo __("admin/orders/delete-are-youu-sure-list"); ?></p>
            <?php if(isset($order["options"]["module"])): ?>
                <p id="deleteModal_text2"><?php echo __("admin/orders/delete-are-youu-sure-list-note"); ?></p>
            <?php endif; ?>
            <div class="clear"></div>
            <div class="yuzde50">
                <a href="javascript:void(0);" id="delete_ok" class="gonderbtn redbtn"><i class="fa fa-check"></i> <?php echo __("admin/orders/delete-ok"); ?></a>
            </div>
            <div class="yuzde50">
                <a href="javascript:void(0);" id="delete_no" class="gonderbtn yesilbtn"><i class="fa fa-ban"></i> <?php echo __("admin/orders/delete-no"); ?></a>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__.DS."inc/header.php"; ?>

<div id="wrapper">

    <div class="icerik-container">
        <div class="icerik">

            <div class="icerikbaslik">
                <h1><strong><?php echo __("admin/orders/page-software-detail",['{name}' => $order["name"]]); ?></strong></h1>
                <?php include __DIR__.DS."inc".DS."breadcrumb.php"; ?>
            </div>


            <div id="tab-content"><!-- tab wrap content start -->
                <ul class="tab">
                    <li>
                        <a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'detail','content')" data-tab="detail"><i class="fa fa-info" aria-hidden="true"></i>  <?php echo __("admin/orders/detail-content-tab-detail"); ?></a>
                    </li>

                    <li>
                        <a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'addons','content')" data-tab="addons"><i class="fa fa-plus" aria-hidden="true"></i>  <?php echo __("admin/orders/detail-content-tab-addons"); ?></a>
                    </li>

                    <li>
                        <a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'requirements','content')" data-tab="requirements"><i class="fa fa-check-square" aria-hidden="true"></i>  <?php echo __("admin/orders/detail-content-tab-requirements"); ?></a>
                    </li>

                    <li>
                        <a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'history','content')" data-tab="history"><i class="fa fa-history" aria-hidden="true"></i>  <?php echo __("admin/orders/detail-content-tab-history"); ?></a>
                    </li>

                    <?php if($privOperation && $order["period"] != "none" && $order["status"] == "active"): ?>
                        <li>
                            <a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'cancelled','content')" data-tab="cancelled"><i class="fa fa-plus-circle" aria-hidden="true"></i>  <?php echo __("admin/orders/detail-content-tab-cancelled"); ?></a>
                        </li>
                    <?php endif; ?>

                </ul>

                <div id="operation-loading" class="blur-text" style="display: none">
                    <i class="fa fa-cog loadingicon" aria-hidden="true"></i>
                    <div class="clear"></div>
                    <strong><?php echo __("admin/orders/list-row-operation-processing"); ?></strong>
                </div>

                <div id="content-detail" class="tabcontent"><!-- detail tab content start -->

                    <div class="adminpagecon">

                        <?php
                            if($cancellation_request)
                            {
                                $cancellation_request["data"] = Utility::jdecode($cancellation_request["data"],true);
                                ?>
                                <div class="red-info" id="CancellationRequestWrap">
                                    <div class="padding20">
                                        <i class="fa fa-meh-o"></i>

                                        <p>
                                            <strong style="display: block;margin-bottom: 10px;font-size: 18px;"><?php echo __("admin/events/cancelled-product-request"); ?></strong>
                                            <span style="display: block;margin-bottom: 10px;">   <strong><?php echo __("admin/orders/cancellation-reason"); ?></strong>: <?php echo $cancellation_request["data"]["reason"]; ?></span>

                                            <span style="display: block;"> <strong><?php echo __("admin/orders/cancellation-urgency"); ?></strong>: <?php echo __("admin/orders/cancellation-urgency-".$cancellation_request["data"]["urgency"]); ?></span>
                                            <span style="display:block;margin-bottom:15px;">
                                                <strong><?php echo __("admin/tools/reminders-creation-date"); ?></strong>: <?php echo DateManager::format(Config::get("options/date-format")." - H:i",$cancellation_request["cdate"])?></span>

                                            <a style="<?php echo $cancellation_request["status"] == "approved" ? "display: none;" : ''; ?>" class="red lbtn cancellation_request_operations" href="javascript:void 0;" onclick="_EventOK(<?php echo $event["id"]; ?>,this);"><?php echo __("admin/orders/detail-operation-button-approve"); ?></a>
                                            <a class="green lbtn" href="javascript:void 0;" onclick="_EventDel(<?php echo $event["id"]; ?>,this);"><?php echo ___("needs/button-delete"); ?></a>
                                            <a class="lbtn" href="<?php echo Controllers::$init->AdminCRLink("tickets-1",["create"]); ?>?user_id=<?php echo $order["owner_id"]; ?>&order_id=<?php echo $order["id"]; ?>"><?php echo __("admin/index/menu-tickets-create"); ?></a>

                                        </p>

                                    </div>
                                </div>
                                <?php
                            }
                        ?>


                        <form action="<?php echo $links["controller"]; ?>" method="post" id="detailForm" enctype="multipart/form-data">
                            <input type="hidden" name="operation" value="update_detail">

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-content-tab-detail-user"); ?></div>
                                <div class="yuzde70">
                                    <a href="<?php echo $links["detail-user-link"]; ?>" target="_blank">
                                        <strong><?php echo $user["full_name"]; ?></strong>
                                        <?php echo $user["company_name"] ? "(".$user["company_name"].")" : ''; ?>
                                        <?php
                                            if($user['blacklist']){
                                                ?>
                                                <span class="flaggeduser"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?php echo __("admin/orders/user-blacklist"); ?></span>
                                                <?php
                                            }
                                        ?>
                                    </a>
                                </div>
                            </div>

                             <?php if($privOperation && $order["status"] != "waiting"): ?>
                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/detail-transfer-to-another-user"); ?></div>
                                    <div class="yuzde70">
                                        <select name="transfer_user" id="transferUser" style="width: 100%;"></select>
                                    </div>
                                </div>

                                 <div class="formcon">
                                     <div class="yuzde30">
                                         <?php echo __("admin/orders/detail-linked-product"); ?>

                                         <?php
                                             if(isset($product) && $product)
                                             {
                                                 ?>
                                                 <a class="sbtn" href="<?php echo Controllers::$init->AdminCRLink("products-2",[$order["type"],"edit"])."?id=".$product["id"]; ?>"><i class="fa fa-external-link"></i></a>
                                                 <?php
                                             }
                                         ?>
                                     </div>
                                     <div class="yuzde70">
                                         <select name="product_id" id="linkedProduct" style="width: 100%;">
                                             <?php
                                                 if(isset($product) && $product){
                                                     ?>
                                                     <option value="<?php echo $product["id"]; ?>"><?php echo $product["title"]; ?></option>
                                                     <?php
                                                 }
                                             ?>
                                         </select>
                                     </div>
                                 </div>
                            <?php endif; ?>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-content-tab-detail-invoice"); ?></div>
                                <div class="yuzde70">
                                    <?php if(isset($invoice) && $invoice): ?>
                                        <a href="<?php echo $links["invoice-link"]; ?>" target="_blank">
                                            <?php echo "#".$invoice["id"]; ?>
                                        </a>
                                    <?php endif;?>

                                    <a class="lbtn" href="javascript:void 0;" onclick="view_all_invoices_btn();"><?php echo __("admin/orders/detail-view-all-invoices"); ?></a>


                                    <?php
                                        if($order["period"] !== "none"){
                                            ?>
                                            <a class="lbtn" href="javascript:void 0;" onclick="generate_renew_invoice_btn(this);"><?php echo __("admin/orders/detail-generate-renew-invoice"); ?></a>

                                            <script type="text/javascript">
                                                function generate_renew_invoice_btn(btn_el){
                                                    var request = MioAjax({
                                                        waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                                        progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                                                        button_element: btn_el,
                                                        action:"<?php echo $links["controller"]; ?>",
                                                        method:"POST",
                                                        data:{operation:"generate_renew_invoice"}
                                                    },true,true);

                                                    request.done(function(result){
                                                        if(result !== ''){
                                                            var solve = getJson(result);
                                                            if(solve !== false){
                                                                if(solve.status == "error"){
                                                                    if(solve.message != undefined && solve.message != '')
                                                                        alert_error(solve.message,{timer:5000});
                                                                }else if(solve.status == "successful"){
                                                                    alert_success(solve.message,{timer:2000});
                                                                    if(solve.redirect !== undefined && solve.redirect !== ''){
                                                                        setTimeout(function(){
                                                                            window.location.href = solve.redirect;
                                                                        },2000);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    });
                                                }
                                            </script>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-ordernum"); ?></div>
                                <div class="yuzde70">
                                    #<?php echo $order["id"]; ?>
                                </div>
                            </div>

                            <?php if($privOperation): ?>
                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/detail-operation"); ?></div>
                                    <div class="yuzde70">

                                        <?php
                                            if(isset($download_link) && $download_link)
                                            {
                                                ?>
                                                <a href="<?php echo $download_link; ?>" class="blue lbtn"><i class="fa fa-download"></i> <?php echo __("website/account_products/download-button"); ?></a>
                                                <?php
                                            }
                                        ?>

                                        <?php if($order["status"] == "waiting"): ?>
                                            <input type="radio" class="radio-custom" id="apply_approve" name="apply" value="approve">
                                            <label class="radio-custom-label" for="apply_approve" style="margin-left: 10px;"><?php echo __("admin/orders/detail-operation-button-approve"); ?></label>
                                        <?php endif; ?>

                                        <?php if($order["status"] != "waiting" && $order["status"] != "active"): ?>
                                            <input type="radio" class="radio-custom" id="apply_active" name="apply" value="active">
                                            <label class="radio-custom-label" for="apply_active" style="margin-left: 10px;"><?php echo __("admin/orders/detail-operation-button-active"); ?></label>
                                        <?php endif; ?>

                                        <?php if($order["status"] != "suspended" && $order["status"] != "waiting"): ?>
                                            <input type="radio" class="radio-custom" id="apply_suspended" name="apply" value="suspended">
                                            <label class="radio-custom-label" for="apply_suspended" style="margin-left: 10px;"><?php echo __("admin/orders/detail-operation-button-suspended"); ?></label>
                                        <?php endif; ?>

                                        <?php if($order["status"] != "cancelled" && $order["status"] != "waiting"): ?>
                                            <input type="radio" class="radio-custom" id="apply_cancelled" name="apply" value="cancelled">
                                            <label class="radio-custom-label" for="apply_cancelled" style="margin-left: 10px;"><?php echo __("admin/orders/detail-operation-button-cancelled"); ?></label>
                                        <?php endif; ?>

                                        <?php if($privDelete): ?>
                                            <input type="radio" class="radio-custom" id="apply_delete" name="apply" value="delete">
                                            <label class="radio-custom-label" for="apply_delete" style="margin-left: 10px;"><?php echo __("admin/orders/detail-operation-button-delete"); ?></label>
                                        <?php endif; ?>

                                        <div class="clear"></div>
                                        <div id="apply_note_cancelled" style="display: none;" class="red-info apply-notes">
                                            <div class="padding15">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                <p><?php echo __("admin/orders/detail-operation-cancelled-info"); ?></p>
                                            </div>
                                        </div>


                                        <script type="text/javascript">
                                            $(document).ready(function(){
                                                $("input[name=apply]").change(function(){
                                                    var val = $(this).val();
                                                    $(".apply-notes").css("display","none");
                                                    if(document.getElementById("apply_note_"+val))
                                                        $("#apply_note_"+val).fadeIn(300);
                                                });
                                            });
                                        </script>

                                    </div>
                                </div>
                            <?php endif; ?>


                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-status"); ?></div>
                                <div class="yuzde70">
                                    <?php echo $situations[$order["status"]]; ?>
                                    <div class="clear"></div>
                                    <div class="red-info" id="statusMsg" style="<?php echo $order["status_msg"] ? '' : 'display:none;'; ?>">
                                        <div class="padding20">
                                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                            <p class="statusMsg_text"><?php echo $order["status_msg"]; ?></p>
                                            <a class="lbtn status-ok-button" href="javascript:void 0;" onclick="StatusOK(this);"><?php echo ___("needs/ok"); ?></a>
                                            <script type="text/javascript">
                                                function StatusOK(el)
                                                {
                                                    var request = MioAjax({
                                                        waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                                        button_element:el,
                                                        action:"<?php echo $links["controller"]; ?>",
                                                        method:"POST",
                                                        data:{operation:"status_ok"}
                                                    },true,true);
                                                    request.done(function(result){
                                                        if(result != ''){
                                                            var solve = getJson(result);
                                                            if(solve !== false){
                                                                if(solve.status == "error")
                                                                {
                                                                    if(solve.message != undefined && solve.message != '')
                                                                        alert_error(solve.message,{timer:5000});
                                                                }
                                                                else if(solve.status == "successful"){
                                                                    $("#statusMsg").fadeOut(300);
                                                                    $("#statusMsg_text").html('');
                                                                }
                                                            }else
                                                                console.log(result);
                                                        }
                                                    });
                                                }
                                            </script>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                    <?php
                                        if(isset($pending_events) && $pending_events){
                                            foreach($pending_events AS $k=>$event){
                                                ?>
                                                <div class="order-event-item" id="event_<?php echo $event["id"]; ?>">
                                                    <?php echo Events::getMessage($event); ?>
                                                    <a class="lbtn event-ok-button" href="javascript:_EventOK(<?php echo $event["id"]; ?>);void 0;"><?php echo ___("needs/ok"); ?></a>
                                                </div>
                                                <?php
                                            }
                                        }
                                    ?>

                                </div>
                            </div>

                            <?php
                                $license_type = isset($product["options"]["license_type"]) && $product["options"]["license_type"] ? $product["options"]["license_type"] : 'domain';

                            ?>

                            <?php if($license_type == "domain"): ?>
                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/detail-license-domain"); ?></div>
                                    <div class="yuzde70">
                                        <input name="domain" type="text" value="<?php echo isset($order["options"]["domain"]) ? $order["options"]["domain"] : ''; ?>">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($license_type == "key"): ?>
                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/detail-license-code"); ?></div>
                                    <div class="yuzde70">
                                        <input name="code" id="license-key" type="text" value="<?php echo isset($order["options"]["code"]) ? $order["options"]["code"] : ''; ?>" style="width: 200px;">
                                        <a class="lbtn" href="javascript:generate_license_key();void 0;"><i class="fa fa-refresh"></i> <?php echo __("admin/products/software-licensing-8"); ?></a>
                                        <div class="clear"></div>
                                        <span class="kinfo"><?php echo __("admin/orders/detail-license-code-info"); ?></span>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    function generate_license_key()
                                    {
                                        var prefix              = "<?php echo isset($product["options"]["key_prefix"]) && $product["options"]["key_prefix"] ? $product["options"]["key_prefix"] : ''; ?>";
                                        var character_sets      = '';
                                        var dashes              = <?php echo isset($product["options"]["key_dashes"]) && $product["options"]["key_dashes"] ? 'true' : 'false'; ?>;
                                        var character_length    = <?php echo isset($product["options"]["key_length"]) && $product["options"]["key_length"] ? $product["options"]["key_length"] : 15; ?>;
                                        var character_l         = 'abcdefghijklmnopqrstuvwxyz';
                                        var character_u         = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                        var character_d         = '0123456789';
                                        var character_s         = '+*!@';

                                        <?php
                                        echo isset($product["options"]["key_l"]) && $product["options"]["key_l"] ? 'character_sets += character_l;' : '';
                                        echo isset($product["options"]["key_u"]) && $product["options"]["key_u"] ? 'character_sets += character_u;' : '';
                                        echo isset($product["options"]["key_d"]) && $product["options"]["key_d"] ? 'character_sets += character_d;' : '';
                                        echo isset($product["options"]["key_s"]) && $product["options"]["key_s"] ? 'character_sets += character_s;' : '';
                                        ?>

                                        if(isNaN(character_length)) character_length = 15;

                                        if(character_sets === '')
                                        {
                                            $("#license-key").val("<?php echo __("admin/products/software-licensing-30"); ?>");
                                            return false;
                                        }

                                        if(prefix.length > 0)
                                        {
                                            prefix += "-";
                                            character_length -= 1;
                                        }

                                        if(character_length < (15 - (prefix.length > 0 ? 1 : 0)))
                                        {
                                            $("#license-key").val("<?php echo __("admin/products/software-licensing-31"); ?>");
                                            return false;
                                        }

                                        var key = voucher_codes.generate({length:character_length,charset: character_sets});
                                        key     = key[0];

                                        if(dashes)
                                        {
                                            var key_n    = '';
                                            var key_c    = 0;
                                            var key_r    = character_length;
                                            $(key.split('')).each(function(k,v){
                                                key_r--;
                                                if(key_c === 4)
                                                {
                                                    key_n += key_r === 0 ? v : '-';
                                                    key_c = 0;
                                                }
                                                else
                                                {
                                                    key_n += v;
                                                    key_c++;
                                                }
                                            });
                                            key             = key_n;
                                            key             = key.rtrim('-');
                                        }


                                        if(prefix.length > 0)
                                            key  = prefix + key;

                                        $("#license-key").val(key);
                                    }
                                </script>

                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/server-ip"); ?></div>
                                    <div class="yuzde70">
                                        <input type="text" name="ip" value="<?php echo isset($options["ip"]) ? $options["ip"] : ''; ?>">
                                    </div>
                                </div>

                                <?php
                                    if(isset($product["options"]["license_parameters"]) && $product["options"]["license_parameters"])
                                    {
                                        foreach($product["options"]["license_parameters"] AS $k => $v)
                                        {
                                            if($k == "ip") continue;
                                            ?>
                                            <div class="formcon">
                                                <div class="yuzde30"><?php echo $v["name"]; ?></div>
                                                <div class="yuzde70">
                                                    <input type="text" name="license_parameters[<?php echo $k; ?>]" value="<?php echo isset($options["license_parameters"][$k]) ? $options["license_parameters"][$k] : ''; ?>">
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                ?>
                            <?php endif; ?>


                            <?php if($license_type == "domain"): ?>
                                <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-change-domain"); ?></div>
                                <div class="yuzde70">

                                    <input<?php echo isset($order["options"]["change-domain"]) && $order["options"]["change-domain"] ? ' checked' : ''; ?> id="change-domain" type="checkbox" name="change-domain" value="1" class="sitemio-checkbox">
                                    <label for="change-domain" class="sitemio-checkbox-label"></label>
                                    <span class="kinfo"><?php echo __("admin/products/add-new-product-change-domain-desc"); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-download-file"); ?></div>
                                <div class="yuzde70">
                                    <input id="delivery-file" type="file" name="delivery-file" onchange="if($(this).val() == '') $('#delivery-file-button').hide(1); else $('#delivery-file-button').show(1); ">
                                    <div id="delivery-file-button" style="<?php echo $delivery_file ? '' : 'display: none;'; ?>">
                                        <?php if($delivery_file): ?>
                                            <a id="delivery-file-click" href="<?php echo $delivery_file; ?>" target="_blank" class="blue sbtn"><i class="fa fa-download" aria-hidden="true"></i></a>
                                        <?php endif; ?>
                                        <a href="javascript:delete_delivery_file();void 0;" class="red sbtn"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </div>
                                    <br><span class="kinfo"><?php echo __("admin/orders/detail-download-file-info"); ?></span>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-product-name"); ?></div>
                                <div class="yuzde70">
                                    <input name="name" type="text" value="<?php echo $order["name"]; ?>">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-product-group"); ?></div>
                                <div class="yuzde70">
                                    <a href="<?php echo $links["group-link"]; ?>" target="_blank">
                                        <?php echo $order["options"]["local_group_name"]; ?>
                                    </a>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-product-category"); ?></div>
                                <div class="yuzde70">
                                    <?php if($category): ?>
                                        <a href="<?php echo $links["category-link"]; ?>" target="_blank">
                                            <?php echo $order["options"]["local_category_name"]; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo isset($order["options"]["local_category_name"]) ? $order["options"]["local_category_name"] : ___("needs/none"); ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-cdate"); ?></div>
                                <div class="yuzde70">
                                    <input class="yuzde25" id="cdate" name="cdate" type="date" value="<?php echo DateManager::format("Y-m-d",$order["cdate"])?>" placeholder="YYYY-MM-DD">
                                    <input class="yuzde25" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="ctime" value="<?php echo DateManager::format("H:i",$order["cdate"]); ?>">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-renewaldate"); ?></div>
                                <div class="yuzde70">
                                    <input class="yuzde25" id="renewaldate" name="renewaldate" type="date" value="<?php echo substr($order["renewaldate"],0,4) != "1881" ? DateManager::format("Y-m-d",$order["renewaldate"]) : ''; ?>" placeholder="YYYY-MM-DD">
                                    <input class="yuzde25" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="renewaltime" value="<?php echo substr($order["renewaldate"],0,4) != "1881" ? DateManager::format("H:i",$order["renewaldate"]) : ''; ?>">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-duedate"); ?></div>
                                <div class="yuzde70">
                                    <input class="yuzde25" id="duedate" name="duedate" type="date" value="<?php echo substr($order["duedate"],0,4) != "1881" ? DateManager::format("Y-m-d",$order["duedate"]) : '';?>" placeholder="YYYY-MM-DD">
                                    <input class="yuzde25" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="duetime" value="<?php echo substr($order["duedate"],0,4) != "1881" ? DateManager::format("H:i",$order["duedate"]) : ''; ?>">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30">
                                    <?php echo __("admin/orders/detail-suspend-date"); ?>
                                    <div class="clear"></div>
                                    <span class="kinfo"><?php echo __("admin/orders/detail-suspend-date-info"); ?></span>
                                </div>
                                <div class="yuzde70">
                                    <input class="yuzde25" id="suspend_date" name="suspend_date" type="date" value="<?php echo substr($order["suspend_date"],0,4) == "0000" || substr($order["suspend_date"],0,4) == "1881" ? '' : $order["suspend_date"]; ?>" placeholder="YYYY-MM-DD">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30">
                                    <?php echo __("admin/orders/detail-cancel-date"); ?>
                                    <div class="clear"></div>
                                    <span class="kinfo"><?php echo __("admin/orders/detail-cancel-date-info"); ?></span>
                                </div>
                                <div class="yuzde70">
                                    <input class="yuzde25" id="cancel_date" name="cancel_date" type="date" value="<?php echo substr($order["cancel_date"],0,4) == "0000" || substr($order["cancel_date"],0,4) == "1881" ? '' : $order["cancel_date"]; ?>" placeholder="YYYY-MM-DD">
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-process-exemption"); ?></div>
                                <div class="yuzde70">
                                    <input class="yuzde50" id="process_exemption_date" name="process_exemption_date" type="date" value="<?php echo substr($order["process_exemption_date"],0,4) != "1881" ? DateManager::format("Y-m-d",$order["process_exemption_date"]) : '';?>" placeholder="YYYY-MM-DD">
                                    <div class="clear"></div>
                                    <span class="kinfo"><?php echo __("admin/orders/detail-process-exemption-info"); ?></span>

                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-pmethod"); ?></div>
                                <div class="yuzde70">
                                    <select name="pmethod">
                                        <option value="none"><?php echo ___("needs/none"); ?></option>
                                        <?php
                                            if($pmethods){
                                                foreach($pmethods AS $k=>$v){
                                                    ?><option<?php echo $k == $order["pmethod"] ? ' selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php
                                if(isset($subscription) && $subscription)
                                {
                                    ?>
                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/link-subscription"); ?></div>
                                        <div class="yuzde70" id="subscription_content">

                                            <div id="subscription_loader">
                                                <div class="load-wrapp">
                                                    <p style="margin-bottom:20px"><strong><?php echo ___("needs/processing"); ?>...</strong><br><?php echo ___("needs/please-wait"); ?></p>
                                                    <div class="load-7">
                                                        <div class="square-holder">
                                                            <div class="square"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $.get("<?php echo $links["controller"]; ?>?bring=subscription_detail",function(res){
                                                $("#subscription_loader").html(res);
                                            });
                                        });
                                        function cancel_subscription(el)
                                        {
                                            if(!confirm("<?php echo ___("needs/apply-are-you-sure"); ?>")) return false;
                                            var request = MioAjax({
                                                button_element:el,
                                                waiting_text: "<?php echo __("website/others/button1-pending"); ?>",
                                                action: "<?php echo $links["controller"]; ?>",
                                                method: "POST",
                                                data:{
                                                    operation: "cancel_subscription",
                                                    id: <?php echo $subscription["id"]; ?>,
                                                    order_id: <?php echo $order["id"]; ?>
                                                }
                                            },true,true);
                                            request.done(function(result){
                                                var solve = getJson(result);
                                                if(solve !== undefined && solve !== false)
                                                {
                                                    if(solve.status === "error")
                                                        alert_error(solve.message,{timer:4000});
                                                    else if(solve.status === "successful")
                                                    {
                                                        window.location.href = '<?php echo $links["controller"]; ?>';
                                                    }
                                                }
                                            });
                                        }
                                    </script>
                                <?php
                                    }
                                    else
                                    {
                                ?>
                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/subscription-identifier"); ?></div>
                                        <div class="yuzde70">
                                            <input type="text" name="subscription[identifier]" placeholder="" value="">
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-pricing-type"); ?></div>
                                <div class="yuzde70">
                                    <select name="pricing-type">
                                        <?php
                                            $pricing_type = false;
                                            if(isset($options["pricing-type"]))
                                                $pricing_type = $options["pricing-type"];
                                        ?>
                                        <option value="1"<?php echo !$pricing_type || $pricing_type == 1 ? ' selected' : ''; ?>><?php echo __("admin/orders/detail-pricing-type-1"); ?></option>
                                        <option value="2"<?php echo $pricing_type == 2 ? ' selected' : ''; ?>><?php echo __("admin/orders/detail-pricing-type-2"); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-pricing"); ?></div>
                                <div class="yuzde70">

                                    <input class="yuzde15" name="period_time" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-period"); ?>" value="<?php echo $order["period_time"]; ?>"> -
                                    <select class="yuzde15" name="period">
                                        <?php
                                            foreach(___("date/periods") AS $k=>$v){
                                                ?>
                                                <option<?php echo $order["period"] == $k ? ' selected' : ''; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select> -
                                    <input class="yuzde15" name="amount" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-amount"); ?>" value="<?php echo $order["amount"] ? Money::formatter($order["amount"],$order["amount_cid"]) : ''; ?>" onkeypress='return event.charCode==44 || event.charCode==46 || event.charCode>= 48 &&event.charCode<= 57'> -
                                    <select class="yuzde20" name="amount_cid">
                                        <?php
                                            foreach(Money::getCurrencies($order["amount_cid"]) AS $curr){
                                                ?>
                                                <option<?php echo $order["amount_cid"] == $curr["id"] ? ' selected' : ''; ?> value="<?php echo $curr["id"]; ?>"><?php echo $curr["name"]." (".$curr["code"].")"; ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                    <div class="clear"></div>

                                </div>
                            </div>

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/detail-notes"); ?></div>
                                <div class="yuzde70">
                                    <textarea name="notes" placeholder="<?php echo __("admin/orders/detail-notes-ex"); ?>"><?php echo $order["notes"]; ?></textarea>
                                </div>
                            </div>


                            <?php if($privOperation): ?>
                                <div style="float:right;margin-bottom:20px;" class="guncellebtn yuzde30">
                                    <a id="detailForm_submit" class="yesilbtn gonderbtn" href="javascript:void(0);"><?php echo __("admin/orders/update-button"); ?></a>
                                </div>
                            <?php endif; ?>


                        </form>


                        <div class="clear"></div>
                    </div>


                    <div class="clear"></div>
                </div><!-- detail tab content end -->

                <div id="content-addons" class="tabcontent"><!-- addons tab content start -->

                    <div id="statusMessage" style="display: none;" data-izimodal-title="<?php echo __("admin/orders/modal-status-message"); ?>">
                        <div class="padding20">
                            <div class="status-message-text"></div>
                        </div>
                    </div>

                    <script type="text/javascript">
                        var addons;
                        $(document).ready(function(){
                            addons = $('#myAddons-table').DataTable({
                                'createdRow': function( row, data, dataIndex ) {

                                    if($(".have-event",row).length>0)
                                        $(row).addClass('tr-have-event');
                                },
                                "columnDefs": [
                                    {
                                        "targets": [0],
                                        "visible":false,
                                        "searchable": false
                                    },
                                ],
                                "aaSorting" : [[0, 'asc']],
                                "lengthMenu": [
                                    [10, 25, 50, -1], [10, 25, 50, "<?php echo ___("needs/allOf"); ?>"]
                                ],
                                "bProcessing": true,
                                "bServerSide": true,
                                "sAjaxSource": "<?php echo $links["ajax-addons"]; ?>",
                                responsive: true,
                                "language":{"url":"<?php echo APP_URI; ?>/<?php echo ___("package/code"); ?>/datatable/lang.json"}
                            });
                        });
                    </script>
                    <div id="addon-operation-loading" class="blur-text" style="display: none">
                        <i class="fa fa-cog loadingicon" aria-hidden="true"></i>
                        <div class="clear"></div>
                        <strong><?php echo __("admin/orders/list-row-operation-processing"); ?></strong>
                    </div>

                    <div class="adminpageconx" id="AddonList">

                        <div id="addMyAddon" style="display: none;" data-izimodal-title="<?php echo __("admin/orders/my-addons-add-new-addon"); ?>">
                            <div class="padding20">

                                <form id="addMyAddonForm" action="<?php echo $links["controller"]; ?>" method="post">
                                    <input type="hidden" name="operation" value="add_addon">

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-addon-info"); ?></div>
                                        <div class="yuzde70">
                                            <script type="text/javascript">
                                                function change_addon_tg(el)
                                                {
                                                    $('.addon-wraps').css("display","none");
                                                    $('.addon-wraps input , .addon-wraps select').attr("disabled",true);
                                                    var val = $(el).val();

                                                    if(val !== '')
                                                    {
                                                        $("input[name=addon_name]").css("display","none");
                                                        $("input[name=option_name]").css("display","none");

                                                        $('#ad-'+val+'-wrap').css("display","block");
                                                        $('#ad-'+val+'-wrap input, #ad-'+val+'-wrap select').removeAttr("disabled");
                                                        $('#ad-'+val+'-wrap input:checked').each(function(){
                                                            ad_selected_tg(this);
                                                        });
                                                        $('#ad-'+val+'-wrap select option:selected').each(function(){
                                                            ad_selected_tg(this);
                                                        });

                                                        $("#addMyAddon input[name=amount]").attr('readonly',true);
                                                    }
                                                    else
                                                    {
                                                        $("#addMyAddon input[name=amount]").removeAttr('readonly');
                                                        $("input[name=addon_name]").css("display","inline-block");
                                                        $("input[name=option_name]").css("display","inline-block");
                                                    }
                                                }

                                                function ad_selected_tg(el){
                                                    el = $(el);
                                                    var amount      = el.data("amount");
                                                    var cid         = el.data("cid");
                                                    var period_d    = el.data("period-duration");
                                                    var period_u    = el.data("period-unit");
                                                    var type        = el.data("type");

                                                    $('#addMyAddonForm input[name=period_time]').val(period_d);
                                                    $('#addMyAddonForm select[name=period]').val(period_u);
                                                    $('#addMyAddonForm input[name=amount]').val(amount);
                                                    $('#addMyAddonForm select[name=cid]').val(cid);

                                                    console.log("period_d        :"+period_d);
                                                    console.log("period_u        :"+period_u);
                                                    console.log("amount          :"+amount);
                                                    console.log("cid             :"+cid);

                                                    if(type === 'quantity')
                                                    {
                                                        $('input[type=range]',el.parent().parent()).trigger('change');
                                                        $('input[type=range]',el.parent().parent()).on('change input',function(){
                                                            var count       = $(this).val();
                                                            var nAmount     = (parseFloat(amount) * count).toFixed(2);
                                                            $('#addMyAddonForm input[name=amount]').val(nAmount);
                                                        });
                                                    }
                                                }
                                            </script>
                                            <select name="addon_id" onchange="change_addon_tg(this);">
                                                <option value=""><?php echo ___("needs/none"); ?></option>
                                                <?php
                                                    if(isset($product_addons) && $product_addons)
                                                    {
                                                        foreach($product_addons AS $ag)
                                                        {
                                                            ?>
                                                            <optgroup label="<?php echo $ag["title"]; ?>">
                                                                <?php
                                                                    foreach($ag["addons"] AS $ad_id => $ad)
                                                                    {
                                                                        ?><option value="<?php echo $ad_id; ?>"><?php echo $ad["name"]; ?></option><?php
                                                                    }
                                                                ?>
                                                            </optgroup>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <input type="text" name="addon_name" value="" placeholder="<?php echo __("admin/orders/my-addons-addon-name"); ?>">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-option-name"); ?></div>
                                        <div class="yuzde70">
                                            <?php
                                                if(isset($product_addons) && $product_addons)
                                                {
                                                    foreach($product_addons AS $ag)
                                                    {
                                                        foreach($ag["addons"] AS $ad_id => $ad)
                                                        {
                                                            ?>
                                                            <div class="addon-wraps" id="ad-<?php echo $ad_id; ?>-wrap" style="display: none;">
                                                                <?php
                                                                    $ad_options     = Utility::jdecode($ad["options"],true);
                                                                    $properties     = Utility::jdecode($ad["properties"],true);
                                                                    $compulsory     = true;
                                                                ?>
                                                                <?php if($ad["description"]): ?>
                                                                    <span style="font-size: 14px;"><?php echo $ad["description"]; ?></span>
                                                                    <div class="clear"></div>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if($ad["type"] == "radio"){
                                                                        ?>
                                                                        <?php if(!$compulsory): ?>
                                                                    <input checked id="addon-<?php echo $ad["id"]."-none"; ?>" class="radio-custom" name="option_id" value="" type="radio">
                                                                        <label style="margin-right:30px;" for="addon-<?php echo $ad["id"]."-none"; ?>" class="radio-custom-label"><?php echo ___("needs/idont-want"); ?></label>
                                                                    <br>
                                                                    <?php endif; ?>
                                                                        <?php
                                                                    foreach ($ad_options AS $k=>$opt){
                                                                        $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"]);
                                                                        if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                                        $periodic   = View::period($opt["period_time"],$opt["period"]);
                                                                        $name       = $opt["name"];
                                                                        $show_name  = $name." <strong>".$amount."</strong>";
                                                                        if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                                            $show_name .= " | <strong>".$periodic."</strong>";
                                                                        ?>
                                                                    <input<?php echo $compulsory && $k==0 ? ' checked' : ''; ?> id="addon-<?php echo $ad["id"]."-".$k; ?>" class="radio-custom" name="option_id" value="<?php echo $opt["id"]; ?>" type="radio" onchange="if($(this).prop('checked')) ad_selected_tg(this);" data-amount="<?php echo round($opt["amount"],2); ?>" data-cid="<?php echo $opt["cid"]; ?>" data-period-duration="<?php echo $opt["period_time"]; ?>" data-period-unit="<?php echo $opt["period"]; ?>"  data-type="<?php echo $ad["type"]; ?>">
                                                                        <label style="margin-right:30px;" for="addon-<?php echo $ad["id"]."-".$k; ?>" class="radio-custom-label"><?php echo $show_name; ?></label>
                                                                    <br>
                                                                    <?php
                                                                        }
                                                                        }
                                                                        elseif($ad["type"] == "checkbox"){
                                                                    ?>
                                                                    <?php if(!$compulsory): ?>
                                                                    <input checked id="addon-<?php echo $ad["id"]."-none"; ?>" class="checkbox-custom" name="option_id" value="" type="radio">
                                                                        <label style="margin-right:30px;" for="addon-<?php echo $ad["id"]."-none"; ?>" class="checkbox-custom-label"><?php echo ___("needs/idont-want"); ?></label>
                                                                    <br>
                                                                    <?php endif; ?>
                                                                        <?php
                                                                    foreach ($ad_options AS $k=>$opt){
                                                                        $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"],!$ad["override_usrcurrency"]);
                                                                        if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                                        $periodic = View::period($opt["period_time"],$opt["period"]);
                                                                        $name       = $opt["name"];
                                                                        $show_name  = $name." <strong>".$amount."</strong>";
                                                                        if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                                            $show_name .= " | <strong>".$periodic."</strong>";
                                                                        ?>
                                                                    <input<?php echo $compulsory && $k==0 ? ' checked' : ''; ?> id="addon-<?php echo $ad["id"]."-".$k; ?>" class="checkbox-custom" name="option_id" value="<?php echo $opt["id"]; ?>" type="radio" onchange="if($(this).prop('checked')) ad_selected_tg(this);" data-amount="<?php echo round($opt["amount"],2); ?>" data-cid="<?php echo $opt["cid"]; ?>" data-period-duration="<?php echo $opt["period_time"]; ?>" data-period-unit="<?php echo $opt["period"]; ?>"  data-type="<?php echo $ad["type"]; ?>">
                                                                        <label style="margin-right:30px;" for="addon-<?php echo $ad["id"]."-".$k; ?>" class="checkbox-custom-label"><?php echo $show_name; ?></label>
                                                                    <br>
                                                                    <?php
                                                                        }
                                                                        }
                                                                        elseif($ad["type"] == "select"){
                                                                    ?>
                                                                        <select name="option_id" onchange="ad_selected_tg($('option:selected',this));">
                                                                            <?php if(!$compulsory): ?>
                                                                                <option value=""><?php echo ___("needs/idont-want"); ?></option>
                                                                            <?php endif; ?>
                                                                            <?php
                                                                                foreach ($ad_options AS $k=>$opt){
                                                                                    $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"]);
                                                                                    if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                                                    $periodic = View::period($opt["period_time"],$opt["period"]);
                                                                                    $name       = $opt["name"];
                                                                                    $show_name  = $name." <strong>".$amount."</strong>";
                                                                                    if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                                                        $show_name .= " | <strong>".$periodic."</strong>";
                                                                                    ?>
                                                                                    <option value="<?php echo $opt["id"]; ?>" data-amount="<?php echo round($opt["amount"],2); ?>" data-cid="<?php echo $opt["cid"]; ?>" data-period-duration="<?php echo $opt["period_time"]; ?>" data-period-unit="<?php echo $opt["period"]; ?>"  data-type="<?php echo $ad["type"]; ?>"><?php echo $show_name; ?></option>

                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                    <?php
                                                                        }
                                                                        elseif($ad["type"] == "quantity"){
                                                                        $min = isset($properties["min"]) ? $properties["min"] : '0';
                                                                        $max = isset($properties["max"]) ? $properties["max"] : '0';
                                                                        $stp = isset($properties["step"]) ? $properties["step"] : '1';
                                                                        if($min == 0) $min = 1;
                                                                    ?>
                                                                        <select name="option_id" id="addon-<?php echo $ad["id"]; ?>-selection" style="margin-bottom: 5px;">
                                                                            <?php if(!$compulsory): ?>
                                                                                <option value=""><?php echo ___("needs/idont-want"); ?></option>
                                                                            <?php endif; ?>
                                                                            <?php
                                                                                foreach ($ad_options AS $k=>$opt){
                                                                                    $amount     = Money::formatter_symbol($opt["amount"],$opt["cid"]);
                                                                                    if(!$opt["amount"]) $amount = ___("needs/free-amount");
                                                                                    $periodic = View::period($opt["period_time"],$opt["period"]);
                                                                                    $name       = $opt["name"];
                                                                                    $show_name  = $name." <strong>".$amount."</strong>";
                                                                                    if(($opt["amount"] && $opt["period"] == "none") || $opt["amount"])
                                                                                        $show_name .= " | <strong>".$periodic."</strong>";
                                                                                    ?>
                                                                                    <option value="<?php echo $opt["id"]; ?>" onchange="if($(this).prop('selected')) ad_selected_tg(this);" data-amount="<?php echo round($opt["amount"],2); ?>" data-cid="<?php echo $opt["cid"]; ?>" data-period-duration="<?php echo $opt["period_time"]; ?>" data-period-unit="<?php echo $opt["period"]; ?>"  data-type="<?php echo $ad["type"]; ?>"><?php echo $show_name; ?></option>

                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                        <script type="text/javascript">
                                                                            $(document).ready(function(){
                                                                                $("#addon-<?php echo $ad["id"]; ?>-selection").change(function() {
                                                                                    if( $(this).val() === '') {
                                                                                        $('#addon-<?php echo $ad["id"]; ?>-slider-content').slideUp(250);
                                                                                    }else{
                                                                                        $('#addon-<?php echo $ad["id"]; ?>-slider-content').slideDown(250);
                                                                                    }
                                                                                });
                                                                            });
                                                                        </script>
                                                                        <div id="addon-<?php echo $ad["id"]; ?>-slider-content" style="<?php echo $compulsory ? '' : 'display: none;'; ?>">
                                                                            <input id="addon-<?php echo $ad["id"]; ?>-slider-value" type="range" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $stp; ?>" value="<?php echo $min; ?>" oninput="$(this).next().val($(this).val());" style="width: 80%;">
                                                                            <input min="<?php echo $min; ?>" type="number" name="option_quantity" onchange="$(this).prev().val($(this).val()).trigger('change');" style="float:right; width:50px;font-weight: bold; border: solid 1px #ccc;text-align: center;padding: 7px 0px;" value="<?php echo $min; ?>">

                                                                        </div>
                                                                        <?php

                                                                    }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            ?>
                                            <input type="text" name="option_name" value="" placeholder="<?php echo __("admin/users/document-filter-f-option-name"); ?>">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-cdate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="cdate" type="date" value="<?php echo DateManager::Now("Y-m-d"); ?>" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="ctime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-renewaldate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="renewaldate" type="date" value="<?php echo DateManager::Now("Y-m-d"); ?>" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="renewaltime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-duedate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="duedate" type="date" value="" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="duetime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-status"); ?></div>
                                        <div class="yuzde70">
                                            <select name="status">
                                                <?php
                                                    foreach($situations AS $k=>$v){
                                                        $v = Filter::html_clear($v);
                                                        ?>
                                                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/detail-pricing"); ?></div>
                                        <div class="yuzde70">

                                            <input style="width:80px;" name="period_time" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-period"); ?>" value=""> -
                                            <select style="width:130px;" name="period">
                                                <?php
                                                    foreach(___("date/periods") AS $k=>$v){
                                                        ?>
                                                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select><div class="clear"></div>
                                            <input style="width:80px;" name="amount" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-amount"); ?>" value=""> -
                                            <select style="width:130px;" name="cid">
                                                <?php
                                                    foreach(Money::getCurrencies() AS $curr){
                                                        ?>
                                                        <option value="<?php echo $curr["id"]; ?>"><?php echo $curr["name"]." (".$curr["code"].")"; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-invoice-generation"); ?></div>
                                        <div class="yuzde70">

                                            <div class="formcon">

                                                <input onclick="$('#addon_pmethods').css('display','block');" type="radio" class="checkbox-custom" id="invoice-generation-paid" name="invoice-generation" value="paid">
                                                <label style="margin-right:10px; " class="checkbox-custom-label" for="invoice-generation-paid"><?php echo __("admin/orders/updown-upgrade-invoice-generation-paid"); ?></label>

                                                <input onclick="$('#addon_pmethods').css('display','none');" type="radio" class="checkbox-custom" id="invoice-generation-unpaid" name="invoice-generation" value="unpaid">
                                                <label style="margin-right:10px; " class="checkbox-custom-label" for="invoice-generation-unpaid"><?php echo __("admin/orders/updown-upgrade-invoice-generation-unpaid"); ?></label>

                                            </div>

                                            <div class="formcon" id="addon_pmethods" style="display: none;">
                                                <span><?php echo __("admin/orders/detail-pmethod"); ?></span>
                                                <select name="pmethod" style="width: 160px;">
                                                    <option value="none"><?php echo ___("needs/none"); ?></option>
                                                    <?php
                                                        if($pmethods){
                                                            foreach($pmethods AS $k=>$v){
                                                                ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="formcon" id="addon-notification_wrap">
                                        <div class="yuzde30"><?php echo __("admin/orders/create-notification"); ?></div>
                                        <div class="yuzde70">
                                            <input type="checkbox" name="notification" value="1" class="checkbox-custom" id="addon-notification">
                                            <label for="addon-notification" class="checkbox-custom-label"></label>
                                        </div>

                                    </div>




                                    <?php if($privOperation): ?>
                                        <div style="float:right;margin-bottom:20px;" class="guncellebtn yuzde30">
                                            <a id="addMyAddonForm_submit" class="yesilbtn gonderbtn" href="javascript:void(0);"><?php echo __("admin/orders/my-addons-add-new-addon"); ?></a>
                                        </div>
                                    <?php endif; ?>

                                </form>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#addMyAddonForm_submit").on("click",function(){
                                            MioAjaxElement($(this),{
                                                waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                                progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                                                result:"addMyAddonForm_handler",
                                            });
                                        });
                                    });

                                    function addMyAddonForm_handler(result){
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.for != undefined && solve.for != ''){
                                                        $("#addMyAddonForm "+solve.for).focus();
                                                        $("#addMyAddonForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                        $("#addMyAddonForm "+solve.for).change(function(){
                                                            $(this).removeAttr("style");
                                                        });
                                                    }
                                                    if(solve.message != undefined && solve.message != '')
                                                        alert_error(solve.message,{timer:5000});
                                                }else if(solve.status == "successful"){
                                                    addons.ajax.reload();
                                                    alert_success(solve.message,{timer:2000});
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    }
                                </script>

                            </div>
                        </div>

                        <div id="editMyAddon" style="display: none;">
                            <div class="padding20">
                                <form id="editMyAddonForm" action="<?php echo $links["controller"]; ?>" method="post">
                                    <input type="hidden" name="operation" value="edit_addon">
                                    <input type="hidden" name="addon_id" value="0">

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-addon-name"); ?></div>
                                        <div class="yuzde70">
                                            <input type="text" name="addon_name" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-option-name"); ?></div>
                                        <div class="yuzde70">
                                            <input name="option_quantity" type="number" class="yuzde20" placeholder="<?php echo __("admin/products/add-addon-type-quantity"); ?>" value="1" min="1">
                                            <span class="yuzde5" style="font-weight: 600; text-align: center; margin-top:13px;">x</span>
                                            <input class="yuzde75" type="text" name="option_name" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-cdate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="cdate" type="date" value="<?php echo DateManager::Now("Y-m-d"); ?>" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="ctime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-renewaldate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="renewaldate" type="date" value="<?php echo DateManager::Now("Y-m-d"); ?>" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="renewaltime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-duedate"); ?></div>
                                        <div class="yuzde70">
                                            <input style="width: 150px;" name="duedate" type="date" value="" placeholder="00/00/0000">
                                            <input style="width:100px;" onkeypress='return event.charCode==58 || event.charCode>= 48 &&event.charCode<= 57' maxlength="5" type="time" name="duetime" value="">
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/my-addons-status"); ?></div>
                                        <div class="yuzde70">
                                            <select name="status">
                                                <?php
                                                    foreach($situations AS $k=>$v){
                                                        $v = Filter::html_clear($v);
                                                        ?>
                                                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/detail-pmethod"); ?></div>
                                        <div class="yuzde70">
                                            <select name="pmethod">
                                                <option value="none"><?php echo ___("needs/none"); ?></option>
                                                <?php
                                                    if($pmethods){
                                                        foreach($pmethods AS $k=>$v){
                                                            ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="subscription_wrap"></div>


                                    <div class="formcon">
                                        <div class="yuzde30"><?php echo __("admin/orders/detail-pricing"); ?></div>
                                        <div class="yuzde70">

                                            <input style="width:80px;" name="period_time" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-period"); ?>" value=""> -
                                            <select style="width:130px;" name="period">
                                                <?php
                                                    foreach(___("date/periods") AS $k=>$v){
                                                        ?>
                                                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select><div class="clear"></div>
                                            <input style="width:80px;" name="amount" type="text" placeholder="<?php echo __("admin/orders/detail-pricing-amount"); ?>" value=""> -
                                            <select style="width:130px;" name="cid">
                                                <?php
                                                    foreach(Money::getCurrencies() AS $curr){
                                                        ?>
                                                        <option value="<?php echo $curr["id"]; ?>"><?php echo $curr["name"]." (".$curr["code"].")"; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                    <div class="formcon" id="addon-edit-notification_wrap">
                                        <div class="yuzde30"><?php echo __("admin/orders/create-notification"); ?></div>
                                        <div class="yuzde70">
                                            <input type="checkbox" name="notification" value="1" class="checkbox-custom" id="addon-edit--notification">
                                            <label for="addon-edit--notification" class="checkbox-custom-label"></label>
                                        </div>

                                    </div>


                                    <?php if($privOperation): ?>
                                        <div style="float:right;margin-bottom:20px;" class="guncellebtn yuzde30">
                                            <a id="editMyAddonForm_submit" class="yesilbtn gonderbtn" href="javascript:void(0);"><?php echo __("admin/orders/save-button"); ?></a>
                                        </div>
                                    <?php endif; ?>

                                </form>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#editMyAddonForm_submit").on("click",function(){
                                            MioAjaxElement($(this),{
                                                waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                                progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                                                result:"editMyAddonForm_handler",
                                            });
                                        });
                                    });

                                    function editMyAddonForm_handler(result){
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.for != undefined && solve.for != ''){
                                                        $("#editMyAddonForm "+solve.for).focus();
                                                        $("#editMyAddonForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                        $("#editMyAddonForm "+solve.for).change(function(){
                                                            $(this).removeAttr("style");
                                                        });
                                                    }
                                                    if(solve.message != undefined && solve.message != '')
                                                        alert_error(solve.message,{timer:5000});
                                                }else if(solve.status == "successful"){
                                                    addons.ajax.reload();
                                                    alert_success(solve.message,{timer:2000});
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    }

                                    function cancel_subscription_addon(el,sub_id,ad_id)
                                    {
                                        if(!confirm("<?php echo ___("needs/apply-are-you-sure"); ?>")) return false;
                                        var request = MioAjax({
                                            button_element:el,
                                            waiting_text: "<?php echo __("website/others/button1-pending"); ?>",
                                            action: "<?php echo $links["controller"]; ?>",
                                            method: "POST",
                                            data:{
                                                operation: "cancel_subscription",
                                                id: sub_id,
                                                addon_id: ad_id
                                            }
                                        },true,true);
                                        request.done(function(result){
                                            var solve = getJson(result);
                                            if(solve !== undefined && solve !== false)
                                            {
                                                if(solve.status === "error")
                                                    alert_error(solve.message,{timer:4000});
                                                else if(solve.status === "successful")
                                                {
                                                    close_modal("editMyAddon");
                                                    window.location.href = location.href;
                                                }
                                            }
                                        });
                                    }
                                </script>

                            </div>
                        </div>

                        <div id="EventModal" style="display: none;" data-izimodal-title="<?php echo __("admin/orders/modal-events"); ?>">
                            <script type="text/javascript">
                                function EventOK(id){
                                    var button = $("#event_"+id+" .event-ok-button");
                                    var request = MioAjax({
                                        waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                        button_element:button,
                                        action:"<?php echo $links["controller"]; ?>",
                                        method:"POST",
                                        data:{operation:"event_ok",id:id}
                                    },true,true);
                                    request.done(function(result){
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.message != undefined && solve.message != '')
                                                        alert_error(solve.message,{timer:5000});
                                                }else if(solve.status == "successful"){
                                                    $("#event_"+id).remove();

                                                    if($("#EventModal_content .order-event-item").length==0){
                                                        close_modal("EventModal");
                                                        table.ajax.reload();
                                                    }
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    });
                                }
                            </script>
                            <div class="padding20">

                                <div id="EventModal_content"></div>

                            </div>
                        </div>

                        <script type="text/javascript">
                            $(document).ready(function(){

                                $("#myAddons-table").on("click",".status-msg",function(){
                                    var message = $('.status-msg-content',$(this).parent()).html();
                                    open_modal('statusMessage');
                                    $("#statusMessage .status-message-text").html(message);
                                });

                                $("#myAddons-table").on("click",".open-event",function(){
                                    var id = $(this).data("id");;
                                    open_modal('EventModal');
                                    var content = $("#addon-"+id+" .event-data").html();
                                    $("#EventModal_content").html(content);
                                });
                            });

                            function editMyAddon(id){


                                var addon_name  = $("#addon-"+id+" input[name=addon_name]").val();
                                var modal_title = "<?php echo __("admin/orders/my-addons-edit")?>";
                                modal_title = modal_title.replace("{name}",addon_name);

                                $("#editMyAddon").attr("data-iziModal-title",modal_title);
                                open_modal('editMyAddon');

                                var option_name  = $("#addon-"+id+" input[name=option_name]").val();
                                var option_q     = $("#addon-"+id+" input[name=option_quantity]").val();
                                var amount       = $("#addon-"+id+" input[name=amount]").val();
                                var cid          = $("#addon-"+id+" input[name=cid]").val();
                                var period       = $("#addon-"+id+" input[name=period]").val();
                                var period_time  = $("#addon-"+id+" input[name=period_time]").val();
                                var status       = $("#addon-"+id+" input[name=status]").val();
                                var cdate        = $("#addon-"+id+" input[name=cdate]").val();
                                var ctime        = $("#addon-"+id+" input[name=ctime]").val();
                                var renewaldate  = $("#addon-"+id+" input[name=renewaldate]").val();
                                var renewaltime  = $("#addon-"+id+" input[name=renewaltime]").val();
                                var duedate      = $("#addon-"+id+" input[name=duedate]").val();
                                var duetime      = $("#addon-"+id+" input[name=duetime]").val();
                                var pmethod      = $("#addon-"+id+" input[name=pmethod]").val();
                                var subscription = $("#addon-"+id+" .subscription-data").html();

                                $("#editMyAddon input[name=addon_name]").val(addon_name);
                                $("#editMyAddon input[name=option_quantity]").val(option_q);
                                $("#editMyAddon input[name=option_name]").val(option_name);
                                $("#editMyAddon input[name=amount]").val(amount);
                                $("#editMyAddon select[name=pmethod]").removeAttr("selected");
                                $("#editMyAddon select[name=cid]").removeAttr("selected");
                                $("#editMyAddon select[name=cid] option[value='"+cid+"']").attr("selected",true);
                                $("#editMyAddon select[name=pmethod] option[value='"+pmethod+"']").attr("selected",true);
                                $("#editMyAddon select[name=period]").removeAttr("selected");
                                $("#editMyAddon select[name=period] option[value='"+period+"']").attr("selected",true);
                                $("#editMyAddon input[name=period_time]").val(period_time);
                                $("#editMyAddon select[name=status]").removeAttr("selected");
                                $("#editMyAddon select[name=status] option[value='"+status+"']").attr("selected",true);
                                $("#editMyAddon input[name=cdate]").val(cdate);
                                $("#editMyAddon input[name=ctime]").val(ctime);
                                $("#editMyAddon input[name=renewaldate]").val(renewaldate);
                                $("#editMyAddon input[name=renewaltime]").val(renewaltime);
                                $("#editMyAddon input[name=duedate]").val(duedate);
                                $("#editMyAddon input[name=duetime]").val(duetime);
                                $("#editMyAddon input[name=addon_id]").val(id);
                                $("#editMyAddon #subscription_wrap").html(subscription);

                                if($("#editMyAddon #subscription_wrap .subscription-content").length > 0)
                                {
                                    $.get("<?php echo $links["controller"]; ?>?operation=addon_subscription_detail&addon_id="+id,function(data){
                                        $("#editMyAddon #subscription_wrap .subscription-content").html(data);
                                    });
                                }

                            }

                            function deleteAddon(id){
                                swal({
                                    title: '<?php echo __("admin/orders/my-addons-delete-alert-title"); ?>',
                                    text: '<?php echo __("admin/orders/my-addons-delete-alert-body"); ?>',
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: '<?php echo __("admin/orders/delete-ok"); ?>',
                                    cancelButtonText: '<?php echo __("admin/orders/delete-no"); ?>',
                                }).then(function(){
                                    var request = MioAjax({
                                        action:"<?php echo $links["controller"]; ?>",
                                        method:"POST",
                                        data:{operation:"delete_addon",id:id}
                                    },true,true);

                                    request.done(function(res){
                                        if(res != ''){
                                            var solve = getJson(res);
                                            if(solve && typeof solve == "object"){
                                                if(solve.status == "error"){
                                                    swal({
                                                        title: 'Hata!',
                                                        text: solve.message,
                                                        type: 'error',
                                                        showConfirmButton: false,
                                                        timer: 3000,
                                                    });
                                                }else if(solve.status == "successful"){
                                                    var timer = 1500;
                                                    setTimeout(function(){
                                                        addons.ajax.reload();
                                                    },timer);
                                                    swal({
                                                        title: '<?php echo __("admin/orders/my-addons-delete-alert-success-title"); ?>',
                                                        text: '<?php echo __("admin/orders/my-addons-delete-alert-success-body"); ?>',
                                                        type: 'success',
                                                        showConfirmButton: false,
                                                        timer: timer,
                                                    });
                                                }
                                            }else
                                                console.log(res);
                                        }
                                    });
                                });
                            }

                            function applyAddonOperation(type,id){
                                $("#AddonList").addClass("tab-blur-content");
                                $("#addon-operation-loading").fadeIn(500,function(){
                                });

                                var request = MioAjax({
                                    action: "<?php echo $links["controller"]; ?>",
                                    method: "POST",
                                    data: {operation:"apply_operation_addons",type:type,id:id}
                                },true,true);

                                request.done(function(result){

                                    $("#addon-operation-loading").fadeOut(500,function(){
                                        $("#AddonList").removeClass("tab-blur-content");
                                    });

                                    if(result){
                                        if(result != ''){
                                            var solve = getJson(result);
                                            if(solve !== false){
                                                if(solve.status == "error"){
                                                    if(solve.message != undefined && solve.message != '')
                                                        alert_error(solve.message,{timer:5000});
                                                    table.ajax.reload();
                                                }else if(solve.status == "successful"){

                                                    addons.ajax.reload();

                                                    alert_success(solve.message,{timer:3000});
                                                }
                                            }else
                                                console.log(result);
                                        }
                                    }else console.log(result);
                                });

                            }

                            function AddonEventOK(id){
                                var button = $("#event_"+id+" .event-ok-button");
                                var request = MioAjax({
                                    waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                    button_element:button,
                                    action:"<?php echo $links["controller"]; ?>",
                                    method:"POST",
                                    data:{operation:"event_ok",id:id}
                                },true,true);
                                request.done(function(result){
                                    if(result != ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error"){
                                                if(solve.message != undefined && solve.message != '')
                                                    alert_error(solve.message,{timer:5000});
                                            }else if(solve.status == "successful"){
                                                $("#event_"+id).remove();

                                                if($("#EventModal_content .order-event-item").length==0){
                                                    close_modal("EventModal");
                                                    addons.ajax.reload();
                                                }
                                            }
                                        }else
                                            console.log(result);
                                    }
                                });
                            }
                        </script>

                        <style type="text/css">
                            #myAddons-table tbody tr td:nth-child(1) {
                                text-align: left;
                            }

                            #myAddons-table tbody .tr-have-event {
                                background-color:#fc928738;
                            }
                        </style>

                        <?php if($privOperation): ?>
                            <div class="clear"></div>
                            <a style="margin-top:5px;" href="javascript:void(0);open_modal('addMyAddon');" class="lbtn">+ <?php echo __("admin/orders/my-addons-add-new-addon"); ?></a>
                        <?php endif; ?>

                        <div id="bigmobil">

                            <div class="middle">

                                <table width="100%" id="myAddons-table" class="table table-striped table-borderedx table-condensed nowrap">
                                    <thead style="background:#ebebeb;">
                                    <tr>
                                        <th align="left">#</th>
                                        <th data-orderable="false" align="left"><?php echo __("admin/orders/my-addons-addon-info"); ?></th>
                                        <th data-orderable="false" align="center"><?php echo __("admin/orders/my-addons-cdate"); ?> / <?php echo __("admin/orders/my-addons-duedate"); ?></th>
                                        <th data-orderable="false" align="center"><?php echo __("admin/orders/my-addons-amount"); ?></th>
                                        <th data-orderable="false" align="center"><?php echo __("admin/orders/my-addons-status"); ?></th>
                                        <th data-orderable="false" align="center"></th>
                                    </tr>
                                    </thead>
                                    <tbody align="center" style="border-top:none;">


                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="clear"></div>

                    </div>

                    <div class="clear"></div>
                </div><!-- addons tab content end -->


                <div id="content-requirements" class="tabcontent"><!-- requirements tab content start -->

                    <div class="adminpagecon">

                        <form action="<?php echo $links["controller"]; ?>" method="post" id="requirementsForm">
                            <input type="hidden" name="operation" value="update_requirements">

                            <div align="center" id="no-requirements" style="<?php echo $myrequirements ? 'display:none;' : '' ;?>">
                                <br><br>
                                <h5><strong><?php echo __("admin/orders/my-requirements-no-requirements"); ?></strong></h5>
                                <br><br>
                            </div>

                            <?php
                                if($myrequirements){
                                    foreach($myrequirements AS $requirement){
                                        $response = $requirement["response"];
                                        $rtype    = $requirement["response_type"];

                                        if($rtype == "select" || $rtype == "radio" || $rtype == "checkbox" || $rtype == "file")
                                            $response_j   = Utility::jdecode($response,true);

                                        if(($rtype == "select" || $rtype == "radio") && is_array($response_j))
                                            $response = htmlentities($response_j[0], ENT_QUOTES);
                                        else
                                            $response = htmlentities($response,ENT_QUOTES);

                                        ?>
                                        <div class="formcon">
                                            <div class="yuzde30"><?php echo $requirement["requirement_name"]; ?></div>
                                            <div class="yuzde70">
                                                <?php
                                                    if($rtype == "file"){
                                                        foreach($response_j AS $k=>$re){
                                                            $link = $links["controller"]."?operation=requirement-file-download&rid=".$requirement["id"]."&key=".$k;
                                                            ?><a href="<?php echo $link; ?>" target="_blank" class="lbtn"><i class="fa fa-external-link" aria-hidden="true"></i> <?php echo Utility::short_text($re["file_name"],0,30,true); ?></a> <?php
                                                        }
                                                    }elseif($rtype == "input" || $rtype == "select" || $rtype == "radio"){
                                                        ?>
                                                        <input type="text" name="values[<?php echo $requirement["id"]; ?>]" value="<?php echo $response; ?>">
                                                        <?php
                                                    }elseif($rtype == "textarea"){
                                                        ?>
                                                        <textarea name="values[<?php echo $requirement["id"]; ?>]"><?php echo html_entity_decode($response,ENT_QUOTES); ?></textarea>
                                                        <?php
                                                    }elseif($rtype == "checkbox"){
                                                        if($response_j && is_array($response_j)){
                                                            foreach($response_j AS $re){
                                                                ?>
                                                                <input type="text" name="values[<?php echo $requirement["id"]; ?>][]" value="<?php echo htmlentities($re,ENT_QUOTES); ?>">
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            ?>


                            <div class="clear"></div>

                            <div style="float:right;margin-bottom:20px;" class="guncellebtn yuzde30">
                                <a id="requirementsForm_submit" class="yesilbtn gonderbtn" href="javascript:void(0);"><?php echo __("admin/orders/update-button"); ?></a>
                            </div>

                        </form>
                        <script type="text/javascript">
                            $(document).ready(function(){

                                $("#requirementsForm_submit").on("click",function(){
                                    MioAjaxElement($(this),{
                                        waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                        progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                                        result:"requirementsForm_handler",
                                    });
                                });
                            });

                            function requirementsForm_handler(result){
                                if(result != ''){
                                    var solve = getJson(result);
                                    if(solve !== false){
                                        if(solve.status == "error"){
                                            if(solve.for != undefined && solve.for != ''){
                                                $("#requirementsForm "+solve.for).focus();
                                                $("#requirementsForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                $("#requirementsForm "+solve.for).change(function(){
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
                            }
                        </script>

                        <div class="clear"></div>
                    </div>

                    <div class="clear"></div>
                </div><!-- requirements tab content end -->

                <div id="content-history" class="tabcontent"><!-- history tab content start -->

                    <script type="text/javascript">
                        $(document).ready(function(){
                            $('#history-table').DataTable({
                                "columnDefs": [
                                    {
                                        "targets": [0],
                                        "visible":false,
                                        "searchable": false
                                    },
                                ],
                                "aaSorting" : [[0, 'asc']],
                                "lengthMenu": [
                                    [10, 25, 50, -1], [10, 25, 50, "<?php echo ___("needs/allOf"); ?>"]
                                ],
                                responsive: true,
                                "language":{"url":"<?php echo APP_URI; ?>/<?php echo ___("package/code"); ?>/datatable/lang.json"}
                            });
                        });
                    </script>
                    <table width="100%" id="history-table" class="table table-striped table-borderedx table-condensed nowrap">
                        <thead style="background:#ebebeb;">
                        <tr>
                            <th align="left">#</th>
                            <th data-orderable="false" align="left"><?php echo __("admin/orders/detail-history-th-by"); ?></th>
                            <th data-orderable="false" align="left"><?php echo __("admin/orders/detail-history-th-desc"); ?></th>
                            <th data-orderable="false" align="center"><?php echo __("admin/orders/detail-history-th-date"); ?></th>
                            <th data-orderable="false" align="center"><?php echo __("admin/users/detail-actions-th-ip"); ?></th>
                        </tr>
                        </thead>
                        <tbody align="center" style="border-top:none;">
                        <?php
                            $users = [];
                            $list = Events::getList('log','order',$order["id"],false,false,0,'id DESC');
                            if($list){
                                foreach($list AS $i => $row){
                                    $row['data'] = Utility::jdecode($row['data'],true);
                                    $user_detail    = ___("needs/system");
                                    $user           = [];
                                    if($row["user_id"] > 0)
                                    {
                                        if(isset($users[$row['user_id']]))
                                            $user = $users[$row['user_id']];
                                        else
                                        {
                                            $users[$row['user_id']] = User::getData($row['user_id'],'type,full_name','assoc');
                                            $user = $users[$row['user_id']];
                                        }
                                    }

                                    if($user){
                                        $user_detail = $user["full_name"];
                                        if($user["type"] == "admin")
                                            $user_detail = "<a href='".Controllers::$init->AdminCRLink("admins-dl",[$row["user_id"]])."' target='_blank' style='color:green;'>".$user_detail."</a>";
                                        elseif($user["type"] == "member")
                                            $user_detail = "<a href='".Controllers::$init->AdminCRLink("users-2",["detail",$row["user_id"]])."' target='_blank'>".$user_detail."</a>";
                                    }
                                    ?>
                                    <tr>
                                        <td align="left"><?php echo $i; ?></td>
                                        <td align="left">
                                            <?php
                                                echo $user_detail;
                                            ?>
                                        </td>
                                        <td align="left">
                                            <?php
                                                echo Events::order_log_description($row);
                                            ?>
                                        </td>
                                        <td align="center">
                                            <?php echo DateManager::format("d/m/Y H:i",$row["cdate"])?>
                                        </td>
                                        <td align="center">
                                            <?php echo isset($row['data']['ip']) ? $row['data']['ip'] : '-'; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                    <div class="clear"></div>

                </div><!-- history tab content end -->

                <?php if($privOperation && $order["period"] != "none" && $order["status"] == "active"): ?>
                    <div id="content-cancelled" class="tabcontent"><!-- cancelled tab content start -->

                        <div class="adminpagecon content-updown">


                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/orders/updown-statistics"); ?>
                                    <div class="clear"></div>
                                    <span class="kinfo"><?php echo __("admin/orders/updown-statistics-info"); ?></span>
                                </div>
                                <div class="yuzde70">

                                    <div class="formcon">
                                        <span><?php echo __("admin/orders/updown-statistics-name"); ?>:</span>
                                        <?php echo $product["title"]; ?>
                                    </div>

                                    <div class="formcon">
                                        <span><?php echo __("admin/orders/updown-statistics-renewal-date"); ?>:</span>
                                        <?php echo DateManager::format(Config::get("options/date-format"),$order["renewaldate"]); ?>
                                    </div>

                                    <div class="formcon">
                                        <span><?php echo __("admin/orders/updown-statistics-due-date"); ?>:</span>
                                        <?php echo DateManager::format(Config::get("options/date-format"),$order["duedate"]); ?>
                                    </div>

                                    <div class="formcon">
                                        <span><?php echo __("admin/orders/updown-statistics-times-used"); ?>:</span>
                                        <?php echo $updown_times_used ." ". ___("date/day"); ?> (<strong><?php echo $updown_times_used_amount; ?></strong>)
                                    </div>

                                    <div class="formcon">
                                        <span><?php echo __("admin/orders/updown-statistics-remaining-day"); ?>:</span>
                                        <?php echo $updown_remaining_day ." ". ___("date/day"); ?> (<strong style="color:red;"><?php echo $updown_remaining_amount; ?></strong>)
                                    </div>

                                </div>
                            </div>


                            <script type="text/javascript">
                                $(document).ready(function(){
                                    $("#cancelled-warning").on("click","#cancelledForm_submit",function(){
                                        MioAjaxElement($(this),{
                                            waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                                            progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                                            result:"cancelledForm_handler",
                                        });
                                    });
                                });

                                function cancelledForm_handler(result) {
                                    if(result != ''){
                                        var solve = getJson(result);
                                        if(solve !== false){
                                            if(solve.status == "error"){
                                                if(solve.for != undefined && solve.for != ''){
                                                    $("#cancelledForm "+solve.for).focus();
                                                    $("#cancelledForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                                                    $("#cancelledForm "+solve.for).change(function(){
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
                                }
                            </script>
                            <form action="<?php echo $links["controller"]; ?>" method="post" id="cancelledForm">
                                <input type="hidden" name="operation" value="cancelled">

                                <div class="formcon">
                                    <div class="yuzde30"><?php echo __("admin/orders/cancelled-operation"); ?>
                                    </div>
                                    <div class="yuzde70">

                                        <div class="formcon">
                                            <span><?php echo __("admin/orders/updown-downgrade-refund-as-none"); ?></span>
                                            <input checked type="radio" class="checkbox-custom" id="refund2_as_none" name="refund" value="none">
                                            <label style="margin-right:10px; " class="checkbox-custom-label" for="refund2_as_none"></label>
                                        </div>

                                        <div class="formcon">
                                            <span><?php echo __("admin/orders/updown-downgrade-refund-as-credit"); ?></span>
                                            <input type="radio" class="checkbox-custom" id="refund2_as_credit" name="refund" value="credit">
                                            <label style="margin-right:10px; " class="checkbox-custom-label" for="refund2_as_credit"></label>
                                        </div>

                                        <div class="formcon">
                                            <span><?php echo __("admin/orders/updown-downgrade-refund-as-money"); ?></span>
                                            <input type="radio" class="checkbox-custom" id="refund2_as_money" name="refund" value="money">
                                            <label style="margin-right:10px; " class="checkbox-custom-label" for="refund2_as_money"></label>
                                        </div>

                                    </div>
                                </div>

                                <div id="cancelled-warning" style="display: none;" data-izimodal-title="<?php echo __("admin/orders/cancelled-submit"); ?>">
                                    <div class="padding20">

                                        <div align="center">
                                            <p id="deleteModal_text1"><?php echo __("admin/orders/cancelled-warning-text"); ?></p>
                                            <?php if($order["module"] != "none"): ?>
                                                <p><?php echo __("admin/orders/note1"); ?></p>
                                            <?php endif; ?>
                                            <div class="clear"></div>
                                            <div class="yuzde50">
                                                <a href="javascript:void(0);" id="cancelledForm_submit" class="gonderbtn redbtn"><i class="fa fa-check"></i> <?php echo ___("needs/yes"); ?></a>
                                            </div>
                                            <div class="yuzde50">
                                                <a href="javascript:void(0);close_modal('cancelled-warning');" class="gonderbtn yesilbtn"><i class="fa fa-ban"></i> <?php echo ___("needs/no"); ?></a>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div style="float:right;margin-bottom:20px;" class="guncellebtn yuzde30">
                                    <a class="redbtn gonderbtn" href="javascript:open_modal('cancelled-warning');void 0;"><?php echo __("admin/orders/cancelled-submit"); ?></a>
                                </div>


                            </form>


                            <div class="clear"></div>
                        </div>

                        <div class="clear"></div>
                    </div><!-- cancelled tab content end -->
                <?php endif; ?>


            </div><!-- tab wrap content end -->


            <div class="clear"></div>
        </div>
    </div>


</div>

<?php include __DIR__.DS."inc".DS."footer.php"; ?>

</body>
</html>