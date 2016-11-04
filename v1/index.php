<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;
$merchant_id = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'cs_api_key' heder
 **/
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();




    // Verifying cs_api_key Header
    if (isset($headers['cs_api_key']) && (isset($headers['merchant_login_key']) || isset($headers['user_login_key']))) {
        $db = new DbHandler();
        // get the api key
        $api_key = $headers['cs_api_key'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } 

        if(isset($headers['merchant_login_key']) && $headers['merchant_login_key'] != NULL) {
            $login_key = $headers['merchant_login_key'];
            if (!$db->isValidServiceProviderLoginKey($login_key)) {
                // api key is not present in users table
                $response["error"] = true;
                $response["message"] = "Access Denied. Invalid Login key";
                echoRespnse(401, $response);
                $app->stop();
            } else {
                global $merchant_id;
                // get merchant primary key id
                $merchant_id = $db->getServiceProviderId($login_key);
            }
        }
        if(isset($headers['user_login_key']) && $headers['user_login_key'] != NULL) {
            $login_key = $headers['user_login_key'];
            if (!$db->isValidUserLoginKey($login_key)) {
                // api key is not present in users table
                $response["error"] = true;
                $response["message"] = "Access Denied. Invalid Login key";
                echoRespnse(401, $response);
                $app->stop();
            } else {
                global $user_id;
                // get merchant primary key id
                $user_id = $db->getUserId($login_key);
            }
        }
    //    if(isset($headers['user_login_key']))
    //        $login_key = $headers['user_login_key'];

    } else {
        // api key is missing in header
        if (!(isset($headers['cs_api_key']) || (isset($headers['merchant_login_key']) || isset($headers['user_login_key'])))) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Api key and Login key are missing";
            echoRespnse(400, $response);
            $app->stop();
        }
        else if(!isset($headers['cs_api_key'])) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Api key is missing";
            echoRespnse(400, $response);
            $app->stop();
        } else if(!(isset($headers['merchant_login_key']) || isset($headers['user_login_key']))) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Login key is missing";
            echoRespnse(400, $response);
            $app->stop();
        }
    }
}



/**
 * Merchant Login
 * url - /login/merchant
 * method - POST
 * params - mobile, password
 */
$app->post('/merchant/login', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('mobile', 'password'));

            // reading post params
            $mobile = $app->request()->post('mobile');
            $password = $app->request()->post('password');
            $response = array();

            $db = new DbHandler();
            // check for correct mobile and password
            if ($db->checkMerchantLogin2($mobile, $password)) {
                // get the merchant by mobile and password
                $merchant = $db->getMerchantByMobileAndPassword($mobile, $password);

                if ($merchant != NULL) {
                    $response["error"] = false;
                    $response['merchant_id'] = $merchant['id'];
                    $response['merchant_login_key'] = $merchant['login_key'];
                    $response['status'] = 1;
                    $response['message'] = "Login Successfull";
                } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['merchant_id'] = 0;
                    $response['merchant_login_key'] = "";
                    $response['status'] = 0;
                    $response['message'] = "An error occurred. Please try again";
                }
  
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['merchant_id'] = 0;
                $response['merchant_login_key'] = "";
                $response['status'] = 0;
                $response['message'] = 'Login failed. Incorrect credentials';
            }

            echoRespnse(200, $response);
        });


/**
 * Listing all requests of particual merchant
 * method GET
 * url /requests/merchant          
 */
