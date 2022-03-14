<?php
    if(!defined("CORE_FOLDER")) die();

    $lang           = $module->lang;
    $config         = $module->config;

    Helper::Load(["Money"]);

    $merchant_id        = Filter::init("POST/merchant_id","letters_numbers");
    $merchant_key       = Filter::init("POST/merchant_key","letters_numbers");
    $merchant_salt      = Filter::init("POST/merchant_salt","letters_numbers");
    $test_mode          = (int) Filter::init("POST/test_mode","numbers");
    $debug_on           = (int) Filter::init("POST/debug_on","numbers");
    $commission_rate     = Filter::init("POST/commission_rate","amount");
    $commission_rate     = str_replace(",",".",$commission_rate);

    $sets               = [];

    if($merchant_id != $config["settings"]["merchant_id"])
        $sets["settings"]["merchant_id"] = $merchant_id;

    if($merchant_key != $config["settings"]["merchant_key"])
        $sets["settings"]["merchant_key"] = $merchant_key;

    if($merchant_salt != $config["settings"]["merchant_salt"])
        $sets["settings"]["merchant_salt"] = $merchant_salt;
    
    if($test_mode != $config["settings"]["test_mode"])
        $sets["settings"]["test_mode"] = $test_mode;

    if($debug_on != $config["settings"]["debug_on"])
        $sets["settings"]["debug_on"] = $debug_on;

    if($commission_rate != $config["settings"]["commission_rate"])
        $sets["settings"]["commission_rate"] = $commission_rate;


    if($sets){
        $config_result  = array_replace_recursive($config,$sets);
        $array_export   = Utility::array_export($config_result,['pwith' => true]);

        $file           = dirname(__DIR__).DS."config.php";
        $write          = FileManager::file_write($file,$array_export);

        $adata          = UserManager::LoginData("admin");
        User::addAction($adata["id"],"alteration","changed-payment-module-settings",[
            'module' => $config["meta"]["name"],
            'name'   => $lang["name"],
        ]);
    }

    echo Utility::jencode([
        'status' => "successful",
        'message' => $lang["success1"],
    ]);