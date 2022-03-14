<!DOCTYPE html>
<html>
<head>
    <?php
        Utility::sksort($lang_list,"local");
        $plugins    = ['select2','jquery-ui','jscolor'];
        include __DIR__.DS."inc".DS."head.php";

        $options    = $group["options"] ? Utility::jdecode($group["options"],true) : [];
        $optionsl   = $group["optionsl"] ? Utility::jdecode($group["optionsl"],true) : [];
        $list_template = isset($options["list_template"]) ? $options["list_template"] : 1;
        if(!$list_template) $list_template = 1;
        $columns    = isset($optionsl["columns"]) ? $optionsl["columns"] : [];
    ?>

    <script type="text/javascript">
        $(document).ready(function(){

            $(".input-external-link").change(function(){
                var value = $(this).val();
                if(value){
                    $("#settings-optional-addons .adminpagecon,#settings-requirements .adminpagecon").addClass("tab-blur-content");
                    $(".blur-text").fadeIn(100);
                }else{
                    $("#settings-informations .adminpagecon,#settings-optional-addons .adminpagecon,#settings-requirements .adminpagecon").removeClass("tab-blur-content");
                    $(".blur-text").fadeOut(100);
                }

            });

            var tab = _GET("settings");
            if (tab != '' && tab != undefined) {
                $("#tab-settings .tablinks[data-tab='" + tab + "']").click();
            } else {
                $("#tab-settings .tablinks:eq(0)").addClass("active");
                $("#tab-settings .tabcontent:eq(0)").css("display", "block");
            }

            var tab2 = _GET("lang");
            if (tab2 != '' && tab2 != undefined) {
                $("#tab-lang .tablinks[data-tab='" + tab2 + "']").click();
            } else {
                $("#tab-lang .tablinks:eq(0)").addClass("active");
                $("#tab-lang .tabcontent:eq(0)").css("display", "block");
            }

            var tab3 = _GET("lang2");
            if (tab3 != '' && tab3 != undefined) {
                $("#tab-lang2 .tablinks[data-tab='" + tab3 + "']").click();
            } else {
                $("#tab-lang2 .tablinks:eq(0)").addClass("active");
                $("#tab-lang2 .tabcontent:eq(0)").css("display", "block");
            }

            /*$("#addNewForm").bind("keypress", function(e) {
                if (e.keyCode == 13) $("#addNewForm_submit").click();
            });*/

            $("#addNewForm_submit").on("click",function(){
                MioAjaxElement($(this),{
                    waiting_text: '<?php echo ___("needs/button-waiting"); ?>',
                    progress_text: '<?php echo ___("needs/button-uploading"); ?>',
                    result:"addNewForm_handler",
                });
            });

        });

        function addNewForm_handler(result){
            if(result != ''){
                var solve = getJson(result);
                if(solve !== false){
                    if(solve.status == "error"){
                        if(solve.for != undefined && solve.for != ''){
                            $("#addNewForm "+solve.for).focus();
                            $("#addNewForm "+solve.for).attr("style","border-bottom:2px solid red; color:red;");
                            $("#addNewForm "+solve.for).change(function(){
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

</head>
<body>

<?php include __DIR__.DS."inc/header.php"; ?>

<div id="wrapper">

    <div class="icerik-container">
        <div class="icerik">

            <div class="icerikbaslik">
                <h1><strong><?php echo __("admin/products/page-add-special",[
                            '{group-name}' => $group["title"],
                        ]); ?></strong></h1>
                <?php include __DIR__.DS."inc".DS."breadcrumb.php"; ?>
            </div>

            <div class="clear"></div>

            <form action="<?php echo $links["controller"]; ?>" method="post" id="addNewForm" style="margin-top: 5px;">
                <input type="hidden" name="operation" value="add_new_special">

                <div id="tab-settings"><!-- tab wrap content start -->
                    <ul class="tab">
                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'detail','settings')" data-tab="detail"><i class="fa fa-info" aria-hidden="true"></i>  <?php echo __("admin/products/add-new-product-settings-detail"); ?></a></li>

                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'automation','settings')" data-tab="automation"><i class="fa fa-cogs" aria-hidden="true"></i> <?php echo __("admin/products/add-new-server-automation"); ?></a></li>


                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'optional-addons','settings')" data-tab="optional-addons"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo __("admin/products/add-new-product-settings-optional-addons"); ?></a></li>

                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'requirements','settings')" data-tab="requirements"><i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo __("admin/products/add-new-product-settings-requirements"); ?></a></li>

                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'upgradeable','settings')" data-tab="upgradeable"><i class="fa fa-angle-double-up" aria-hidden="true"></i> <?php echo __("admin/products/add-new-product-settings-upgradeable-products"); ?></a></li>

                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, 'pricing','settings')" data-tab="pricing"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php echo __("admin/products/add-new-product-settings-pricing"); ?></a></li>
                    </ul>

                    <div id="settings-detail" class="tabcontent"><!-- detail tab content start -->

                        <div id="tab-lang"><!-- tab wrap content start -->
                            <ul class="tab">
                                <?php
                                    foreach($lang_list AS $lang){
                                        ?>
                                        <li><a href="javascript:void(0)" class="tablinks" onclick="open_tab(this, '<?php echo $lang["key"]; ?>','lang')" data-tab="<?php echo $lang["key"]; ?>"> <?php echo strtoupper($lang["key"]); ?></a></li>
                                        <?php
                                    }
                                ?>
                            </ul>

                            <?php
                                foreach($lang_list AS $lang) {
                                    $lkey = $lang["key"];

                                    ?>
                                    <div id="lang-<?php echo $lkey; ?>" class="tabcontent">

                                        <div class="adminpagecon">
                                            <div class="formcon">
                                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-title"); ?></div>
                                                <div class="yuzde70">
                                                    <input name="title[<?php echo $lkey; ?>]" type="text">
                                                </div>
                                            </div>

                                            <?php if($lang["local"]): ?>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-category"); ?></div>
                                                    <div class="yuzde70">
                                                        <script type="text/javascript">
                                                            function categoryChange(elem){
                                                                var val  = $(elem).val();
                                                                var type = $("option:selected",elem).data("list-type");

                                                                if(type==1){
                                                                    $(".list-template-1 textarea").attr("disabled",false);
                                                                    $(".list-template-2").slideUp(100,function(){
                                                                        $(".list-template-1").slideDown(100);
                                                                    });
                                                                    $(".cat-features input").attr("disabled",true);
                                                                    $(".cat-features").slideUp(100);
                                                                }else if(type == 2){
                                                                    $(".list-template-1 textarea").attr("disabled",true);
                                                                    $(".list-template-1").slideUp(100,function(){
                                                                        $(".list-template-2").slideDown(100);
                                                                    });

                                                                    $(".cat-features input").attr("disabled",true);
                                                                    $(".cat-features").slideUp(100,function(){
                                                                        $(".category-"+val+"-feature input").attr("disabled",false);
                                                                        $(".category-"+val+"-feature").slideDown(100);
                                                                    });
                                                                }
                                                            }
                                                        </script>
                                                        <select name="category" size="10" onchange="categoryChange(this);">
                                                            <option value="<?php echo $group["id"]; ?>" data-list-type="<?php echo $list_template; ?>" selected><?php echo ___("needs/none"); ?></option>
                                                            <?php
                                                                $cats = [];
                                                                $cats[] = $group["id"];
                                                                if(isset($categories) && $categories){
                                                                    foreach($categories AS $category){
                                                                        $opt  = $category["options"] ? Utility::jdecode($category["options"],true) : [];
                                                                        $ltemplate = isset($opt["list_template"]) ? $opt["list_template"] : 0;
                                                                        if($ltemplate == 2)
                                                                            $cats[] = $category["id"];
                                                                        ?>
                                                                        <option data-list-type="<?php echo $ltemplate; ?>" value="<?php echo $category["id"]; ?>"><?php echo $category["title"]; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="formcon list-template-1" style="<?php echo $list_template == 1 ? '' : 'display:none;'; ?>">
                                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-features"); ?></div>
                                                <div class="yuzde70">
                                                    <textarea<?php echo $list_template !=1 ? ' disabled' : ''; ?> rows="6" name="features[<?php echo $lkey; ?>]" placeholder="<?php echo __("admin/products/add-new-product-features-desc"); ?>"></textarea>
                                                </div>
                                            </div>

                                            <div class="list-template-2" style="<?php echo $list_template== 2 ? '' : 'display:none'; ?>">

                                                <?php
                                                    if(isset($cats) && $cats){
                                                        $getcwl = $functions["get_category_lang"];
                                                        foreach($cats AS $cs){
                                                            $ca = $getcwl($cs,$lkey);
                                                            if($ca){
                                                                $optl  = isset($ca["options"]) ? Utility::jdecode($ca["options"],true) : [];
                                                                $columns = isset($optl["columns"]) ? $optl["columns"] : [];
                                                                foreach($columns AS $col){
                                                                    ?>
                                                                    <div class="formcon category-<?php echo $cs; ?>-feature cat-features" <?php echo $cs == $group["id"] ? '' : 'style="display: none;"'; ?>>
                                                                        <div class="yuzde30"><?php echo $col["name"]; ?></div>
                                                                        <div class="yuzde70">
                                                                            <input<?php echo $cs != $group["id"] ? ' disabled' : ''; ?> name="features[<?php echo $lkey; ?>][<?php echo $col["id"]; ?>]" type="text">
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </div>

                                            <?php if($lang["local"]): ?>
                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-status"); ?></div>
                                                    <div class="yuzde70">
                                                        <select name="status">
                                                            <option value="active"><?php echo __("admin/products/status-active"); ?></option>
                                                            <option value="inactive"><?php echo __("admin/products/status-inactive"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/renewal-selection-hide"); ?></div>
                                                    <div class="yuzde70">
                                                        <input type="checkbox" name="renewal_selection_hide" value="1" id="renewal-selection-hide" class="checkbox-custom">
                                                        <label class="checkbox-custom-label" for="renewal-selection-hide">
                                                            <span class="kinfo"><?php echo __("admin/products/renewal-selection-hide-desc"); ?></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="formcon" style="<?php echo !Config::get("options/ctoc-service-transfer/status") ? 'display:none;' : ''; ?>">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-ctoc-service-transfer"); ?></div>
                                                    <div class="yuzde70">
                                                        <input type="checkbox" name="ctoc-service-transfer" value="1" id="ctoc-service-transfer" class="checkbox-custom" onchange="if($(this).prop('checked')) $('#ctoc-service-transfer-wrap').css('display','block'); else $('#ctoc-service-transfer-wrap').css('display','none');">
                                                        <label class="checkbox-custom-label" for="ctoc-service-transfer"><span class="kinfo"><?php echo __("admin/products/add-new-product-ctoc-service-transfer-desc"); ?></span></label>

                                                        <div class="clear"></div>
                                                        <div id="ctoc-service-transfer-wrap" style="display:none;">
                                                            <div class="formcon">
                                                                <div class="yuzde30" style="width: 50px;"><?php echo __("admin/products/add-new-product-limit"); ?></div>
                                                                <div class="yuzde70">
                                                                    <input type="text" style="width: 10%;" name="ctoc-service-transfer-limit" value="">
                                                                    <span class="kinfo"><?php echo __("admin/products/add-new-product-limit-ctoc-service-transfer"); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30">
                                                        <?php echo __("admin/products/add-new-product-affiliate"); ?>
                                                    </div>
                                                    <div class="yuzde70">

                                                        <div class="formcon">
                                                            <strong style="width: 20%; display:inline-block;"><?php echo __("admin/products/add-new-product-affiliate-disable"); ?></strong>

                                                            <input type="checkbox" class="checkbox-custom" id="affiliate-disable" name="affiliate_disable" value="1">
                                                            <label class="checkbox-custom-label" for="affiliate-disable"><span class="kinfo"><?php echo __("admin/products/add-new-product-affiliate-disable-desc"); ?></span></label>
                                                        </div>

                                                        <div class="formcon">
                                                            <strong style="width: 20%; display:inline-block;"><?php echo __("admin/products/add-new-product-affiliate-commission-rate"); ?></strong>
                                                            <input type="text" name="affiliate_rate" value="" class="yuzde10" onkeypress='return event.charCode==44 || event.charCode==46 || event.charCode>= 48 &&event.charCode<= 57'>
                                                            <span class="kinfo"><?php echo __("admin/products/add-new-product-affiliate-commission-rate-desc"); ?></span>
                                                        </div>


                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-popular"); ?></div>
                                                    <div class="yuzde70">
                                                        <input type="checkbox" name="popular" value="1" class="checkbox-custom" id="popular">
                                                        <label class="checkbox-custom-label" for="popular"><?php echo __("admin/products/add-new-product-popular-label"); ?></label>
                                                    </div>
                                                </div>

                                            <?php endif; ?>

                                            <?php if($lang["local"]): ?>
                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-rank"); ?></div>
                                                    <div class="yuzde70">
                                                        <input type="text" name="rank" value="" class="yuzde10">
                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-stock"); ?></div>
                                                    <div class="yuzde70">
                                                        <input type="text" name="stock" value="" class="yuzde10">
                                                        <span class="kinfo"><?php echo __("admin/products/add-new-product-stock-desc"); ?></span>
                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-show-domain-options"); ?></div>
                                                    <div class="yuzde70">

                                                        <input id="show_domain" type="checkbox" name="show_domain" value="1" class="sitemio-checkbox">
                                                        <label for="show_domain" class="sitemio-checkbox-label"></label>
                                                        <span class="kinfo"><?php echo __("admin/products/add-new-product-show-domain-options-desc"); ?></span>

                                                    </div>
                                                </div>


                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-invisible"); ?></div>
                                                    <div class="yuzde70">

                                                        <input id="invisibility" type="checkbox" name="invisibility" value="1" class="sitemio-checkbox">
                                                        <label for="invisibility" class="sitemio-checkbox-label"></label>
                                                        <span class="kinfo"><?php echo __("admin/products/add-new-product-invisible-desc"); ?></span>

                                                    </div>
                                                </div>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-download-file"); ?></div>
                                                    <div class="yuzde70">

                                                        <input id="download-file" type="file" name="download-file" style="width: 200px;" onchange="if($(this).val() == '') $('#download-file-button').hide(1); else $('#download-file-button').show(1); ">
                                                        <a style="display: none;" id="download-file-button" href="javascript:$('#download-file').val('').trigger('change');void 0;" class="red sbtn"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        <div class="clear"></div>
                                                        <span class="kinfo"><?php echo __("admin/products/add-new-product-download-file-desc"); ?></span>

                                                    </div>
                                                </div>
                                            <?php endif; ?>


                                            <div class="formcon">
                                                <div class="yuzde30"><?php echo __("admin/orders/detail-delivery-title"); ?> <br><span style="font-weight:normal"><?php echo __("admin/orders/detail-delivery-title-ex"); ?></span></div>
                                                <div class="yuzde70">
                                                    <input name="delivery-title-name[<?php echo $lkey; ?>]" type="text" placeholder="<?php echo __("admin/orders/detail-delivery-title-name"); ?>" value="">
                                                    <textarea name="delivery-title-description[<?php echo $lkey; ?>]" placeholder="<?php echo __("admin/orders/detail-delivery-title-description"); ?>"></textarea>
                                                    <br><span class="kinfo"><?php echo __("admin/orders/detail-delivery-title-info"); ?></span>
                                                </div>
                                            </div>


                                            <?php if($lang["local"]): ?>

                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-order-image"); ?>
                                                        <div class="clear"></div>
                                                        <span class="kinfo"><?php echo __("admin/products/add-new-product-order-image-desc"); ?></span>
                                                    </div>
                                                    <div class="yuzde70">
                                                        <div class="headerbgedit">
                                                            <input type="file" name="order_image" id="order_image" style="display:none;" onchange="read_image_file(this,'order_image_preview');" data-default-image="<?php echo $getOrderImageDeft; ?>" />
                                                            <div class="headbgeditbtn">
                                                                <a href="javascript:$('#order_image').val('').trigger('change');void 0;" class="photosil"><i class="fa fa-trash"></i></a><br/>
                                                                <a class="avatarguncelle" href="javascript:void(0);" onclick="$('#order_image').click();" ><i class="fa fa-camera" aria-hidden="true"></i></a>
                                                            </div>
                                                            <img src="<?php echo $getOrderImageDeft; ?>" style="    height: 130px;    width: 170px;" width="100%" id="order_image_preview">
                                                        </div>

                                                    </div>
                                                </div>



                                                <div class="formcon">
                                                    <div class="yuzde30"><?php echo __("admin/products/add-new-product-notes"); ?></div>
                                                    <div class="yuzde70">
                                                        <textarea name="notes" rows="5" placeholder="<?php echo __("admin/products/add-new-product-notes-ex"); ?>"></textarea>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="formcon">
                                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-buy-external-link"); ?></div>
                                                <div class="yuzde70">
                                                    <input name="external_link[<?php echo $lkey; ?>]" class="" type="text" placeholder="http://www.example.com/buylink.html">
                                                    <br><span class="kinfo"><?php echo __("admin/products/add-new-product-buy-external-link-desc"); ?></span>
                                                </div>
                                            </div>

                                            <div class="formcon">
                                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-buy-button-name"); ?></div>
                                                <div class="yuzde70">
                                                    <input name="buy_button_name[<?php echo $lkey; ?>]" type="text" placeholder="">
                                                    <br><span class="kinfo"><?php echo __("admin/products/add-new-product-buy-button-name-desc"); ?></span>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="clear"></div>
                                    </div>
                                    <?php
                                }
                            ?>


                        </div><!-- tab wrap content end -->

                    </div><!-- detail tab content end -->

                    <div id="settings-automation" class="tabcontent">
                        <div class="adminpagecon">

                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-automation-module"); ?></div>
                                <div class="yuzde70">
                                    <script type="text/javascript">
                                        $(document).ready(function(){

                                            $("select[name=module]").change(function(){
                                                var module      = $(this).val();

                                                if(module === 'none'){
                                                    $("#module_data_content").fadeOut(1).html('');
                                                    $("#auto_install_wrap").css("display","none");
                                                }else{
                                                    $("#module_data_content").html($("#module_data_loader").html()).fadeIn(1);
                                                    var request = MioAjax({
                                                        action:"<?php echo $links["controller"]; ?>",
                                                        method:"POST",
                                                        data:{
                                                            operation:"get_module_product_detail",
                                                            module:module,
                                                        }
                                                    },true,true);
                                                    request.done(function(result){
                                                        $("#module_data_content").fadeIn(1).html(result);
                                                        $("#auto_install_wrap").css("display","block");
                                                    });
                                                }

                                            });
                                        });
                                    </script>
                                    <select name="module">
                                        <option value="none"><?php echo ___("needs/none"); ?></option>
                                        <?php
                                            if(isset($module_groups) && $module_groups){
                                                foreach($module_groups AS $g_k=>$_modules){
                                                    $g_name = __("admin/products/api-manage-group-".$g_k);
                                                    ?>
                                                    <optgroup label="<?php echo $g_name; ?>">
                                                        <?php
                                                            foreach($_modules AS $m_k=>$module){
                                                                ?>
                                                                <option value="<?php echo $m_k;?>"><?php echo $module["lang"]["name"]; ?></option>
                                                                <?php
                                                            }
                                                        ?>
                                                    </optgroup>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div id="module_data_loader" style="display:none;text-align:center;">
                                <div class="clear"></div>
                                <div class="load-wrapp">
                                    <p style="margin-bottom:20px;font-size:17px;"><strong><?php echo ___("needs/processing"); ?>...</strong><br><?php echo ___("needs/please-wait"); ?></p>
                                    <div class="load-7">
                                        <div class="square-holder">
                                            <div class="square"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="module_data_content" style="display: none;"></div>

                            <div class="clear"></div>

                            <div class="formcon" style="display: none;" id="auto_install_wrap">
                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-auto-install-label"); ?></div>
                                <div class="yuzde70">
                                    <input type="checkbox" name="auto_install" value="1" id="auto_install" class="sitemio-checkbox">
                                    <label class="sitemio-checkbox-label" for="auto_install"></label>
                                    <span class="kinfo"><?php echo __("admin/products/add-new-product-auto-install-desc"); ?></span>
                                </div>
                            </div>



                        </div>
                    </div><!-- automation end -->

                    <div id="settings-optional-addons" class="tabcontent"><!-- tab content start -->
                        <div class="blur-text" style="display:none;">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <div class="clear"></div>
                            <?php echo __("admin/products/add-new-product-blur-text"); ?>
                        </div>

                        <div class="adminpagecon">


                            <div class="green-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <?php echo __("admin/products/add-new-product-addons-desc"); ?>
                                </div>
                            </div><br><br>

                            <script type="text/javascript">
                                function allof_addons(id,element){
                                    var checked = $(element).prop('checked');

                                    if(checked){
                                        $('#category_'+id+' .addon-item')
                                            .prop('checked',true)
                                            .each(function(){
                                                $(this).trigger("change");
                                            });
                                        $('label[for=category_'+id+'_allof],#category_'+id+' label').css('opacity',1);

                                    }else{
                                        $('#category_'+id+' .addon-item')
                                            .prop('checked',false)
                                            .each(function(){
                                                $(this).trigger("change");
                                            });
                                        $('label[for=category_'+id+'_allof],#category_'+id+' label')
                                            .css('opacity',0.7);
                                    }
                                }
                                function auto_select_requirements(ids,element){
                                    var split,checked = $(element).prop("checked");
                                    ids += ",";
                                    split = ids.split(",");
                                    $(split).each(function(k,v){
                                        if(v != ''){
                                            //$("#requirement_"+v).prop("checked",checked).trigger("change");
                                        }
                                    });
                                }
                            </script>

                            <ul class="addons-categories">
                                <?php
                                    $get_addons = $functions["get_addons_with_category"];
                                    if(isset($addon_categories) && $addon_categories){
                                        foreach($addon_categories AS $category){
                                            $addons = $get_addons($category["id"]);
                                            if($addons){
                                                ?>
                                                <li style="border-bottom:solid 1px #eee;padding:10px;" id="category_<?php echo $category["id"]; ?>">
                                                    <input onchange="allof_addons(<?php echo $category["id"]; ?>,this)" type="checkbox" class="allOf-trigger checkbox-custom" id="category_<?php echo $category["id"]; ?>_allof">
                                                    <label for="category_<?php echo $category["id"]; ?>_allof" class="checkbox-custom-label" style="font-weight: bold; opacity:0.7;"><?php echo $category["name"]; ?></label>
                                                    <div class="clear"></div>
                                                    <div class="padding10">
                                                        <ul class="addons" id="category_<?php echo $category["id"]; ?>_addons">
                                                            <?php
                                                                foreach ($addons AS $addon){
                                                                    $desc = $addon["description"] ? '('.$addon["description"].')' : '';
                                                                    ?>
                                                                    <li>
                                                                        <input onchange="auto_select_requirements('<?php echo $addon["requirements"]; ?>',this); if($('#category_<?php echo $category["id"]; ?>_addons input:not(:checked)').length==0) $('#category_<?php echo $category["id"]; ?>_allof').prop('checked',true),$('label[for=category_<?php echo $category["id"]; ?>_allof]').css('opacity',1); else $('#category_<?php echo $category["id"]; ?>_allof').prop('checked',false),$('label[for=category_<?php echo $category["id"]; ?>_allof]').css('opacity',0.7); if($(this).prop('checked')) $('label[for=addon_<?php echo $addon["id"]; ?>]').css('opacity',1); else $('label[for=addon_<?php echo $addon["id"]; ?>]').css('opacity',0.7);" type="checkbox" class="addon-item checkbox-custom" id="addon_<?php echo $addon["id"]; ?>" name="addons[]" value="<?php echo $addon["id"]; ?>">
                                                                        <label for="addon_<?php echo $addon["id"]; ?>" class="checkbox-custom-label" style="font-weight: 600; opacity:0.7;"><?php echo $addon["name"]; ?> <span style="font-weight:normal;"><?php echo $desc; ?></span></label>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="clear"></div>
                                                </li>
                                                <?php

                                            }
                                        }
                                    }
                                ?>
                            </ul>
                            <div class="clear"></div>

                            <a href="<?php echo $links["add-new-addon"]."?mcategory=special_".$group["id"]; ?>" class="lbtn">+ <?php echo __("admin/products/add-new-addon"); ?></a>

                        </div>

                        <div class="clear"></div>
                    </div><!-- tab content end -->


                    <div id="settings-requirements" class="tabcontent"><!-- tab content start -->
                        <div class="blur-text" style="display:none;">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <div class="clear"></div>
                            <?php echo __("admin/products/add-new-product-blur-text"); ?>
                        </div>

                        <div class="adminpagecon">


                            <div class="green-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <?php echo __("admin/products/add-new-product-requirements-desc"); ?>
                                </div>
                            </div><br><br>



                            <ul class="requirements-categories">
                                <?php
                                    $get_requirements = $functions["get_requirements_with_category"];
                                    if(isset($requirement_categories) && $requirement_categories){
                                        foreach($requirement_categories AS $category){
                                            $requirements = $get_requirements($category["id"]);
                                            if($requirements){

                                                ?>
                                                <li style="border-bottom:solid 1px #eee;padding:10px;" id="category_<?php echo $category["id"]; ?>">
                                                    <input onchange="if($(this).prop('checked')) $('#category_<?php echo $category["id"]; ?> input').prop('checked',true),$('label[for=category_<?php echo $category["id"]; ?>_allof],#category_<?php echo $category["id"]; ?> label').css('opacity',1); else $('#category_<?php echo $category["id"]; ?> input').prop('checked',false),$('label[for=category_<?php echo $category["id"]; ?>_allof],#category_<?php echo $category["id"]; ?> label').css('opacity',0.7);" type="checkbox" class="allOf-trigger checkbox-custom" id="category_<?php echo $category["id"]; ?>_allof">
                                                    <label for="category_<?php echo $category["id"]; ?>_allof" class="checkbox-custom-label" style="font-weight: bold; opacity:0.7;"><?php echo $category["name"]; ?></label>
                                                    <div class="clear"></div>
                                                    <div class="padding10">
                                                        <ul class="requirements" id="category_<?php echo $category["id"]; ?>_requirements">
                                                            <?php
                                                                foreach ($requirements AS $requirement){
                                                                    $desc = $requirement["description"] ? '('.$requirement["description"].')' : '';
                                                                    ?>
                                                                    <li>
                                                                        <input onchange="if($('#category_<?php echo $category["id"]; ?>_requirements input:not(:checked)').length==0) $('#category_<?php echo $category["id"]; ?>_allof').prop('checked',true),$('label[for=category_<?php echo $category["id"]; ?>_allof]').css('opacity',1); else $('#category_<?php echo $category["id"]; ?>_allof').prop('checked',false),$('label[for=category_<?php echo $category["id"]; ?>_allof]').css('opacity',0.7); if($(this).prop('checked')) $('label[for=requirement_<?php echo $requirement["id"]; ?>]').css('opacity',1); else $('label[for=requirement_<?php echo $requirement["id"]; ?>]').css('opacity',0.7);" type="checkbox" class="checkbox-custom" id="requirement_<?php echo $requirement["id"]; ?>" name="requirements[]" value="<?php echo $requirement["id"]; ?>">
                                                                        <label for="requirement_<?php echo $requirement["id"]; ?>" class="checkbox-custom-label" style="font-weight: 600; opacity:0.7;"><?php echo $requirement["name"]; ?> <span style="font-weight:normal;"><?php echo $desc; ?></span></label>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="clear"></div>
                                                </li>
                                                <?php

                                            }
                                        }
                                    }
                                ?>
                            </ul>
                            <div class="clear"></div>

                            <a href="<?php echo $links["add-new-requirement"]."?mcategory=special_".$group["id"]; ?>" class="lbtn">+ <?php echo __("admin/products/add-new-requirement"); ?></a>

                        </div>

                        <div class="clear"></div>
                    </div><!-- tab content end -->

                    <div id="settings-upgradeable" class="tabcontent"><!-- tab content start -->
                        <div class="adminpagecon">

                            <div class="green-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <?php echo __("admin/products/add-new-product-settings-upgradeable-products-desc"); ?>
                                </div>
                            </div><br><br>

                            <select name="upgradeable-products[]" multiple style="height: 240px;">
                                <?php
                                    if(isset($upgradeable_products) && $upgradeable_products)
                                    {
                                        foreach($upgradeable_products AS $g_id => $g)
                                        {
                                            ?>
                                            <optgroup label="<?php echo $g['title']; ?>">
                                                <?php
                                                    foreach($g['products'] AS $p)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $p["id"]; ?>"><?php echo $p["title"]; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </optgroup>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>



                        </div>
                    </div><!-- tab content end -->

                    <div id="settings-pricing" class="tabcontent"><!-- tab content start -->
                        <div class="adminpagecon">

                            <div class="green-info">
                                <div class="padding15">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <?php echo __("admin/products/add-new-product-pricing-desc"); ?>
                                </div>
                            </div><br><br>
                            <div class="formcon">
                                <div class="yuzde30"><?php echo __("admin/products/add-new-product-pricing"); ?></div>
                                <div class="clear"></div>
                                <div id="bigmobil">

                                    <div class="middle" style="line-height:45px;">
                                        <script type="text/javascript">
                                            $(document).ready(function(){
                                                $("#pricing-list").sortable({
                                                    handle:'.period-bearer',
                                                }).disableSelection();

                                                $("#pricing-list").on("click",".period-delete",function(){
                                                    $(this).parent().remove();
                                                    $("#pricing-list").sortable("refresh");
                                                });

                                            });

                                            function addPeriod(){
                                                var template = $("#price-item-template").html();

                                                $("#pricing-list").append(template);
                                                $("#pricing-list").sortable("refresh");

                                            }
                                        </script>
                                        <ul id="pricing-list" style="padding:0;margin:0;">

                                            <li style="padding:0; margin:0;">
                                                <input style="width:140px;" name="prices[time][]" type="text" placeholder="<?php echo __("admin/products/add-new-product-period-time"); ?>"> -
                                                <select style="width:140px;" name="prices[period][]">
                                                    <?php
                                                        foreach(___("date/periods") AS $k=>$v){
                                                            ?>
                                                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select> -
                                                <input style="width:140px;" name="prices[amount][]" type="text" placeholder="<?php echo __("admin/products/add-new-product-period-amount"); ?>"> -
                                                <select style="width:140px;" name="prices[cid][]">
                                                    <?php
                                                        foreach(Money::getCurrencies() AS $currency){
                                                            ?>
                                                            <option value="<?php echo $currency["id"]; ?>"><?php echo $currency["name"]." (".$currency["code"].")"; ?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                </select> -
                                                <span style="margin-right:20px;"><?php echo __("admin/products/add-new-product-period-discount",[
                                                        '{input}' => '<input style="width:70px;"  name="prices[discount][]" type="text" placeholder="'.__("admin/products/add-new-product-period-discount-placeholder").'" onkeypress="return event.charCode>= 48 &&event.charCode<= 57">',
                                                    ]); ?></span>
                                                <a style="line-height:normal;    padding: 7px 3px;" href="javascript:void(0);" class="sbtn period-bearer"><i class="fa fa-arrows-alt"></i></a>
                                                <a style="line-height:normal;    padding: 7px 3px;" href="javascript:void(0);" class="red sbtn period-delete"><i class="fa fa-trash"></i></a>
                                                <div class="clear"></div>
                                            </li>

                                        </ul>


                                    </div>
                                    <span class="kinfo"><?php echo __("admin/products/add-new-product-pricing-note"); ?></span>
                                    <div class="clear"></div>
                                    <br>
                                    <input type="checkbox" name="override_usrcurrency" value="1" class="checkbox-custom" id="override_usrcurrency">
                                    <label class="checkbox-custom-label" for="override_usrcurrency"><?php echo __("admin/products/override-user-currency"); ?> <span class="kinfo"><?php echo __("admin/products/override-user-currency-desc"); ?></span></label>

                                    <br>
                                    <input type="checkbox" name="taxexempt" value="1" class="checkbox-custom" id="taxexempt">
                                    <label class="checkbox-custom-label" for="taxexempt"><?php echo __("admin/products/taxexempt"); ?> <span class="kinfo"><?php echo __("admin/products/taxexempt-desc"); ?></span></label>

                                    <div class="clear"></div>

                                    <a href="javascript:addPeriod();void 0;" style="margin-top:15px;" class="lbtn">+ <?php echo __("admin/products/add-new-product-add-new-period"); ?></a>
                                </div>
                            </div>

                            <div class="clear"></div>
                        </div>

                        <div class="clear"></div>
                    </div><!-- tab content end -->


                </div><!-- tab wrap content end -->


                <div style="float:right;margin-top:10px;" class="guncellebtn yuzde30">
                    <a class="yesilbtn gonderbtn" id="addNewForm_submit" href="javascript:void(0);"><?php echo __("admin/products/new-product-button"); ?></a>
                </div>
                <div class="clear"></div>
            </form>

            <ul id="price-item-template" style="display: none;">
                <li style="padding:0; margin:0;">
                    <input style="width:140px;" name="prices[time][]" type="text" placeholder="<?php echo __("admin/products/add-new-product-period-time"); ?>"> -
                    <select style="width:140px;" name="prices[period][]">
                        <?php
                            foreach(___("date/periods") AS $k=>$v){
                                ?>
                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                <?php
                            }
                        ?>
                    </select> -
                    <input style="width:140px;" name="prices[amount][]" type="text" placeholder="<?php echo __("admin/products/add-new-product-period-amount"); ?>"> -
                    <select style="width:140px;" name="prices[cid][]">
                        <?php
                            foreach(Money::getCurrencies() AS $currency){
                                ?>
                                <option value="<?php echo $currency["id"]; ?>"><?php echo $currency["name"]." (".$currency["code"].")"; ?></option>
                                <?php
                            }
                        ?>
                    </select> -
                    <span style="margin-right:20px;"> <?php echo __("admin/products/add-new-product-period-discount",[
                            '{input}' => '<input style="width:70px;"  name="prices[discount][]" type="text" placeholder="'.__("admin/products/add-new-product-period-discount-placeholder").'" onkeypress="return event.charCode>= 48 &&event.charCode<= 57">',
                        ]); ?></span>
                    <a style="line-height:normal;    padding: 7px 3px;" href="javascript:void(0);" class="sbtn period-bearer"><i class="fa fa-arrows-alt"></i></a>
                    <a style="line-height:normal;    padding: 7px 3px;" href="javascript:void(0);" class="red sbtn period-delete"><i class="fa fa-trash"></i></a>
                    <div class="clear"></div>
                </li>
            </ul>


            <div class="clear"></div>
        </div>
    </div>


</div>

<?php include __DIR__.DS."inc".DS."footer.php"; ?>

</body>
</html>