$app->get('/merchant/requests', 'authenticate', function() {
            global $merchant_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant requests
            $result = $db->getAllServiceProviderRequests($merchant_id);

            $response["error"] = false;
            $response['message'] = "Requests fetched successfully";
            $response["requests"] = array();

            // looping through result and preparing requests array
            while ($requests = $result->fetch_assoc()) {
                $tmp = array();
                $timetemp = $requests["service_time"];
                $time22 = explode(":", $timetemp);
                $tmp["time"] = $time22[0].":".$time22[1];
                $datetemp = $requests["service_date"];
                $date22 = explode("-", $datetemp);
                $tmp["date"] = $date22[2]."/".$date22[1]."/".$date22[0];
                $tmp["cartype"] = $requests["cartype"];
                $tmp["status"] = $requests["status"];
                $tmp["req_id"] = $requests["id"];
                $tmp["latitude"] = $requests['latitude'];
                $tmp["longitude"] = $requests['longitude'];
                $tmp["addressline0"] = $requests['addressline0'];
                $tmp["addressline1"] = $requests['addressline1'];
                $tmp["addressline2"] = $requests['addressline2'];
                $tmp["addressline3"] = $requests['addressline3'];
                $tmp["locality"] = $requests['locality'];
                $tmp["city"] = $requests['city'];
                $tmp["state"] = $requests['state'];
                $tmp["country"] = $requests['country'];
                $tmp["pincode"] = $requests['pincode'];

                array_push($response["requests"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Listing single request of particual merchant
 * method GET
 * url /merchant/requests/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/merchant/requests/:id', 'authenticate', function($request_id) {
            global $merchant_id;
            $response = array();
            $db = new DbHandler();

            // fetch request
            $result = $db->getServiceProviderRequest($merchant_id, $request_id);
            
            $response["error"] = false;
            $response['message'] = "Request fetched successfully";
            $response["requests"] = array();

            // looping through result and preparing requests array
            while ($requests = $result->fetch_assoc()) {
                $tmp = array();
                $timetemp = $requests["service_time"];
                $time22 = explode(":", $timetemp);
                $tmp["time"] = $time22[0].":".$time22[1];
                $datetemp = $requests["service_date"];
                $date22 = explode("-", $datetemp);
                $tmp["date"] = $date22[2]."/".$date22[1]."/".$date22[0];
                $tmp["cartype"] = $requests["cartype"];
                $tmp["status"] = $requests["status"];
                $tmp["req_id"] = $requests["id"];
                $tmp["latitude"] = $requests['latitude'];
                $tmp["longitude"] = $requests['longitude'];
                $tmp["addressline0"] = $requests['addressline0'];
                $tmp["addressline1"] = $requests['addressline1'];
                $tmp["addressline2"] = $requests['addressline2'];
                $tmp["addressline3"] = $requests['addressline3'];
                $tmp["locality"] = $requests['locality'];
                $tmp["city"] = $requests['city'];
                $tmp["state"] = $requests['state'];
                $tmp["country"] = $requests['country'];
                $tmp["pincode"] = $requests['pincode'];

                array_push($response["requests"], $tmp);
            }

            echoRespnse(200, $response);      
        });


/**
 * Add a new Driver in db
 * method POST
 * params - name, mobile
 * url - /drivers
 */
$app->post('/merchant/drivers', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('name', 'mobile'));

            $response = array();
            $name = $app->request->post('name');
            $mobile = $app->request->post('mobile');

            global $merchant_id;
            $db = new DbHandler();

            // creating new task
            $result = $db->addNewDriver($merchant_id, $name, $mobile);

            if ($result != NULL) {
                $response["error"] = false;
                $response["message"] = "Driver added successfully";
                $response["status"] = 1;
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to add driver. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }            
        });


/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/merchant/drivers', 'authenticate', function() {
            global $merchant_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getAllDrivers($merchant_id);

            $response["error"] = false;
            $response["message"] = "Drivers fetched successfully";
            $response["drivers"] = array();

            // looping through result and preparing tasks array
            while ($drivers = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $drivers["id"];
                $tmp["driver_name"] = $drivers["name"];
                $tmp["driver_mobile"] = $drivers["mobile"];
                array_push($response["drivers"], $tmp);
            }

            echoRespnse(200, $response);
        });


/**
 * Listing default driver of particual merchant
 * method GET
 * url /merchant/drivers/default          
 */
