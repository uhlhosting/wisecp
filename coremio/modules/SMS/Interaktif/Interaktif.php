<?php
    /**
    * @author Sitemio Bilişim Teknolojileri Tic. Ltd. Şti.
    * @project Sitemio Temel Kaynak Kod Çekirdek Yapısı
    * @date 2017-07-01 09:00 AM
    * @contract http://www.sitemio.com/hizmet-sozlesmesi.html
    * @copyright Tüm Hakları Sitemio Bilişim Teknolojileri Tic. Ltd. Şti. adına saklıdır
    * @warning Lisanssız kopyalanamaz, dağıtılamaz ve kullanılamaz.
    **/

    defined('CORE_FOLDER') OR exit('You can not get in here!');
    class Interaktif
    {
        public $international=false,$lang,$config;
        private $instance,$title,$body,$numbers=[],$numbers_intl=[],$panel=false;
        public  $error,$prevent_transmission_to_intl = false;
        public function __construct($external_config=[])
        {
            if(!class_exists("InteraktifLib")) include __DIR__.DS."class.php";
            $this->lang         = Modules::Lang("SMS",__CLASS__);
            $config             = Modules::Config("SMS",__CLASS__);
            $this->config       = $config;
            $external           = $external_config ? $external_config : [];
            $config             = array_merge($config,$external);
            $username           = $config["username"];
            $password           = $config["password"];
            $secret             = $config["secret"];
            $title              = $config["origin"];
            $this->instance     = new InteraktifLib($username,$password,$secret);
            $this->title = $title;
        }

        public function change_config($username,$password,$secret=''){
            $this->config["username"]   = $username;
            $this->config["password"]   = $password;
            $this->config["secret"]     = $secret;
            $this->instance = new InteraktifLib($username,$password,$secret);
        }

        public function title($arg=''){
            $this->title = $arg;
            return $this;
        }

        public function body($text='',$template=false,$variables=[],$lang='',$user=0){
            $this->numbers_reset();
            if($template) {
                $look = View::notifications("sms",$template,$text,$variables,$lang,$user);
                if($look!==false && isset($look["content"])){
                    if(isset($look["title"]))
                        $this->title($look["title"]);
                    $text = $look["content"];
                }
            }

            if(!class_exists("Money")) Helper::Load("Money");
            $currencies = Money::getCurrencies();

            foreach($currencies AS $row){
                if(($row["prefix"] && substr($row["prefix"],-1,1) == ' ') || ($row["suffix"] && substr($row["suffix"],0,1) == ' '))
                    $code = $row["code"];
                else
                    $code = $row["prefix"] ? $row["code"].' ' : ' '.$row["code"];

                $row["prefix"] = Utility::text_replace($row["prefix"],[' ' => '']);
                $row["suffix"] = Utility::text_replace($row["suffix"],[' ' => '']);
                if(!Validation::isEmpty($row["prefix"]) && $row["prefix"])
                    $text = Utility::text_replace($text,[$row["prefix"] => $code]);
                elseif(!Validation::isEmpty($row["suffix"]) && $row["suffix"])
                    $text = Utility::text_replace($text,[$row["suffix"] => $row["code"]]);
            }
            $text       = Filter::transliterate($text);

            $this->body = $text;
            return $this;
        }

        public function AddNumber($arg=0,$cc=NULL){
            if(!is_array($arg)){
                if($cc) $arg = [$cc."|".$arg];
                else $arg = [$arg];
            }
            foreach($arg AS $num){
                if(strstr($num,"|")){
                    $split  = explode("|",$num);
                    $cc     = $split[0] ? $split[0] : "90";
                    $no     = substr(Filter::numbers($split[1]),0,10);
                    $num    = $cc.$no;
                    if(!$this->prevent_transmission_to_intl && ($cc != "90" || $cc != "+90")) $this->numbers_intl[] = $num;
                    else $this->numbers[] = $num;
                }else{
                    $num    = Filter::numbers($num);
                    $strlen = strlen($num);
                    if($strlen == 11) $num = "9".$num;
                    elseif($strlen == 10) $num = "90".$num;
                    $this->numbers[] = $num;
                }
            }
            return $this;
        }

        public function getBalance(){
            $balance = $this->instance->Balance();

            if(!$balance && $this->instance->error){
                $this->error = $this->instance->error;
                return false;
            }
            return $balance;
        }

        public function getReportID(){
            return $this->instance->rid;
        }

        public function getReport($id=0){
            $id     = ($id == 0) ? $this->getReportID() : $id;
            $content = $this->instance->ReportLook($id);
            if($content != ''){
                $waiting_arr	    = [];
                $conducted_arr      = [];
                $erroneous_arr	    = [];
                $waiting_count	    = 0;
                $conducted_count	= 0;
                $erroneous_count	= 0;

                $rows               = explode("|",$content);

                foreach($rows AS $k=>$row){
                    if($k!=0){
                        $split      = explode(" ",$row);
                        $number     = $split[0];
                        $status     = (int) $split[1];
                        if($status == 0){
                            $waiting_arr[] = $number;
                            $waiting_count++;
                        }elseif($status == 5 || $status == 9){
                            $conducted_arr[] = $number;
                            $conducted_count++;
                        }else{
                            $erroneous_arr[] = $number;
                            $erroneous_count++;
                        }

                    }
                }


                return [
                    'waiting'       => ['data' => $waiting_arr, 'count' => $waiting_count],
                    'conducted'     => ['data' => $conducted_arr, 'count' => $conducted_count],
                    'erroneous'     => ['data' => $erroneous_arr, 'count' => $erroneous_count],
                ];
            }
            $this->error = $this->instance->error;
            return false;
        }

        public function origins(){
            $content = $this->instance->getOrigins();
            if($content != ''){

                if($content){
                    $result = [];
                    foreach($content AS $row){
                        $result[] = [
                            'name'      => $row,
                            'status'    => "active",
                        ];
                    }
                    return $result;
                }
                return [];
            }
            $this->error = $this->instance->error;
            return false;
        }

        public function getNumbers(){
            return array_merge($this->numbers,$this->numbers_intl);
        }

        public function getTitle(){
            return $this->title;
        }

        public function getBody(){
            return $this->body;
        }

        public function getError(){
            return $this->error;
        }

        public function numbers_reset(){
            $this->numbers = array();
            $this->numbers_intl = array();
            return true;
        }

        public function submit($isthis=false){
            if(Validation::isEmpty($this->body)){
                $this->error = "Message content can not be left blank!";
                return false;
            }

            if(!$this->numbers && !$this->numbers_intl){
                $this->error = "Phone numbers can not be left blank!";
                return false;
            }

            if(!$this->prevent_transmission_to_intl && $this->numbers_intl){
                if($module_intl = Config::get("modules/sms-intl")){
                    if($module_intl != "none"){
                        Modules::Load("SMS",$module_intl);
                        if(class_exists($module_intl)){
                            $sms = new $module_intl();
                            $sms->body($this->getBody())->AddNumber($this->numbers_intl);
                            $send = $sms->submit();
                            if(!$this->numbers){
                                $this->error = $sms->getError();
                                return ($isthis) ? $this : $send;
                            }
                        }
                    }
                }
            }

            if($this->numbers){
                $send = $this->instance->Submit($this->title,$this->body,$this->numbers);
                $this->error = $this->instance->error;
                return ($isthis) ? $this : $send;
            }

            return false;
        }
    }