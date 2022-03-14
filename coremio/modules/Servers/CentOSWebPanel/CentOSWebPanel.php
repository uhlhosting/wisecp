<?php

    class CentOSWebPanel_Module extends ServerModule
    {
        private $storage=[];
        public $api,$area_link;

        function __construct($server,$options=[]){
            $this->force_setup  = true; // If "true", even if the module gets an error message, it will be ignored and the installation is complete.
            $this->_name        = __CLASS__;
            parent::__construct($server,$options);
        }

        protected function define_server_info($server=[])
        {
            if(!class_exists("CentOSWebPanel_Api")) include __DIR__.DS."api.php";
            $this->api = new CentOSWebPanel_Api($server["ip"],$server["port"],$server["username"],$server["password"],$server["access_hash"],$server["secure"]);
        }

        public function testConnect(){
            $this->api->set_timeout(10);
            $test       = $this->api->account_list();

            if(!$test){
                $this->error = $this->api->error;
                return false;
            }

            return true;
        }
        public function getPlans($reseller=false){
            $data = $this->api->packages_list($reseller);
            if(!$data){
                $this->error = $this->api->error;
                return false;
            }
            return $data["msj"];
        }
        public function createAccount($domain,$options=[]){
            $domain         = idn_to_ascii($domain,0,INTL_IDNA_VARIANT_UTS46);
            $username       = $this->UsernameGenerator($domain);
            $password       = Utility::generate_hash(12,false,"lud");
            if(isset($options["username"])) $username = $options["username"];
            if(isset($options["password"])) $password = $options["password"];

            $username       = str_replace("-","",$username);

            $creation_info  = isset($options["creation_info"]) ? $options["creation_info"] : [];

            $reseller       = isset($creation_info["reseller"]) ? $creation_info["reseller"] : NULL;
            $package        = isset($creation_info["plan"]) ? $creation_info["plan"] : NULL;
            $inode          = isset($creation_info["inode"]) ? $creation_info["inode"] : 0;
            $limit_nproc    = isset($creation_info["limit_nproc"]) ? $creation_info["limit_nproc"] : 0;
            $limit_nofile   = isset($creation_info["limit_nofile"]) ? $creation_info["limit_nofile"] : 0;
            $backup         = isset($creation_info["backup"]) ? $creation_info["backup"] : 0;

            $params             = [
                'domain'        => $domain,
                'username'      => $username,
                'user'          => $username,
                'pass'          => $password,
                'email'         => $this->user["email"],
                'package'       => $package,
                'inode'         => $inode,
                'nproc'         => $limit_nproc,
                'nofile'        => $limit_nofile,
                'server_ips'    => $this->server["ip"],
            ];
            if($reseller) $params['reseller'] = 1;

            $create     = $this->api->account_add($params);
            if(!$create){
                $this->error = $this->api->error;
                return false;
            }

            if($backup){
                $apply = $this->api->account_udp([
                    'user'      => $username,
                    'backup'    => $backup ? "on" : "off",
                ]);
                if(!$apply){
                    $this->error = $this->api->error;
                    return false;
                }
            }

            return [
                'username' => $username,
                'password' => $password,
                'ftp_info' => [
                    'ip'   => $this->server["ip"],
                    'host' => "ftp.".$domain,
                    'username' => $username,
                    'password' => $password,
                    'port' => 21,
                ],
            ];
        }

        public function modifyAccount($params=[]){
            $params["user"] = $this->config["user"];

            $apply  = $this->api->account_udp($params);

            if(!$apply){
                $this->error = $this->api->error;
                return false;
            }

            return true;
        }

        public function apply_options($old_options,$new_options=[]){
            $old_config     = isset($old_options["config"]) ? $old_options["config"] : [];
            $new_config     = isset($new_options["config"]) ? $new_options["config"] : [];

            $old_c_info     = isset($old_options["creation_info"]) ? $old_options["creation_info"] : [];
            $new_c_info     = isset($new_options["creation_info"]) ? $new_options["creation_info"] : [];

            $old_c_user     = isset($old_config["user"]) ? $old_config["user"] : '';
            $new_c_user     = isset($new_config["user"]) ? $new_config["user"] : '';


            $old_plan       = isset($old_c_info["plan"]) ? $old_c_info["plan"] : false;
            $new_plan       = isset($new_c_info["plan"]) ? $new_c_info["plan"] : false;

            if($old_c_user !== $new_c_user && $new_c_user){
                if(!$this->api->accountdetail_list($new_c_user)){
                    $this->error = $this->api->error;
                    return false;
                }
            }

            $old_password           = isset($old_config["password"]) ? $old_config["password"] : '';
            $password               = Filter::password($new_config["password"]);
            $c_password             = $password ? Crypt::encode($password,Config::get("crypt/user")) : '';
            $new_config["password"] = $c_password;

            if($new_c_user && $password && $c_password != $old_password){

                if(Utility::strlen($password) < 5){
                    $this->error = __("admin/orders/error8");
                    return false;
                }
                $strength = $this->getPasswordStrength($password);
                if(!$strength) return false;

                if($strength < 65){
                    $this->error = __("admin/orders/error9");
                    return false;
                }

                $changed    = $this->changePassword(false,$password);
                if(!$changed) return false;
            }

            $new_options["ftp_info"]["host"] = "ftp.".$new_options["domain"];
            $new_options["ftp_info"]["port"] = 21;
            $new_options["ftp_info"]["username"] = $new_config["user"];
            $new_options["ftp_info"]["password"] = $c_password;


            if($new_c_user){
                if($new_plan && !Validation::isEmpty($new_plan)){
                    if(!$old_plan || $new_plan != $old_plan){
                        $change  = $this->change_plan($new_plan);
                        if(!$change) return false;
                    }
                }

                $old_backup         = isset($old_c_info["backup"]) ? $old_c_info["backup"] : false;
                $new_backup         = isset($new_c_info["backup"]) ? $new_c_info["backup"] : false;

                $setFeatures        = [];

                if($new_c_info["inode"] != $old_c_info["inode"])
                    $setFeatures["inode"] = $new_c_info["inode"];

                if($new_c_info["limit_nproc"] != $old_c_info["limit_nproc"])
                    $setFeatures['processes'] = $new_c_info["limit_nproc"];

                if($new_c_info["limit_nofile"] != $old_c_info["limit_nofile"])
                    $setFeatures['openfiles'] = $new_c_info["limit_nofile"];

                if($new_backup != $old_backup)
                    $setFeatures['backup'] = $new_backup ? "on" : "off";

                if($setFeatures){
                    $apply  = $this->modifyAccount($setFeatures);
                    if(!$apply) return false;

                    $change  = $this->change_plan($new_plan);
                    if(!$change) return false;
                }
            }


            $new_options["config"]          = $new_config;
            $new_options["creation_info"]   = $new_c_info;

            return $new_options;
        }

        public function apply_updowngrade($orderopt=[],$product=[]){
            $creation_info      = isset($product["module_data"]["create_account"]) ? $product["module_data"]["create_account"] : [];
            if(!$creation_info || isset($product["module_data"]["plan"])) $creation_info = $product["module_data"];


            $p_plan             = isset($creation_info["plan"]) ? $creation_info["plan"] : false;

            if($p_plan){
                $change  = $this->change_plan($p_plan);
                if(!$change) return false;
            }

            $old_backup = isset($orderopt["creation_info"]["backup"]) ? $orderopt["creation_info"]["backup"] : false;
            $new_backup = isset($creation_info["backup"]) ? $creation_info["backup"] : false;

            $setFeatures        = [];

            if($creation_info["inode"] != $orderopt["creation_info"]["inode"])
                $setFeatures["inode"] = $creation_info["inode"];

            if($creation_info["limit_nproc"] != $orderopt["creation_info"]["limit_nproc"])
                $setFeatures["processes"] = $creation_info["limit_nproc"];

            if($creation_info["limit_nofile"] != $orderopt["creation_info"]["limit_nofile"])
                $setFeatures["openfiles"] = $creation_info["limit_nofile"];

            if($new_backup != $old_backup)
                $setFeatures["backup"] = $new_backup ? "on" : "off";

            if($setFeatures){
                $apply  = $this->modifyAccount($setFeatures);
                if(!$apply) return false;
                $change  = $this->change_plan($p_plan);
                if(!$change) return false;
            }

            return true;
        }

        public function UsernameGenerator($domain='',$half_mixed=false){
            $exp            = explode(".",$domain);
            $domain         = Filter::transliterate($exp[0]);
            $username       = $domain;
            $fchar          = substr($username,0,1);
            $size           = strlen($username);
            if($fchar == "0" || (int)$fchar)
                $username   = Utility::generate_hash(1,false,"l").substr($username,1,$size-1);

            /*if($half_mixed){
                $username   = substr($username,0,10);
                if($size>=8){
                    $username   = substr($username,0,4);
                    $username .= Utility::generate_hash(4,false,"d");
                }elseif($size>4 && $size<8){
                    $username   = substr($username,0,5);
                    $username .= Utility::generate_hash(3,false,"d");
                }elseif($size>=1 && $size<5){
                    $how        = (8 - $size);
                    $username   = substr($username,0,$size);
                    $username .= Utility::generate_hash($how,false,"d");
                }
            }else*/if($size>=8){
                $username   = substr($username,0,5);
                $username .= Utility::generate_hash(3,false,"l");
            }elseif($size>4 && $size<9){
                $username   = substr($username,0,5);
                $username .= Utility::generate_hash(3,false,"l");
            }elseif($size>=1 && $size<5){
                $how        = (8 - $size);
                $username   = substr($username,0,$size);
                $username .= Utility::generate_hash($how,false,"l");
            }

            return $username;
        }

        public function getEmailList(){
            return false;
        }
        public function getForwardsList(){
            return false;
        }
        public function addNewEmail($username,$domain,$password,$quota,$unlimited){
            return true;
        }
        public function addNewEmailForward($domain,$dest,$forward){
            return true;
        }
        public function setPassword($domain,$email,$password){
            return true;
        }
        public function setQuota($domain,$email,$quota,$unlimited){
            return true;
        }
        public function deleteEmail($domain,$email){
            return true;
        }
        public function deleteEmailForward($dest,$forward){
            return true;
        }
        public function getPasswordStrength($password){
            return 100;
        }
        public function getMailDomains(){
            return false;
        }
        public function getDomains(){
            return false;
        }

        public function changePassword($oldpw,$newpw){
            $user   = $this->config["user"];
            $apply  = $this->api->changepass_udp($user,$newpw);
            if(!$apply){
                $this->error = $this->api->error;
                return false;
            }
            return true;
        }

        public function setReseller($user,$params=[]){
            return true;
        }

        public function removeAccount($user=false){
            if(!$user) $user = $this->config["user"];
            $apply      = $this->api->account_del($user);
            if(!$apply){
                $this->error = $this->api->error;
                if(stristr($this->error,'account does not exist')) return true;
                return false;
            }
            return true;
        }

        public function removeReseller($user=false){
            return $this->removeAccount($user);
        }

        public function setupReseller($user=false,$params=[]){
            return true;
        }

        public function suspend(){
            $apply      = $this->api->account_susp($this->config["user"]);
            if(!$apply){
                $this->error = $this->api->error;
                return false;
            }
            return true;
        }

        public function suspend_reseller(){
            return $this->suspend();
        }

        public function unsuspend(){
            $apply      = $this->api->account_unsp($this->config["user"]);
            if(!$apply){
                $this->error = $this->api->error;
                return false;
            }

            return true;
        }

        public function unsuspend_reseller(){
            return $this->unsuspend();
        }

        public function change_plan($plan){
            if($plan == '') return true;

            $apply      = $this->api->changepack_udp($this->config["user"],$plan);
            if(!$apply){
                $this->error = $this->api->error;
                return false;
            }

            return true;
        }

        public function change_quota($quota){
            return true;
        }

        public function change_bandwidth($bwlimit){
            return true;
        }

        public function listAccounts(){

            $getData = $this->api->account_list();
            if(!$getData && !is_array($getData)){
                $this->error = $this->api->error;
                return false;
            }

            $data = [];

            if(isset($getData["msj"]) && is_array($getData["msj"])){

                if($getData){
                    foreach($getData["msj"] AS $row){
                        $item   = [];
                        $item["cdate"]      = $row["setup_date"];
                        $item["domain"]     = $row["domain"];
                        $item["username"]   = $row["username"];
                        $item["plan"]       = $row["idpackage"];
                        $item["unix_cdate"] = DateManager::strtotime($row["setup_date"]);
                        $data[] = $item;
                    }

                    Utility::sksort($data,"unix_cdate");
                }

            }else $this->error = $getData["msj"];

            return $data;

        }


        public function use_adminArea_SingleSignOn()
        {
            Utility::redirect($this->CentOSWebPanel_link());
            return true;
        }
        public function use_adminArea_root_SingleSignOn()
        {
            Utility::redirect($this->CentOSWebPanel_link($this->server["username"]));
            return true;
        }
        public function use_clientArea_SingleSignOn()
        {
            Utility::redirect($this->CentOSWebPanel_link());
            return true;
        }
        public function use_clientArea_webMail()
        {
            Utility::redirect($this->WebMail_link());
            return true;
        }

        private function CentOSWebPanel_link($user=''){
            $link = '';

            if($this->server["secure"])
                $link .= "https";
            else
                $link .= "http";

            $link .= "://".$this->server["name"].":";

            if($this->server["secure"])
                $link .= "2083";
            else
                $link .= "2082";

            $session = $this->api->user_session($user ? $user : $this->config["user"]);

            if($session && isset($session['status']) && $session['status'] == 'OK')
            {
                return $session['msj']['details'][0]['url'];
            }

            return $link;
        }
        private function WebMail_link(){
            $link = '';

            if($this->server["secure"])
                $link .= "https";
            else
                $link .= "http";

            $link .= "://".$this->options["domain"].":";

            if($this->server["secure"])
                $link .= "2096";
            else
                $link .= "2095";

            return $link;
        }

        public function clientArea()
        {
            $content    = '';
            $_page      = $this->page;
            $_data      = [
                'LANG' => $this->lang,
            ];

            if(!$_page) $_page = 'home';

            if($_page == 'home')
            {
                $_data["username"] = $this->config["user"];
                $_data["password"] = $this->config["password"];
            }

            $content .= $this->get_page('clientArea-'.$_page,$_data);
            return  $content;
        }

        public function getBandwidth($user=false){
            return false;
        }
        public function getDisk($user=false){
            return false;
        }

    }