$app->get('/merchant/drivers/default', 'authenticate', function() {
            global $merchant_id;
            $response = array();
            $db = new DbHandler();

            // fetching default merchant drivers
            $result = $db->getDefaultDrivers($merchant_id);

            $response["error"] = false;
            $response["message"] = "Default Driver fetched successfully";
            $response["drivers"] = array();

            // looping through result and preparing tasks array
            while ($drivers = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $drivers["id"];
                $tmp["driver_name"] = $drivers["name"];
                $tmp["driver_mobile"] = $drivers["mobile"];
                array_push($response["drivers"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Listing all jobs of particular merchant
 * method GET
 * url /merchant/jobs          
 */
$app->get('/merchant/jobs', 'authenticate', function() {
            global $merchant_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getAllJobs($merchant_id);

            $response["error"] = false;
            $response["message"] = "Jobs fetched successfully";
            $response["jobs"] = array();

            // looping through result and preparing tasks array
            while ($jobs = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["request_id"] = $jobs["id"];
                $tmp["locality"] = $jobs["addressline1"];
                $datetemp = $jobs["service_date"];
                $date22 = explode("-", $datetemp);
                $tmp["date"] = $date22[2]."/".$date22[1]."/".$date22[0];
                $timetemp = $jobs["service_time"];
                $time22 = explode(":", $timetemp);
                $tmp["time"] = $time22[0].":".$time22[1];
                $tmp["cartype"] = $jobs["cartype"];
                $tmp["status"] = $jobs["status"];
                $tmp["driver_name"] = $jobs["name"];
                array_push($response["jobs"], $tmp);
            }

            echoRespnse(200, $response);
        });


/**
 * Removing driver. Merchant can delete only their driver
 * method DELETE
 * url /drivers/:id
 */
$app->delete('/merchant/drivers/:id', 'authenticate', function($driver_id) use($app) {
            global $merchant_id;

            $db = new DbHandler();
            $response = array();
            $result = $db->removeDriver($merchant_id, $driver_id);
            if ($result) {
                // driver deleted successfully
                $response["error"] = false;
                $response["message"] = "Driver removed succesfully";
                $response["status"] = 1;
            } else {
                // driver failed to delete
                $response["error"] = true;
                $response["message"] = "Driver failed to remove. Please try again!";
                $response["status"] = 0;
            }
            echoRespnse(200, $response);
        });

/**
 * Updating existing request
 * method PUT
 * params task, status
 * url - /tasks/:id
 */
$app->put('/merchant/requests/:id', 'authenticate', function($request_id) use($app) {
            // check for required params
            verifyRequiredParams(array('driver_id'));

            global $merchant_id;            
            $driver_id = $app->request->put('driver_id');
       
            $db = new DbHandler();
            $response = array();

            // updating driver
            $result = $db->editDriver($merchant_id, $request_id, $driver_id);
            if ($result == 1) {
                // driver updated successfully
                $response["error"] = false;
                $response["message"] = "Driver updated successfully";
                $response["status"] = 1;
            } else {
                // driver failed to update
                $response["error"] = true;
                $response["message"] = "Driver failed to update. Please try again!";
                $response["status"] = 0;
            }
            echoRespnse(200, $response);
        });

/**
 * Decline a request in db
 * method POST
 * params - decline_option, other_reason
 * url - /merchant/requests/decline/:id
 */
$app->post('/merchant/requests/decline/:id', 'authenticate', function($request_id) use ($app) {
            // check for required params
            verifyRequiredParams(array('decline_option', 'other_reason'));

            $response = array();
            $decline_option = $app->request->post('decline_option');
            $other_reason = $app->request->post('other_reason');
    
            global $merchant_id;
            $db = new DbHandler();

            // creating new task
            $result = $db->declineRequest($merchant_id, $request_id, $decline_option, $other_reason);

            if ($result != NULL) {
                $response["error"] = false;
                $response["message"] = "Request declined successfully";
                $response["status"] = 1;
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to decline request. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }            
        });


/**
 * Accept a request in db
 * method POST
 * params - decline_option, other_reason
 * url - /merchant/requests/decline/:id
 */
$app->post('/merchant/requests/accept/:id', 'authenticate', function($request_id) use ($app) {
            // check for required params
            verifyRequiredParams(array('driver_id'));

            $response = array();
            $driver_id = $app->request->post('driver_id');
    
            global $merchant_id;
            $db = new DbHandler();

            // creating new task
            $result = $db->acceptRequest($merchant_id, $request_id, $driver_id);

            if ($result == 1) {
                $response["error"] = false;
                $response["message"] = "Request accepted successfully";
                $response["status"] = 1;
                echoRespnse(201, $response);
            } else if ($result == 2) {
                $response["error"] = true;
                $response["message"] = "Sorry, this request is already taken";
                $response["status"] = 2;
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to accept request. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }            
        });





/* user starts */




/**
 * Generate OTP
 * method POST
 * params - name, mobile
 * url - /drivers
 */


$app->post('/user/otp', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('mobile'));

            $response = array();
            $mobile = $app->request->post('mobile');

            global $user_id;
            $db = new DbHandler();
            $otp = NULL;
            // creating new task
            $result = $db->generateOTP($mobile);
            
            $otp = $db->getOTP($mobile); 
            

            if ($otp != NULL) {
                $response["error"] = false;
                $response["message"] = "OTP generated successfully";
                $response["status"] = 1;
                $response["otp"] = $otp;
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to generate OTP. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }            
        });


/**
 * Check OTP
 * method POST
 * params - name, mobile
 * url - /drivers
 */


$app->post('/user/otp/check', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('mobile', 'otp'));

            $response = array();
            $mobile = $app->request->post('mobile');
            $otp = $app->request->post('otp');

            $db = new DbHandler();
            if (!$db->checkOTP($mobile, $otp)) {
                $response["error"] = true;
                $response["message"] = "Failed to match OTP. Please try again";
                $response["status"] = 0;
                $response["match"] = 0;
                echoRespnse(200, $response);
            } else {
                if (!$db->OTPUsed($mobile, $otp)) {
                    $response["error"] = false;
                    $response["message"] = "OTP matched successfully";
                    $response["status"] = 1;
                    $response["match"] = 1;
                    echoRespnse(201, $response);
                } else {
                    $response["error"] = true;
                    $response["message"] = "Failed to update is_used. Please try again";
                    $response["status"] = 2;
                    $response["match"] = 1;
                    echoRespnse(200, $response);
                }
            }    
        });


/**
 * Register User
 * method POST
 * params - name, mobile
 * url - /drivers
 */


$app->post('/user/register', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('user_name', 'user_mobile'));

            $response = array();
            $name = $app->request->post('user_name');
            $mobile = $app->request->post('user_mobile');
            if($app->request->post('user_email'))
                $email = $app->request->post('user_email');
            else
                $email = "";

            $db = new DbHandler();
            if (!$db->checkUser($mobile)) {
                //user not found
                if($db->insertUser($name, $mobile, $email)) {
                    $result = $db->getUser($mobile);
                    $response["error"] = false;
                    $response["message"] = "User inserted successfully";
                    $response["status"] = 1;
                    $response["users"] = array();
                   // looping through result and preparing tasks array
                    while ($user = $result->fetch_assoc()) {
                        $tmp = array();
                        $tmp["user_status"] = "new";
                        $tmp["user_id_main"] = $user["id"];
                        $tmp["user_login_key"] = $user["mobile"];
                        array_push($response["users"], $tmp);
                    }
                    echoRespnse(201, $response);
                } else {
                    $response["error"] = true;
                    $response["message"] = "Failed to insert user. Please try again";
                    $response["status"] = 0;
                    echoRespnse(200, $response);
                }
            } else {
                $result = $db->getUser($mobile);
                if($result) {
                    $result2 = $db->updateUser($name, $mobile, $email);
                    if(!$result2) {
                        $response["error"] = false;
                        $response["message"] = "User fetched successfully";
                        $response["status"] = 1;
                        $response["users"] = array();
                        // looping through result and preparing tasks array
                        while ($user = $result->fetch_assoc()) {
                            $tmp = array();
                            $tmp["user_status"] = "existing";
                            $tmp["user_id_main"] = $user["id"];
                            $tmp["user_login_key"] = $user["mobile"];
                            array_push($response["users"], $tmp);
                        }
                        echoRespnse(200, $response);
                    } else {
                        $response["error"] = true;
                        $response["message"] = "Failed to fetch user. Please try again";
                        $response["status"] = 0;
                        echoRespnse(200, $response);
                    }   
                } else {
                        $response["error"] = true;
                        $response["message"] = "Failed to update user. Please try again";
                        $response["status"] = 0;
                        echoRespnse(200, $response);
                }       
            }    
        });


