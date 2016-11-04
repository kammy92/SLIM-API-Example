<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Karman Singh
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

     /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkMerchantLogin($mobile, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT id FROM `service_provider` where mobile = ? && password = ?");

        $stmt->bind_param("is", $mobile, $password);

        $stmt->execute();
        $res = array();
        $stmt->bind_result($id);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();
            $res["id"] = $id;
            $res["status"] = 1;

            $stmt->close();
            return $res;
            
        } else {
            $stmt->close();
            $res["id"] = 0;
            $res["status"] = 0;
            return $res;
        }
    }


/**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT code FROM api_key WHERE code = ? AND is_active = ?");
        $is_active = 1;
        $stmt->bind_param("si", $api_key, $is_active);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Validating merchant login key
     * If the login key is there in db, it is a valid key
     * @param String $login_key login key
     * @return boolean
     */
    public function isValidServiceProviderLoginKey($login_key) {
        $stmt = $this->conn->prepare("SELECT id FROM `service_provider` where login_key = ?");
        $stmt->bind_param("s", $login_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

     /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getServiceProviderId($login_key) {
        $stmt = $this->conn->prepare("SELECT id FROM `service_provider` where login_key = ?");
        $stmt->bind_param("s", $login_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching merchant by mobile and password
     * @param String $mobile Merchant mobile
     * @param String $password Merchant password
     */
    public function getMerchantByMobileAndPassword($mobile, $password) {
        $stmt = $this->conn->prepare("SELECT id, login_key FROM `service_provider` where mobile = ? && password = ?");
        $stmt->bind_param("ss", $mobile, $password);
        if ($stmt->execute()) {
            // $merchant = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($id, $login_key);
            $stmt->fetch();
            $merchant = array();
            $merchant["id"] = $id;
            $merchant["login_key"] = $login_key;
            $stmt->close();
            return $merchant;
        } else {
            return NULL;
        }
    }

    /**
     * Checking merchant login
     * @param String $mobile Merchant login mobile
     * @param String $password Merchant login password
     * @return boolean Merchant login status success/fail
     */
    public function checkMerchantLogin2($mobile, $password) {
        // fetching user by mobile and password
        $stmt = $this->conn->prepare("SELECT id FROM `service_provider` where mobile = ? && password = ?");
        $stmt->bind_param("is", $mobile, $password);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Found merchant with the mobile asnd password        
            $stmt->fetch();
            $stmt->close();
            return TRUE;
        } else {
            $stmt->close();
            // merchant not existed with the mobile and password
            return FALSE;
        }
    }


    /**
     * Fetching all merchant requests
     * @param String $merchant_id id of the user
     */
    public function getAllServiceProviderRequests($merchant_id) {
        $stmt1 = $this->conn->prepare("SELECT * FROM `declined_request` where service_provider_id = ?");
        $stmt1->bind_param("i", $merchant_id);
        $stmt1->execute();
        $stmt1->store_result();
        $num_rows = $stmt1->num_rows;
        $stmt1->close();
        if ($num_rows > 0) {
            $stmt2 = $this->conn->prepare("SELECT group_concat(service_request_id) as reqid FROM `declined_request` where service_provider_id = ?");
            $stmt2->bind_param("i", $merchant_id);
            $stmt2->execute();
            $stmt2->bind_result($concat);
            $stmt2->fetch();
            $stmt2->close();
           
            $stmt3 = $this->conn->prepare("SELECT r.* , a.latitude, a.longitude, a.addressline0, a.addressline1, a.addressline2, a.addressline3, a.locality, a.city, a.state, a.pincode, a.country FROM service_request r inner join user_address a on (r.address_id = a.id) WHERE r.id not in ( ".$concat." ) && r.status = 0 && r.progress = 0 ORDER by r.service_date ASC");
            $stmt3->execute();
            $requests = $stmt3->get_result();
            $stmt3->close();
            
            return $requests;
        } else {
            $stmt4 = $this->conn->prepare("SELECT r.* , a.latitude, a.longitude, a.addressline0, a.addressline1, a.addressline2, a.addressline3, a.locality, a.city, a.state, a.pincode, a.country from service_request r inner join user_address a on (r.address_id = a.id) where r.status = 0 && r.progress = 0 order by r.service_date ASC");
            $stmt4->execute();
            $requests = $stmt4->get_result();
            $stmt4->close();
            return $requests;
        }


     //   $stmt = $this->conn->prepare("SELECT t.* FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = ?");
     //   $stmt->bind_param("i", $merchant_id);
     //   $stmt->execute();
     //   $tasks = $stmt->get_result();
     //   $stmt->close();
     //   return $tasks;
    }

    /**
     * Fetching single merchant request
     * @param String $merchant_id id of the merchant
     * @param String $request_id id of the request
     */
    public function getServiceProviderRequest($merchant_id, $request_id) {
        $stmt = $this->conn->prepare("SELECT r.* , a.latitude, a.longitude, a.addressline0, a.addressline1, a.addressline2, a.addressline3, a.locality, a.city, a.state, a.pincode, a.country from service_request r inner join user_address a on (r.address_id = a.id) where r.status = 0 && r.progress = 0 && r.id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result();
        $stmt->close();
        return $request;
    }


    /**
     * Creating new task
     * @param String $merchant_id merchant id to whom task belongs to
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function addNewDriver($merchant_id, $name, $mobile) {
        $stmt = $this->conn->prepare("SELECT * FROM `employee` where name = ? && mobile = ? && service_provider_id = ?");
        $stmt->bind_param("ssi", $name, $mobile, $merchant_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if ($num_rows > 0) {
            return NULL;
        } else {
            date_default_timezone_set("Asia/Kolkata");
            $dt = new DateTime();
            $dt->format('Y-m-d H:i:s');
            $newdt = $dt->format('Y-m-d H:i:s');

            $stmt2 = $this->conn->prepare("INSERT INTO `employee`(`service_provider_id`, `name`, `mobile`, `created_at`) VALUES ( ?, ?, ?, ?)");
            $stmt2->bind_param("isss", $merchant_id, $name, $mobile, $newdt);
            $result = $stmt2->execute();
            $stmt2->close();
            return $result;
        }
    }

    /**
     * Accept request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function acceptRequest($merchant_id, $request_id, $driver_id) {
        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');
        $is_default = 0;

        $stmt = $this->conn->prepare("SELECT * FROM `service_request` WHERE id = ? && progress = 0");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if ($num_rows > 0) {
            $stmt1 = $this->conn->prepare("UPDATE `service_request` SET `progress` = 1 WHERE id = ?");
            $stmt1->bind_param("i", $request_id);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->conn->prepare("SELECT * FROM `employee` WHERE id = ? && is_default = 1");
            $stmt2->bind_param("i", $driver_id);
            $stmt2->execute();
            $stmt2->store_result();
            $num_rows2 = $stmt2->num_rows;
            $stmt2->close();
      
            if ($num_rows2 > 0) 
                $is_default = 1;
                
            $stmt3 = $this->conn->prepare("UPDATE `service_request` SET `status` = 1 , `service_provider_id` = ? , `employee_id` = ? WHERE `id` = ?");
            $stmt3->bind_param("iii", $merchant_id, $driver_id, $request_id);
            $result = $stmt3->execute();
            $stmt3->close();
            if($result) {
                $stmt4 = $this->conn->prepare("INSERT INTO `accepted_request`(`service_request_id`, `service_provider_id`, `employee_id`, `created_at`) VALUES ( ?, ?, ?, ?)");
                $stmt4->bind_param("iiis", $request_id, $merchant_id, $driver_id, $newdt);
                $result2 = $stmt4->execute();
                $stmt4->close();    
            }
            if ($result && $result2) {
                $stmt5 = $this->conn->prepare("UPDATE `service_request` SET `progress` = 2 WHERE id = ?");
                $stmt5->bind_param("i", $request_id);
                $result = $stmt5->execute();
                $stmt5->close();
            }

            if (!$result) {
                $stmt5 = $this->conn->prepare("UPDATE `service_request` SET `progress` = 0 WHERE id = ?");
                $stmt5->bind_param("i", $request_id);
                $result = $stmt5->execute();
                $stmt5->close();
            }    
                         
            $driver_name;
            $driver_mobile;
            $user_name;
            $user_mobile;
            $service_date;
            $service_time;
            $street_address;
            $locality;
            

            $stmt6 = $this->conn->prepare("SELECT `name`, `mobile` FROM `employee` WHERE id = ?");
            $stmt6->bind_param("i", $driver_id);
            if ($stmt6->execute()) {
                $stmt6->bind_result($driver_name, $driver_mobile);
                $stmt6->fetch();
                $stmt6->close();
            }
            
            $stmt7 = $this->conn->prepare("SELECT u.name, u.mobile, r.service_time, r.service_date, a.addressline0, a.addressline1 from service_request r inner join user u on (r.user_id = u.id) inner join user_address a on (r.address_id = a.id) where r.id = ?");
            $stmt7->bind_param("i", $request_id);
            if ($stmt7->execute()) {
                $stmt7->bind_result($user_name, $user_mobile, $timetemp, $datetemp, $street_address, $locality);
                $stmt7->fetch();
                $date22 = explode("-", $datetemp);
                $service_date = $date22[2]."/".$date22[1]."/".$date22[0];
                $time22 = explode(":", $timetemp);
                $service_time = $time22[0].":".$time22[1];
                $stmt7->close();
            }

            switch ($is_default) {
                    case 0:    
                        $message="Driver ".$driver_name." will be coming for your service with request ID ".$request_id.". You can contact the driver on mobile no. ".$driver_mobile.".";
                        $message=urlencode($message);
                        $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$user_mobile."&text=".$message."";
                        $filename=curl_init();
                        curl_setopt($filename,CURLOPT_URL, $uri );
                        curl_setopt($filename, CURLOPT_HEADER, 0);
                        curl_exec($filename);
                        curl_close($filename);

                        $message2="You have been assigned a job with Request ID ".$request_id.". Details of the service - Date ".$service_date.", Location ".$street_address."".$locality.", Time ".$service_time.", Mobile No. ".$user_mobile."";
                        $message2=urlencode($message2);    
                        $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$driver_mobile."&text=".$message2."";
                        $filename2=curl_init();
                        curl_setopt($filename2,CURLOPT_URL, $uri2 );
                        curl_setopt($filename2, CURLOPT_HEADER, 0);
                        curl_exec($filename2);
                        curl_close($filename2);
                        break;
                    case 1:
                        $message3="Your booking is confirmed. You will be serviced by ".$driver_name.". Service provider can be contacted at ".$driver_mobile.".";
                        $message3=urlencode($message3);
                        $uri3 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$user_mobile."&text=".$message3."";
                        $filename3=curl_init();
                        curl_setopt($filename3,CURLOPT_URL, $uri3 );
                        curl_setopt($filename3, CURLOPT_HEADER, 0);
                        curl_exec($filename3);
                        curl_close($filename3);
                        break;
                }
            return 1;
        } else {
            return 2;
        }
    }

  /**
     * Decline request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function declineRequest($merchant_id, $request_id, $decline_option, $other_reason) {
        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO `declined_request`(`service_request_id`, `service_provider_id`, `reason_id`, `reason_text`, `created_at`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $request_id, $merchant_id, $decline_option, $other_reason, $newdt);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

  /**
     * Fetching all merchant drivers
     * @param String $merchant_id id of the merchant
     */
    public function getAllDrivers($merchant_id) {
        $stmt = $this->conn->prepare("SELECT * from employee where service_provider_id = ? && is_default = 0 order by name asc");
        $stmt->bind_param("i", $merchant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    
    /**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function getAllJobs($merchant_id) {
        $stmt = $this->conn->prepare("SELECT r.service_date , r.service_time , r.cartype , r.status, a.addressline1 , r.id , d.name from service_request r inner join employee d on (r.employee_id = d.id) inner join user_address a on (r.address_id = a.id) where r.service_provider_id = ? && r.status = 1 order by r.service_date DESC");
        $stmt->bind_param("i", $merchant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Fetching default merchant drivers
     * @param String $merchant_id id of the merchant
     */
    public function getDefaultDrivers($merchant_id) {
        $stmt = $this->conn->prepare("SELECT * from employee where service_provider_id = ? && is_default = 1");
        $stmt->bind_param("i", $merchant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Removing a driver
     * @param String $driver_id id of the driver to delete
     */
    public function removeDriver($merchant_id, $driver_id) {
        $stmt = $this->conn->prepare("DELETE FROM `employee` WHERE id = ? && service_provider_id = ?");
        $stmt->bind_param("ii", $driver_id, $merchant_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }


    /**
     * Updating Driver
     * @param String $merchant_id id of the merchant
     * @param String $request_id id of the request
     * @param String $driver_id id of the driver
     */
    public function editDriver($merchant_id, $request_id, $driver_id) {
        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        $stmt = $this->conn->prepare("UPDATE `service_request` SET `employee_id` = ? WHERE id = ? && service_provider_id = ?");
        $stmt->bind_param("iii", $driver_id, $request_id, $merchant_id);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            $stmt2 = $this->conn->prepare("UPDATE `accepted_request` SET `employee_id` = ? WHERE service_request_id = ? && service_provider_id = ?");
            $stmt2->bind_param("iii", $driver_id, $request_id, $merchant_id);
            $stmt2->execute();
            $stmt2->close();

            $driver_name;
            $driver_mobile;
            $user_mobile;
            $user_name;
            $service_date;
            $service_time;
            $street_address;
            $locality;

            $stmt3 = $this->conn->prepare("SELECT `name`, `mobile` FROM `employee` WHERE id = ?");
            $stmt3->bind_param("i", $driver_id);
            if ($stmt3->execute()) {
                $stmt3->bind_result($driver_name, $driver_mobile);
                $stmt3->fetch();
                $stmt3->close();
            }

            $stmt4 = $this->conn->prepare("SELECT u.name, u.mobile, r.service_time, r.service_date, a.addressline0, a.addressline1 from service_request r inner join user u on (r.user_id = u.id) inner join user_address a on (r.address_id = a.id) where r.id = ?");
            $stmt4->bind_param("i", $request_id);
            if ($stmt4->execute()) {
                $stmt4->bind_result($user_name, $user_mobile, $timetemp, $datetemp, $street_address, $locality);
                $stmt4->fetch();
                $date22 = explode("-", $datetemp);
                $service_date = $date22[2]."/".$date22[1]."/".$date22[0];
                $time22 = explode(":", $timetemp);
                $service_time = $time22[0].":".$time22[1];
                $stmt4->close();
            }

            $message="You have been assigned a new driver. The details of the new driver are as follows: Name - ".$driver_name.", Mobile No. - ".$driver_mobile."";
            $message=urlencode($message);
            $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$user_mobile."&text=".$message."";
            $filename=curl_init();
            curl_setopt($filename,CURLOPT_URL, $uri );
            curl_setopt($filename, CURLOPT_HEADER, 0);
            curl_exec($filename);
            curl_close($filename);

            $message2="You have been assigned a job with Request ID ".$request_id.". Details of the service - Date ".$service_date.", Location ".$street_address.", ".$locality.", Time ".$service_time.", Mobile No. ".$user_mobile."";
            $message2=urlencode($message2);    
            $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$driver_mobile."&text=".$message2."";
            $filename2=curl_init();
            curl_setopt($filename2,CURLOPT_URL, $uri2 );
            curl_setopt($filename2, CURLOPT_HEADER, 0);
            curl_exec($filename2);
            curl_close($filename2);
            return 1;
        }
        else
            return 0;
    }

    



/* user functions */


/**
     * Validating user login key
     * If the login key is there in db, it is a valid key
     * @param String $login_key login key
     * @return boolean
     */
    public function isValidUserLoginKey($login_key) {
        $stmt = $this->conn->prepare("SELECT id FROM `user` where mobile = ?");
        $stmt->bind_param("i", $login_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($login_key) {
        $stmt = $this->conn->prepare("SELECT id FROM `user` where mobile = ?");
        $stmt->bind_param("s", $login_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Generate OTP
     * @param String $mobile 
     */
    public function generateOTP($mobile) {
        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $expiry_time = $dt->format('Y-m-d H:i:s');

        $random_id_length = 6; 
        $rnd_id = uniqid(rand(),10); 
        $rnd_id = substr($rnd_id,0,$random_id_length); 
        $otp = $rnd_id;

        $stmt = $this->conn->prepare("SELECT * from otp where mobile = ? && is_used = 0");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if ($num_rows > 0) {
            return NULL;
        } else {
            
            $stmt2 = $this->conn->prepare("INSERT INTO `otp`(`mobile`, `random_otp`, `expiry_time`, `created_at`) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiss", $mobile, $otp, $expiry_time, $newdt);
            $result = $stmt2->execute();
            $stmt2->close();
            return $result;
        }
    }

/**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function getOTP($mobile) {
        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        $stmt = $this->conn->prepare("SELECT random_otp from otp where mobile = ? && is_used = 0");
        $stmt->bind_param("s", $mobile);
        if ($stmt->execute()) {
            $stmt->bind_result($otp);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $message="Your One Time Password (OTP) to access Call Sikandar app is ".$otp.". The password will expire within 30 minutes.";
            $message=urlencode($message);
            $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$mobile."&text=".$message."";
            $filename=curl_init();
            curl_setopt($filename,CURLOPT_URL, $uri );
            curl_setopt($filename, CURLOPT_HEADER, 0);
            curl_exec($filename);
            curl_close($filename);   
            //  http://23.254.128.22:9080/urldreamclient/dreamurl?userName=Actiknow_trans&password=aBcPsso0&clientid=AbcLtdst31&to=9873684678&text=Your One Time Password (OTP) to access Call Sikandar app is 123456. The password will expire within 30 minutes.
            return $otp;
        } else {
            return NULL;
        }
    }



/**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function checkOTP($mobile, $otp) {
        $stmt = $this->conn->prepare("SELECT * from otp where mobile = ? && random_otp = ?");
        $stmt->bind_param("ii", $mobile, $otp);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;   
    }


    /**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function OTPUsed($mobile, $otp) {
        $stmt = $this->conn->prepare("UPDATE `otp` SET `is_used` = '1' WHERE mobile = ? && random_otp = ?");
        $stmt->bind_param("ii", $mobile, $otp);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;   
    }

    /**
     * Submitting User
     * @param String $name name of the user
     * @param String $mobile mobile of the user
     * @param String $email email of the user
     */
    public function insertUser($name, $mobile, $email) {
        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO `user`(`name`, `mobile`, `email`, `created_at`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $mobile, $email, $newdt);
        if ($stmt->execute()) {
            $stmt->fetch();
            $stmt->close();
            return 1;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function favouriteExist($user_id, $addressname, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode) {
        $stmt = $this->conn->prepare("SELECT * FROM `user_address` WHERE `addressline0` = ? && `addressline1` = ? && `addressline2` = ? && `addressline3` = ? && locality = ? && city = ? && user_id = ?");
        $stmt->bind_param("ssssssi", $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;   
    }


    /**
     * Submitting User
     * @param String $name name of the user
     * @param String $mobile mobile of the user
     * @param String $email email of the user
     */
    public function addNewFavourite($user_id, $addressname, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode) {
        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');
                   
        $stmt = $this->conn->prepare("INSERT INTO `user_address`(`user_id`, `addressname`, `latitude`, `longitude`, `addressline0`, `addressline1`, 
            `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssiss", $user_id, $addressname, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $pincode, $country, $newdt);
        if ($stmt->execute()) {
            $stmt->fetch();
            $stmt->close();
            return 1;
        } else {
            return NULL;
        }
    }


    /**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function checkUser($mobile) {
        $stmt = $this->conn->prepare("SELECT * FROM `user` where mobile = ?");
        $stmt->bind_param("i", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;   
    }

    /**
     * Fetching all merchant drivers
     * @param String $merchant_id id of the merchant
     */
    public function getUser($mobile) {
        $stmt = $this->conn->prepare("SELECT id, name, mobile, email FROM `user` where mobile = ?");
        $stmt->bind_param("i", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    
    /**
     * Fetching all merchant drivers
     * @param String $merchant_id id of the merchant
     */
    public function updateUser($name, $mobile, $email) {
        $stmt = $this->conn->prepare("UPDATE `user` SET `name` = ?, `email` = ? WHERE mobile = ?");
        $stmt->bind_param("ssi", $name, $email, $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;   
    }


    /**
     * Fetching single merchant request
     * @param String $merchant_id id of the merchant
     * @param String $request_id id of the request
     */
    public function getTerms($service_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `terms` where service_id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $terms = $stmt->get_result();
        $stmt->close();
        return $terms;
    }

    /**
     * Fetching single merchant request
     * @param String $merchant_id id of the merchant
     * @param String $request_id id of the request
     */
    public function getPricing($service_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `pricing` where service_id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $pricing = $stmt->get_result();
        $stmt->close();
        return $pricing;
    }

    /**
     * Fetching all merchant jobs
     * @param String $merchant_id id of the merchant
     */
    public function getFavouriteAddress($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `user_address` WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Fetching all merchant requests
     * @param String $merchant_id id of the user
     */
    public function getAllUserRequests($user_id) {
        $stmt = $this->conn->prepare("SELECT r.* , e.name, e.mobile from service_request r inner join employee e on (r.employee_id = e.id) where r.user_id= ? && (r.status = 0 || r.status = 1)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $requests = $stmt->get_result();
        $stmt->close();
        return $requests;
    }

    /**
     * Fetching single merchant request
     * @param String $merchant_id id of the merchant
     * @param String $request_id id of the request
     */
    public function getUserRequest($user_id, $request_id) {
        $stmt = $this->conn->prepare("SELECT r.* , a.* from service_request r inner join user_address a on (r.address_id = a.id) where r.id = ? && r.user_id = ?");
        $stmt->bind_param("ii", $request_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $request = $stmt->get_result();
        $stmt->close();
    }


    /**
     * Fetching all merchant requests
     * @param String $merchant_id id of the user
     */
    public function getDriverPrice($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `driver_price`");
        $stmt->execute();
        $driver_price = $stmt->get_result();
        $stmt->close();
        return $driver_price;
    }

    /**
     * Fetching all merchant requests
     * @param String $merchant_id id of the user
     */
    public function getAreaServiced($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `area_serviced`");
        $stmt->execute();
        $area_serviced = $stmt->get_result();
        $stmt->close();
        return $area_serviced;
    }

     /**
     * Fetching all merchant requests
     * @param String $merchant_id id of the user
     */
    public function getIfRating($user_id) {
        $response = array();
        $request_id_array = array();

         //   $stmt1 = $this->conn->prepare("SELECT id FROM `service_rating` where service_request_id IN (".$id22.")"); 
            
        $stmt2 = $this->conn->prepare("SELECT group_concat(id) as reqid FROM `service_request` where status = 4 && user_id = ?");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $stmt2->bind_result($concat);
        $stmt2->fetch();
        $stmt2->close();
           
        $stmt3 = $this->conn->prepare("SELECT id FROM `service_rating` where service_request_id IN (".$concat.")");
        $stmt3->execute();
        $requests = $stmt3->get_result();
        $stmt3->close();
       
        return $requests;

/*
        $stmt = $this->conn->prepare("SELECT id FROM `service_request` where status = 4 && user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->bind_result($request_id);
            while($stmt->fetch()){
                array_push($request_id_array, $request_id);
            }
            $stmt->close();

            for($i=0; $i<count($request_id_array); $i++) {
                $id22 = $request_id_array[$i];
               //echo "SELECT id FROM `service_rating` where service_request_id IN (".$id22.")";
                $stmt1 = $this->conn->prepare("SELECT id FROM `service_rating` where service_request_id IN (".$id22.")"); 
                 if ($stmt1->execute()) {
                    echo "id ".$id22;
                    $stmt1->bind_result($id2);
                    $result = $stmt1->get_result();
                    $stmt1->store_result();
                    $num_rows = $stmt1->num_rows;
                    $stmt1->close();
                }
                print_r($result);
             //   if ($num_rows > 0) {
             //       $response["exist"] = 1;
             //       $response["request_id"] = $id2;
             //   }
            }

        //    while ($req = $request_id_array->fetch()) {
         //       echo "req_id ".$req;
        //    }
           
            //$response["status"] = 1;      
        }
        else
        {
            $result = NULL;
            //$response["status"] = 0;
        }

        return $result;
  */

    }


     /**
     * Removing a driver
     * @param String $driver_id id of the driver to delete
     */
    public function removeRequest($user_id, $request_id) {
       
        $response = array();
        
        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';
        $is_accepted = 0;
        
        $stmt = $this->conn->prepare("SELECT * FROM `service_request` WHERE id = ? && status != 0");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if($num_rows>0) {
            $stmt1 = $this->conn->prepare("UPDATE `service_request` SET `status`= 2 WHERE user_id = ? && id = ?");
            $stmt1->bind_param("ii", $user_id, $request_id);
            $stmt1->execute();
            $stmt1->store_result();
            $num_rows2 = $stmt1->num_rows;
            $stmt1->close();
            $is_accepted = 1;
        }
        else
            $is_accepted = 0;

        $service_provider_mobile;
        $driver_mobile;
        $user_name;

        switch ($is_accepted) {
            case 1:
                $stmt2 = $this->conn->prepare("SELECT s.mobile from service_provider s inner join service_request r on (r.service_provider_id = s.id) where r.id = ?");
                $stmt2->bind_param("i", $request_id);
                if ($stmt2->execute()) {
                    $stmt2->bind_result($service_provider_mobile);
                    $stmt2->fetch();
                    $stmt2->close();
                }
                $stmt3 = $this->conn->prepare("SELECT e.mobile from employee e inner join service_request r on (r.employee_id = e.id) where r.id = ?");
                $stmt3->bind_param("i", $request_id);
                if ($stmt3->execute()) {
                    $stmt3->bind_result($driver_mobile);
                    $stmt3->fetch();
                    $stmt3->close();
                }
                $stmt4 = $this->conn->prepare("SELECT u.name from user u inner join service_request r on (r.user_id = u.id) where r.id = ?");
                $stmt4->bind_param("i", $request_id);
                if ($stmt4->execute()) {
                    $stmt4->bind_result($user_name);
                    $stmt4->fetch();
                    $stmt4->close();
                }    
                $message="User has cancelled the driver booking with Request ID ".$request_id.". Kindly do not send the driver for service.";
                $message=urlencode($message);
                $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$service_provider_mobile."&text=".$message."";
                $filename=curl_init();
                curl_setopt($filename,CURLOPT_URL, $uri );
                curl_setopt($filename, CURLOPT_HEADER, 0);
                curl_exec($filename);
                curl_close($filename);

                $message2="Your booking with Request ID ".$request_id." has been cancelled by the user ".$user_name."";
                $message2=urlencode($message2);
                $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$driver_mobile."&text=".$message2."";
                $filename2=curl_init();
                curl_setopt($filename2,CURLOPT_URL, $uri2 );
                curl_setopt($filename2, CURLOPT_HEADER, 0);
                curl_exec($filename2);
                curl_close($filename2);
                break;
        }
        return $num_rows > 0;   
    }


     /**
     * Decline request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function submitRating($request_id, $rating, $remark) {
        $stmt = $this->conn->prepare("INSERT INTO `service_rating`(`service_request_id`, `service_rating`, `comments`) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $request_id, $rating, $remark);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

/**
     * Accept request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function submitRequest($user_id, $service_id, $date, $time, $cartype, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode) {

        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $address_id;
        $request_id;
        $status;
        $zero = 0;

        $stmt = $this->conn->prepare("INSERT INTO `user_address`(`user_id`, `latitude`, `longitude`, `addressline0`, `addressline1`, `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssiss", $zero, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $pincode, $country, $newdt);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
          $stmt1 = $this->conn->prepare("SELECT id from `user_address` where  addressline0 = ? && addressline1 = ? && addressline2 = ? && addressline3 = ? && locality = ? && city = ? && user_id = ? && created_at = ?");
            $stmt1->bind_param("ssssssis", $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $zero, $newdt);
            if ($stmt1->execute()) {
                $stmt1->bind_result($address_id);
                $stmt1->fetch();
                $stmt1->close();
            }
        }
        $stmt2 = $this->conn->prepare("INSERT INTO `service_request`(`user_id`, `service_id`, `address_id`, `service_time`, `service_date`,`cartype`, `created_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiissis", $user_id, $service_id, $address_id, $time, $date, $cartype, $newdt);
        $result2 = $stmt2->execute();
        $stmt2->close();
        if ($result2) {
          $stmt3 = $this->conn->prepare("SELECT `id` FROM `service_request` WHERE user_id = ? && address_id = ? && service_time = ? && service_date = ? && created_at = ?");
            $stmt3->bind_param("iisss", $user_id, $address_id, $time, $date, $newdt);
            if ($stmt3->execute()) {
                $stmt3->bind_result($request_id);
                $stmt3->fetch();
                $stmt3->close();
            }
            $status = 1;
        }
        else
            $status = 0;
 
        $stmt11 = $this->conn->prepare("SELECT name, mobile from `user` where `id` = ?");
        $stmt11->bind_param("i", $user_id);
        if ($stmt11->execute()) {
            $stmt11->bind_result($user_name, $user_mobile);
            $stmt11->fetch();                    
            $stmt11->close();
        }  
      
        switch ($status) {
            case 1 :   
                $message="Thank you for submitting your request. Your Request ID is ".$request_id.". You will get a message with driver details in a short while.";
                $message=urlencode($message);
                $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$user_mobile."&text=".$message."";
                $filename=curl_init();
                curl_setopt($filename,CURLOPT_URL, $uri );
                curl_setopt($filename, CURLOPT_HEADER, 0);
                curl_exec($filename);
                curl_close($filename);
                $ServiceProviderMobile=array();            
                $stmt10 = $this->conn->prepare("SELECT mobile from `service_provider` where id != 0");
                if ($stmt10->execute()) {
                    $stmt10->bind_result($mobile);
                    while($stmt10->fetch()){
                        $merchant_mobile = $mobile;   
                        $message2="You have received a new request from ".$user_name." with ".$user_mobile." at ".$addressline0.", ".$addressline1.", ".$city.".";
                        $message2=urlencode($message2);
                        $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$merchant_mobile."&text=".$message2."";
                        $filename2=curl_init();
                        curl_setopt($filename2,CURLOPT_URL, $uri2 );
                        curl_setopt($filename2, CURLOPT_HEADER, 0);
                        curl_exec($filename2);
                        curl_close($filename2);
                    }
                    $stmt10->close();  
                }
                $response["status"] = $status;
                $response["request_id"] = $request_id;
                return $response;
            case 0 :
                $response["status"] = $status;
                return $response;
        }
    }



/**
     * Accept request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function updateRequest($user_id, $request_id, $service_id, $date, $time, $cartype, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode) {

        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $service_provider_mobile;
        $driver_mobile;
        $user_mobile;
        $user_name;

        $zero = 0;

        $stmt = $this->conn->prepare("INSERT INTO `user_address`(`user_id`, `latitude`, `longitude`, `addressline0`, `addressline1`, `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssiss", $zero, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $pincode, $country, $newdt);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            $stmt1 = $this->conn->prepare("SELECT id from `user_address` where  addressline0 = ? && addressline1 = ? && addressline2 = ? && addressline3 = ? && locality = ? && city = ? && user_id = ? && created_at = ?");
            $stmt1->bind_param("ssssssis", $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $zero, $newdt);
            if ($stmt1->execute()) {
                $stmt1->bind_result($address_id);
                $stmt1->fetch();
                $stmt1->close();
            }
        }

        $stmt2 = $this->conn->prepare("SELECT s.mobile from service_provider s inner join service_request r on (r.service_provider_id = s.id) where r.id = ?");
        $stmt2->bind_param("i", $request_id);
        if ($stmt2->execute()) {
            $stmt2->bind_result($service_provider_mobile);
            $stmt2->fetch();
            $stmt2->close();
        }
        
        $stmt3 = $this->conn->prepare("SELECT e.mobile from employee e inner join service_request r on (r.employee_id = e.id) where r.id = ?");
        $stmt3->bind_param("i", $request_id);
        if ($stmt3->execute()) {                
            $stmt3->bind_result($driver_mobile);
            $stmt3->fetch();
            $stmt3->close();
        }
    
        $stmt4 = $this->conn->prepare("SELECT u.name from user u inner join service_request r on (r.user_id = u.id) where r.id = ?");
        $stmt4->bind_param("i", $request_id);
        if ($stmt4->execute()) {
            $stmt4->bind_result($user_name);                
            $stmt4->fetch();                    
            $stmt4->close();
        }  

        $stmt5 = $this->conn->prepare("UPDATE `service_request` SET `user_id`= ?,`service_id`= ?,`address_id`= ?,`service_time`= ?,`service_date`= ?,`cartype`= ?,`status`= ?,`service_provider_id`= ?,`employee_id`= ?, `progress` = ? WHERE `id` = ?");
        $stmt5->bind_param("iiissiiiiii", $user_id, $service_id, $address_id, $time, $date, $cartype, $zero, $zero, $zero, $zero, $request_id);
        $result5 = $stmt5->execute();
        $stmt5->close();
        if ($result5) {
            $stmt5 = $this->conn->prepare("DELETE FROM `accepted_request` WHERE `service_request_id` = ?");
            $stmt5->bind_param("i", $request_id);
            $stmt5->execute();
            $stmt5->close();
            $status = 1;
        } else
            $status = 0; 

       
        $stmt6 = $this->conn->prepare("SELECT name, mobile from `user` where `id` = ?");
        $stmt6->bind_param("i", $user_id);
        if ($stmt6->execute()) {
            $stmt6->bind_result($user_name, $user_mobile);
            $stmt6->fetch();                    
            $stmt6->close();
        }  
      
        switch ($status) {
            case 0 :   
                $response["status"] = $status;
                return $response;

            case 1 :
                $message="User has cancelled the driver booking with Request ID ".$request_id.". Kindly do not send the driver for service.";
                $message=urlencode($message);
                $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$service_provider_mobile."&text=".$message."";
                $filename=curl_init();
                curl_setopt($filename,CURLOPT_URL, $uri );
                curl_setopt($filename, CURLOPT_HEADER, 0);
                curl_exec($filename);
                curl_close($filename);

                $message1="Your booking with Request ID ".$request_id." has been cancelled by the user ".$user_name."";
                $message1=urlencode($message1);
                $uri1 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$driver_mobile."&text=".$message1."";
                $filename1=curl_init();
                curl_setopt($filename1,CURLOPT_URL, $uri1 );
                curl_setopt($filename1, CURLOPT_HEADER, 0);
                curl_exec($filename1);
                curl_close($filename1);
                
                $stmt7 = $this->conn->prepare("SELECT mobile from `service_provider` where id != 0");
                if ($stmt7->execute()) {
                    $stmt7->bind_result($mobile);
                    while($stmt7->fetch()){
                        $merchant_mobile = $mobile;   
                        $message2="You have received a new request from ".$user_name." with ".$user_mobile." at ".$addressline0.", ".$addressline1.", ".$city.".";
                        $message2=urlencode($message2);
                        $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$merchant_mobile."&text=".$message2."";
                        $filename2=curl_init();
                        curl_setopt($filename2,CURLOPT_URL, $uri2 );
                        curl_setopt($filename2, CURLOPT_HEADER, 0);
                        curl_exec($filename2);
                        curl_close($filename2);
                    }
                    $stmt7->close();
                }
                $response["status"] = $status;
                $response["request_id"] = $request_id;
                return $response;
        }
    }




 /**
     * Accept request
     * @param String $merchant_id 
     * @param String $request_id 
     * @param String $name name text
     * @param String $mobile mobile text
     */
    public function submitRequest2($user_id, $request_id, $service_id, $date, $time, $cartype, $address_id, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode) {

        $userId='Actiknow_trans';
        $password='aBcPsso0';
        $clientId='AbcLtdst31';

        date_default_timezone_set("Asia/Kolkata");
        $dt = new DateTime();
        $dt->format('Y-m-d H:i:s');
        $newdt = $dt->format('Y-m-d H:i:s');

        $address_id2; // to be used to store the address id of the address entered in the request
        $request_id2 = $request_id;
            
        $is_edit = 0;   //  0 => default, 1 => new request, 2 => edit request
        
        $service_provider_mobile;
        $driver_mobile;
        $user_name;

        $zero = 0;

        if($request_id == 0) {
            $stmt = $this->conn->prepare("INSERT INTO `user_address`(`user_id`, `latitude`, `longitude`, `addressline0`, `addressline1`, `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssssssiss", $zero, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $pincode, $country, $newdt);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
              $stmt1 = $this->conn->prepare("SELECT id from `user_address` where  addressline0 = ? && addressline1 = ? && addressline2 = ? && addressline3 = ? && locality = ? && city = ? && user_id = ? && created_at = ?");
                $stmt1->bind_param("ssssssis", $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $zero, $newdt);
                if ($stmt1->execute()) {
                    $stmt1->bind_result($address_id2);
                    $stmt1->fetch();
                    $stmt1->close();
                }
            }
            $stmt2 = $this->conn->prepare("INSERT INTO `service_request`(`user_id`, `service_id`, `address_id`, `service_time`, `service_date`,`cartype`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iiissis", $user_id, $service_id, $address_id2, $time, $date, $cartype, $newdt);
            $result2 = $stmt2->execute();
            $stmt2->close();
            if ($result2) {
              $stmt3 = $this->conn->prepare("SELECT `id` FROM `service_request` WHERE user_id = ? && address_id = ? && service_time = ? && service_date = ? && created_at = ?");
                $stmt3->bind_param("iisss", $user_id, $address_id2, $time, $date, $newdt);
                if ($stmt3->execute()) {
                    $stmt3->bind_result($request_id2);
                    $stmt3->fetch();
                    $stmt3->close();
                }
            }
            $is_edit = 1;
           
        } else if ($request_id != 0) {
            $stmt4 = $this->conn->prepare("INSERT INTO `user_address`(`user_id`, `latitude`, `longitude`, `addressline0`, `addressline1`, `addressline2`, `addressline3`, `locality`, `city`, `state`, `pincode`, `country`, `created_at`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt4->bind_param("isssssssssiss", $zero, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $pincode, $country, $newdt);
            $result = $stmt4->execute();
            $stmt4->close();
            if ($result) {
              $stmt5 = $this->conn->prepare("SELECT id from `user_address` where  addressline0 = ? && addressline1 = ? && addressline2 = ? && addressline3 = ? && locality = ? && city = ? && user_id = ? && created_at = ?");
                $stmt5->bind_param("ssssssis", $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $zero, $newdt);
                if ($stmt5->execute()) {
                    $stmt5->bind_result($address_id2);
                    $stmt5->fetch();
                    $stmt5->close();
                }
            }

            $stmt6 = $this->conn->prepare("SELECT s.mobile from service_provider s inner join service_request r on (r.service_provider_id = s.id) where r.id = ?");
            $stmt6->bind_param("i", $request_id2);
            if ($stmt6->execute()) {
                $stmt6->bind_result($service_provider_mobile);
                $stmt6->fetch();
                $stmt6->close();
            }
            $stmt7 = $this->conn->prepare("SELECT e.mobile from employee e inner join service_request r on (r.employee_id = e.id) where r.id = ?");
            $stmt7->bind_param("i", $request_id2);
            if ($stmt7->execute()) {
                $stmt7->bind_result($driver_mobile);
                $stmt7->fetch();
                $stmt7->close();
            }
            $stmt8 = $this->conn->prepare("SELECT u.name from user u inner join service_request r on (r.user_id = u.id) where r.id = ?");
            $stmt8->bind_param("i", $request_id2);
            if ($stmt8->execute()) {
                $stmt8->bind_result($user_name);
                $stmt8->fetch();                    
                $stmt8->close();
            }  

            $stmt9 = $this->conn->prepare("UPDATE `service_request` SET `user_id`= ?,`service_id`= ?,`address_id`= ?,`service_time`= ?,`service_date`= ?,`cartype`= ?,`status`= ?,`service_provider_id`= ?,`employee_id`= ? WHERE `id` = ?");
            $stmt9->bind_param("iiissiiiii", $user_id, $service_id, $address_id2, $time, $date, $cartype, $zero, $zero, $zero, $request_id);
            $result2 = $stmt9->execute();
            $stmt9->close();
            if ($result2) {
                $stmt10 = $this->conn->prepare("DELETE FROM `accepted_request` WHERE `service_request_id` = ?");
                $stmt10->bind_param("i", $request_id);
                $stmt10->execute();
                $stmt10->close();
            }
            $is_edit = 2;    
        }
        $status;
        if($result2) {
            switch ($is_edit) {
                case 1:
                    $status = 1;
                break;
                case 2:
                    $status = 2;
                    break;
                default:
                    $status = 3;
                    break;
            }
        }

        $user_name;
        $user_mobile;

        $stmt11 = $this->conn->prepare("SELECT name, mobile from `user` where `id` = ?");
        $stmt11->bind_param("i", $user_id);
        if ($stmt11->execute()) {
            $stmt11->bind_result($user_name, $user_mobile);
            $stmt11->fetch();                    
            $stmt11->close();
        }  
      
        switch ($status) {
            case 1 :   
                $message="Thank you for submitting your request. Your Request ID is ".$request_id2.". You will get a message with driver details in a short while.";
                $message=urlencode($message);
                $uri = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$user_mobile."&text=".$message."";
                $filename=curl_init();
                curl_setopt($filename,CURLOPT_URL, $uri );
                curl_setopt($filename, CURLOPT_HEADER, 0);
                curl_exec($filename);
                curl_close($filename);
                $ServiceProviderMobile=array();            
                $stmt10 = $this->conn->prepare("SELECT mobile from `service_provider` where id != 0");
                if ($stmt10->execute()) {
                    $res = array();
                    $stmt10->bind_result($mobile);
                    while($stmt10->fetch()){
                        $res["mobile"] = $mobile;
                        $merchant_mobile = $mobile;   
                        $message2="You have received a new request from ".$user_name." with ".$user_mobile." at ".$addressline0.", ".$addressline1.", ".$city.".";
                        $message2=urlencode($message2);
                        $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$merchant_mobile."&text=".$message2."";
                        $filename2=curl_init();
                        curl_setopt($filename2,CURLOPT_URL, $uri2 );
                        curl_setopt($filename2, CURLOPT_HEADER, 0);
                        curl_exec($filename2);
                        curl_close($filename2);
                    }
                    $stmt10->close();
                    array_push($ServiceProviderMobile,$res); 
                }
                $response["status"] = $status;
                $response["request_id"] = $request_id2;
                return $response;

/*
                    
                $latLng=$latitude.",".$longitude;
                $getUserLocality=getZoneByLatLng($latLng);
                $sublocality_level=$getUserLocality['sublocality_level'];
                $zone=$getUserLocality['zone'];
                $locality2=$getUserLocality['locality']; 
                $locality2=explode(" ", $locality2);
                $locality2=array_reverse($locality2);
                $locality2=$locality2[0];
                $ServiceProviderMobile=array();
                if($locality2=="Delhi") {
                    $zone=explode(" ", $zone);
                    $zone=array_reverse($zone);
                    $zone=$zone[1]." ".$zone[0];  
//                    $zoneId= getZoneIdByZone($zone);  

                    $stmt6 = $this->conn->prepare("SELECT id FROM service_region WHERE region_name = ?");
                    $stmt6->bind_param("s", $zone);
                    if ($stmt6->execute()) {
                        $stmt6->bind_result($zoneId);
                        $stmt6->fetch();
                        $stmt6->close();
                    }
                   
                    if($zoneId!=NULL) {    
          
                        $stmt = $this->conn->prepare("SELECT t1.id as region_id, t5.mobile, t2.service_provider_id, t3.tagged_area_id, t4.area_name, t2. service_provider_region_id from service_region t1
                            INNER JOIN service_provider_regions t2 ON t2.region_id=t1.id 
                            INNER JOIN service_provider_regions_arae_covered t3 ON t2.service_provider_region_id=t3.service_provider_region_id 
                            INNER JOIN service_provider_regions_areas t4 ON t3.tagged_area_id=t4.id 
                            INNER JOIN service_provider t5 ON t2.service_provider_id=t5.id 
                            WHERE t1.id= ? AND t4.area_name= ?");
                        $stmt->bind_param("ss", $zoneId, $sublocality_level);
                        $stmt->execute();
                        $stmt->store_result();
                        $result = $stmt->get_result();
                        $num_rows = $stmt->num_rows;
                        $stmt->close();
                        if($num_rows>0) {
                            array_push($ServiceProviderMobile,$result["mobile"]); 
                        } else {
                            $stmt1 = $this->conn->prepare("SELECT t1.id as region_id, t3.mobile, t2.service_provider_id from service_region t1
                                INNER JOIN service_provider_regions t2 ON t2.region_id=t1.id 
                                INNER JOIN service_provider t3 ON t2.service_provider_id=t3.id 
                                WHERE t1.id= ?");
                            $stmt1->bind_param("s", $zoneId);
                            $stmt1->execute();
                            $stmt1->store_result();
                            $result1 = $stmt1->get_result();
                            $num_rows1 = $stmt1->num_rows;
                            $stmt1->close();
                            if($num_rows1>0) {
                                array_push($ServiceProviderMobile,$result1["mobile"]); 
                            } else {
                                $stmt2 = $this->conn->prepare("SELECT t4.mobile from ` service_region_zones` t1 
                                    INNER JOIN service_region t2 ON t1.region_zone_id=t2.region_zone_id 
                                    INNER JOIN service_provider_regions t3 ON t2.id=t3.region_id 
                                    INNER JOIN service_provider t4 ON t3.service_provider_id=t4.id
                                    WHERE t1.name= ? GROUP BY t3.service_provider_id");
                                $stmt2->bind_param("s", $locality2);
                                $stmt2->execute();
                                $stmt2->store_result();
                                $result2 = $stmt2->get_result();
                                $num_rows2 = $stmt2->num_rows;
                                $stmt2->close();                                    
                                array_push($ServiceProviderMobile,$result2["mobile"]); 
                            }
                        }
                    } else {
                        $stmt3 = $this->conn->prepare("SELECT t4.mobile from ` service_region_zones` t1 
                            INNER JOIN service_region t2 ON t1.region_zone_id=t2.region_zone_id 
                            INNER JOIN service_provider_regions t3 ON t2.id=t3.region_id 
                            INNER JOIN service_provider t4 ON t3.service_provider_id=t4.id
                            WHERE t1.name= ? GROUP BY t3.service_provider_id");                            
                        $stmt3->bind_param("s", $locality2);        
                        $stmt3->execute();
                        $stmt3->store_result();
                        $result3 = $stmt3->get_result();
                        $num_rows3 = $stmt3->num_rows;
                        $stmt3->close();                                                                   
                        array_push($ServiceProviderMobile,$result3["mobile"]); 
                    }
                } else {
                    $stmt4 = $this->conn->prepare("SELECT t3.mobile as service_provider_mobile from service_region t1 
                        INNER JOIN service_provider_regions t2 ON t1.id=t2.region_id 
                        INNER JOIN service_provider t3 ON t3.id=t2.service_provider_id where t1.region_name=?");                            
                    $stmt4->bind_param("s", $locality);        
                    $stmt4->execute();
                    $stmt4->store_result();
                    $result4 = $stmt4->get_result();
                    $num_rows4 = $stmt4->num_rows;
                    $stmt4->close();                
                    array_push($ServiceProviderMobile,$result4["service_provider_mobile"]);                                                   
                }
*/                
            case 2 :
                $message3="User has cancelled the driver booking with Request ID ".$request_id2.". Kindly do not send the driver for service.";
                $message3=urlencode($message3);
                $uri3 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$service_provider_mobile."&text=".$message3."";
                $filename3=curl_init();
                curl_setopt($filename3,CURLOPT_URL, $uri3 );
                curl_setopt($filename3, CURLOPT_HEADER, 0);
                curl_exec($filename3);
                curl_close($filename3);

                $message4="Your booking with Request ID ".$request_id2." has been cancelled by the user ".$user_name."";
                $message4=urlencode($message4);
                $uri4 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$driver_mobile."&text=".$message4."";
                $filename4=curl_init();
                curl_setopt($filename4,CURLOPT_URL, $uri4 );
                curl_setopt($filename4, CURLOPT_HEADER, 0);
                curl_exec($filename4);
                curl_close($filename4);
                
                $latLng=$latitude.",".$longitude;
                $getUserLocality=getZoneByLatLng($latLng);
                $sublocality_level=$getUserLocality['sublocality_level'];
                $zone=$getUserLocality['zone'];
                $locality2=$getUserLocality['locality']; 
                $locality2=explode(" ", $locality2);
                $locality2=array_reverse($locality2);
                $locality2=$locality2[0];
                $ServiceProviderMobile=array();
                if($locality2=="Delhi") {
                    $zone=explode(" ", $zone);
                    $zone=array_reverse($zone);
                    $zone=$zone[1]." ".$zone[0];  
                    $zoneId= getZoneIdByZone($zone);  
                    if($zoneId!=NULL) {           
                        $sql=mysql_query("SELECT t1.id as region_id, t5.mobile, t2.service_provider_id, t3.tagged_area_id, t4.area_name, t2. service_provider_region_id from service_region t1
                            INNER JOIN service_provider_regions t2 ON t2.region_id=t1.id 
                            INNER JOIN service_provider_regions_arae_covered t3 ON t2.service_provider_region_id=t3.service_provider_region_id 
                            INNER JOIN service_provider_regions_areas t4 ON t3.tagged_area_id=t4.id 
                            INNER JOIN service_provider t5 ON t2.service_provider_id=t5.id 
                            WHERE t1.id=$zoneId AND t4.area_name='$sublocality_level'");
                        $count=mysql_num_rows($sql);
                        if($count>0) {
                            while($row=mysql_fetch_array($sql)) {
                                array_push($ServiceProviderMobile,$row['mobile']);
                            }
                        } else {
                            $sql1=mysql_query("SELECT t1.id as region_id, t3.mobile, t2.service_provider_id from service_region t1
                                INNER JOIN service_provider_regions t2 ON t2.region_id=t1.id 
                                INNER JOIN service_provider t3 ON t2.service_provider_id=t3.id 
                                WHERE t1.id=$zoneId");
                            $count=mysql_num_rows($sql1);
                            if($count>0) {
                                while($row1=mysql_fetch_array($sql1)) {              
                                    array_push($ServiceProviderMobile,$row1['mobile']);
                                }
                            } else {
                                $qry=mysql_query("SELECT t4.mobile from ` service_region_zones` t1                                         
                                    INNER JOIN service_region t2 ON t1.region_zone_id=t2.region_zone_id 
                                    INNER JOIN service_provider_regions t3 ON t2.id=t3.region_id 
                                    INNER JOIN service_provider t4 ON t3.service_provider_id=t4.id
                                    WHERE t1.name='$locality2' GROUP BY t3.service_provider_id");
                                while($row=mysql_fetch_array($qry)) {
                                    array_push($ServiceProviderMobile,$row['mobile']);
                                }
                            }
                        }
                    } else {                            
                        $qry=mysql_query("SELECT t4.mobile from ` service_region_zones` t1 
                            INNER JOIN service_region t2 ON t1.region_zone_id=t2.region_zone_id 
                            INNER JOIN service_provider_regions t3 ON t2.id=t3.region_id 
                            INNER JOIN service_provider t4 ON t3.service_provider_id=t4.id
                            WHERE t1.name='$locality2' GROUP BY t3.service_provider_id");
                        while($row=mysql_fetch_array($qry)) {
                            array_push($ServiceProviderMobile,$row['mobile']);
                        }
                    }
                } else {
                    $qry=mysql_query("SELECT t3.mobile as service_provider_mobile from service_region t1 INNER JOIN service_provider_regions t2 ON t1.id=t2.region_id INNER JOIN service_provider t3 ON t3.id=t2.service_provider_id where t1.region_name='$locality'");
                    while($fetch=mysql_fetch_array($qry)) {
                        $mobileNumber=$fetch['service_provider_mobile'];
                        array_push($ServiceProviderMobile,$mobileNumber);
                    }
                }
                foreach($ServiceProviderMobile as $mobile) {
                    $merchant_mobile = $mobile;          
//                        $message2="A new request for driver from ".$locality." has been received. Kindly login to Accept or Decline the request.";
                    $message2="You have received a new request from ".$user_name." with ".$user_mobile." at ".$addressline0.", ".$addressline1.", ".$city.".";
                    $message2=urlencode($message2);
                    $uri2 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$merchant_mobile."&text=".$message2."";
                    $filename2=curl_init();
                    curl_setopt($filename2,CURLOPT_URL, $uri2 );
                    curl_setopt($filename2, CURLOPT_HEADER, 0);
                    curl_exec($filename2);
                    curl_close($filename2);           
                }               
/*    
                    $result10 = mysql_query("select mobile from `service_provider`");
                    while($row = mysql_fetch_array($result10)) {
                        $merchant_mobile = (int) $row["mobile"];
                        $message5="A new request for driver from ".$addressline1." has been received. Kindly login to Accept or Decline the request.";
                        $message5=urlencode($message5);
                        $uri5 = "http://23.254.128.22:9080/urldreamclient/dreamurl?userName=".$userId."&password=".$password."&clientid=".$clientId."&to=".$merchant_mobile."&text=".$message5."";
                        $filename5=curl_init();
                        curl_setopt($filename5,CURLOPT_URL, $uri5 );
                        curl_setopt($filename5, CURLOPT_HEADER, 0);
                        curl_exec($filename5);
                        curl_close($filename5);          
                    }
*/              
                $response["status"] = $status;
                $response["request_id"] = $request_id2;
                return $response;
                break;

            case 3:
                $response["status"] = $status;
                $response["request_id"] = $request_id2;
                return $response;
                break;
        }
    }
}
?>