/**
 * Add a new Driver in db
 * method POST
 * params - name, mobile
 * url - /drivers
 */
$app->post('/user/addresses', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('addressname', 'latitude', 'longitude', 'addressline0', 'addressline1', 'addressline2', 'addressline3', 'locality', 'city', 'state', 'country', 'pincode'));

            $response = array();
            $addressname = $app->request->post('addressname');
            $latitude = $app->request->post('latitude');
            $longitude = $app->request->post('longitude');
            $addressline0 = $app->request->post('addressline0');
            $addressline1 = $app->request->post('addressline1');
            $addressline2 = $app->request->post('addressline2');
            $addressline3 = $app->request->post('addressline3');
            $locality = $app->request->post('locality');
            $city = $app->request->post('city');
            $state = $app->request->post('state');
            $country = $app->request->post('country');
            $pincode = $app->request->post('pincode');

            global $user_id;
            $db = new DbHandler();
            $exist = $db->favouriteExist($user_id, $addressname, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode);
            if(!$exist) {
                $result = $db->addNewFavourite($user_id, $addressname, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode);
                if ($result != NULL) {
                    $response["error"] = false;
                    $response["message"] = "Favourite added successfully";
                    $response["status"] = 1;
                    echoRespnse(201, $response);
                } else {
                    $response["error"] = true;
                    $response["message"] = "Failed to add favourite. Please try again";
                    $response["status"] = 2;
                    echoRespnse(200, $response);
                }            
            } else {
                $response["error"] = true;
                $response["message"] = "Address already added to the favourites";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Check OTP
 * method POST
 * params - name, mobile
 * url - /drivers
 */


$app->post('/user/rating', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('request_id', 'rating'));

            $response = array();
            $request_id = $app->request->post('request_id');
            $rating = $app->request->post('rating');
            $remark = $app->request->post('remark');

            $db = new DbHandler();
            if (!$db->submitRating($request_id, $rating, $remark)) {
                $response["error"] = true;
                $response["message"] = "Failed to submit rating. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            } else {
                $response["error"] = false;
                $response["message"] = "Rating submitted successfully";
                $response["status"] = 1;
                echoRespnse(201, $response);
            }    
        });

/**
 * Accept a request in db
 * method POST
 * params - decline_option, other_reason
 * url - /merchant/requests/decline/:id
 */
$app->post('/user/requests/', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('service_id', 'date', 'time', 'cartype', 'latitude', 'longitude', 'addressline0', 'addressline1', 'addressline2', 'addressline3', 'locality', 'city', 'state', 'country', 'pincode'));

            $response = array();
         
            $service_id = $app->request->post('service_id');
            $date2 = $app->request->post('date');
            $time = $app->request->post('time');
            $cartype = $app->request->post('cartype');
            $latitude = $app->request->post('latitude');
            $longitude = $app->request->post('longitude');
            $addressline0 = $app->request->post('addressline0');
            $addressline1 = $app->request->post('addressline1');
            $addressline2 = $app->request->post('addressline2');
            $addressline3 = $app->request->post('addressline3');
            $locality = $app->request->post('locality');
            $city = $app->request->post('city');
            $state = $app->request->post('state');
            $country = $app->request->post('country');
            $pincode = $app->request->post('pincode');
            
            $datetemp = explode("/", $date2);
            $date = $datetemp[2]."-".$datetemp[1]."-".$datetemp[0];   
            
            global $user_id;
            $db = new DbHandler();

           // creating new task
            $result = $db->submitRequest($user_id, $service_id, $date, $time, $cartype, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode);
            if ($result["status"] == 1) {
                $response["error"] = false;
                $response["message"] = "Request submitted successfully";
                $response["status"] = $result["status"];
                $response["request_id"] = $result["request_id"];        
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to submit request. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });

/**
 * Updating existing request
 * method PUT
 * params task, status
 * url - /tasks/:id
 */
$app->put('/user/requests/:id', 'authenticate', function($request_id) use($app) {
            // check for required params
            verifyRequiredParams(array('service_id', 'date', 'time', 'cartype', 'latitude', 'longitude', 'addressline0', 'addressline1', 'addressline2', 'addressline3', 'locality', 'city', 'state', 'country', 'pincode'));
            $response = array();

            $service_id = $app->request->post('service_id');
            $date2 = $app->request->post('date');
            $time = $app->request->post('time');
            $cartype = $app->request->post('cartype');
            $latitude = $app->request->post('latitude');
            $longitude = $app->request->post('longitude');
            $addressline0 = $app->request->post('addressline0');
            $addressline1 = $app->request->post('addressline1');
            $addressline2 = $app->request->post('addressline2');
            $addressline3 = $app->request->post('addressline3');
            $locality = $app->request->post('locality');
            $city = $app->request->post('city');
            $state = $app->request->post('state');
            $country = $app->request->post('country');
            $pincode = $app->request->post('pincode');
            
            $datetemp = explode("/", $date2);
            $date = $datetemp[2]."-".$datetemp[1]."-".$datetemp[0];   
            
            global $user_id;            
            $db = new DbHandler();
           
           // creating new task
            $result = $db->updateRequest($user_id, $request_id, $service_id, $date, $time, $cartype, $latitude, $longitude, $addressline0, $addressline1, $addressline2, $addressline3, $locality, $city, $state, $country, $pincode);
            if ($result["status"] == 1) {
                $response["error"] = false;
                $response["message"] = "Request updated successfully";
                $response["status"] = $result["status"];
                $response["request_id"] = $result["request_id"];        
                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to update request. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });



/**
 * Listing single request of particual merchant
 * method GET
 * url /merchant/requests/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/user/terms/:id', 'authenticate', function($service_id) {
            $response = array();
            $db = new DbHandler();
            // fetch request
            $result = $db->getTerms($service_id);
            if($result) {
            $response["error"] = false;
            $response['message'] = "Terms fetched successfully";
            $response["status"] = 1;
            $response["terms"] = array();
            // looping through result and preparing requests array
            while ($terms = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["term"] = $terms["terms"];
                array_push($response["terms"], $tmp);
            }
            echoRespnse(200, $response);    
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch terms. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing single request of particual merchant
 * method GET
 * url /merchant/requests/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/user/pricing/:id', 'authenticate', function($service_id) {
            $response = array();
            $db = new DbHandler();

            // fetch request
            $result = $db->getPricing($service_id);
            if($result) {
                $response["error"] = false;
                $response['message'] = "Pricing fetched successfully";
                $response["status"] = 1;
                $response["pricing"] = array();
                // looping through result and preparing requests array
                while ($price = $result->fetch_assoc()) {
                    $tmp = array();
                    $tmp["price"] = $price["pricing"];
                    array_push($response["pricing"], $tmp);
                }
                echoRespnse(200, $response); 
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch pricing. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });

/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/user/addresses', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getFavouriteAddress($user_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Addresses fetched successfully";
                $response["status"] = 1;
                $response["addresses"] = array();
                // looping through result and preparing tasks array
                while ($address = $result->fetch_assoc()) {
                    $tmp = array();
                    $tmp["address_id"] = $address["id"];
                    $tmp["addressname"] = $address["addressname"];
                    $tmp["user_id"] = $address["user_id"];
                    $tmp["latitude"] = $address["latitude"];
                    $tmp["longitude"] = $address["longitude"];
                    $tmp["addressline0"] = $address["addressline0"];
                    $tmp["addressline1"] = $address["addressline1"];
                    $tmp["addressline2"] = $address["addressline2"];
                    $tmp["addressline3"] = $address["addressline3"];
                    $tmp["locality"] = $address["locality"];
                    $tmp["city"] = $address["city"];
                    $tmp["state"] = $address["state"];
                    $tmp["pincode"] = $address["pincode"];
                    $tmp["country"] = $address["country"];
                    array_push($response["addresses"], $tmp);
                }
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch addresses Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/user/requests', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getAllUserRequests($user_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Requests fetched successfully";
                $response["status"] = 1;
                $response["requests"] = array();
                // looping through result and preparing tasks array
                while ($request = $result->fetch_assoc()) {
                    $tmp = array();
                    $tmp["request_id"] = $request["id"];
                    $datetemp = $request["service_date"];
                    $date22 = explode("-", $datetemp);
                    $tmp["date"] = $date22[2]."/".$date22[1]."/".$date22[0];
                    $timetemp = $request["service_time"];
                    $time22 = explode(":", $timetemp);
                    $tmp["time"] = $time22[0].":".$time22[1];
                    $tmp["status"] = $request["status"];
                    $tmp["merchant_id"] = $request["service_provider_id"];
                    $tmp["driver_id"] = $request["employee_id"];
                    $tmp["driver_name"] = $request["name"];
                    $tmp["driver_mobile"] = $request["mobile"];
                    array_push($response["requests"], $tmp);
                }
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch requests Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing single request of particual merchant
 * method GET
 * url /merchant/requests/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/user/requests/:id', 'authenticate', function($request_id) {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetch request
            $result = $db->getUserRequest($user_id, $request_id);
            if($result){
                $response["error"] = false;
                $response['message'] = "Request fetched successfully";
                $response["status"] = 1;   
                while ($request = $result->fetch_assoc()) {
                    $response["request_id"] = $request["id"];
                    $timetemp = $request["service_time"];
                    $time22 = explode(":", $timetemp);
                    $response["time"] = $time22[0].":".$time22[1];
                    $datetemp = $request["service_date"];
                    $date22 = explode("-", $datetemp);
                    $response["date"] = $date22[2]."/".$date22[1]."/".$date22[0];
                    $response["cartype"] = $request["cartype"];
                    $response["latitude"] = $request["latitude"];
                    $response["longitude"] = $request["longitude"];
                }
                echoRespnse(200, $response);      
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch requests Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/user/price/drivers', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getDriverPrice($user_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Driver price fetched successfully";
                $response["status"] = 1;
                // looping through result and preparing tasks array
                while ($driver_price = $result->fetch_assoc()) {
                    $response["fixed_hour"] = $driver_price["fixed_hour"];
                    $response["flat_rate"] = $driver_price["flat_rate"];
                    $response["hour_charges"] = $driver_price["hour_charges"];
                    $response["day_charges"] = $driver_price["day_charges"];
                }
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch driver price Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/user/areas', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getAreaServiced($user_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Area serviced fetched successfully";
                $response["status"] = 1;
                // looping through result and preparing tasks array
                while ($area_serviced = $result->fetch_assoc()) {
                    $response["latitude"] = $area_serviced["latitude"];
                    $response["longitude"] = $area_serviced["longitude"];
                    $response["radius"] = $area_serviced["radius"];
                }
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch area serviced Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });


/**
 * Listing all driver of particual merchant
 * method GET
 * url /merchant/drivers          
 */
$app->get('/user/rating', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all merchant drivers
            $result = $db->getIfRating($user_id);

            if ($result) {
                $response["error"] = false;
                $response["message"] = "Rating fetched successfully";
                $response["status"] = 1;
                $response["rating"] = array();
                // looping through result and preparing tasks array
                while ($request_id = $result->fetch_assoc()) {
                    $tmp = array();
                    $tmp["request_id"] = $request_id["id"];
                    array_push($response["rating"], $tmp);
                }
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to fetch rating. Please try again";
                $response["status"] = 0;
                echoRespnse(200, $response);
            }
        });



/**
 * Removing driver. Merchant can delete only their driver
 * method DELETE
 * url /drivers/:id
 */
$app->delete('/user/requests/:id', 'authenticate', function($request_id) use($app) {
            global $user_id;

            $db = new DbHandler();
            $response = array();
            $result = $db->removeRequest($user_id, $request_id);
            if ($result) {
                // driver deleted successfully
                $response["error"] = false;
                $response["message"] = "Request cancelled succesfully";
                $response["status"] = 1;
            } else {
                // driver failed to delete
                $response["error"] = true;
                $response["message"] = "Failed to cancel request. Please try again!";
                $response["status"] = 0;
            }
            echoRespnse(200, $response);
        });
















/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json

    $app->contentType('application/json');
    echo json_encode($response);
//    $app->contentType('text/xml');
//    echo xml_encode($response);
}

function xml_encode($mixed, $domElement=null, $DOMDocument=null) {
    if (is_null($DOMDocument)) {
        $DOMDocument =new DOMDocument;
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        echo $DOMDocument->saveXML();
    }
    else {
        // To cope with embedded objects 
        if (is_object($mixed)) {
          $mixed = get_object_vars($mixed);
        }
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    }
                    else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                xml_encode($mixedElement, $node, $DOMDocument);
            }
        }
        else {
            $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}
$app->run();
?>