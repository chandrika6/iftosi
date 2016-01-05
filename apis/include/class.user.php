<?php
        include APICLUDE.'common/db.class.php';
	class user extends DB
    {
		function __construct($db) 
                {
			parent::DB($db);
		}

        public function checkUser($params)
        {
            $csql="select * from tbl_registration where logmobile=".$params['mobile']."";
            $cres=$this->query($csql);
            $cnt1 = $this->numRows($cres);
            if($cnt1==0)
            {
                $arr='User Not yet Registered';
                $err=array('Code'=>0,'Msg'=>'No Data matched');
            }
            else 
            {
                while($row=$this->fetchData($cres))
                {
                    $arr2['userid']=$row['user_id'];
                    $isV=$row['is_vendor'];
                    if($isV == 1)
                    {
                        $uid = $arr2['userid'];
                    }
                    $arr2=$row;
                }
                if($isV == 1)
                {
                    $sql = "SELECT is_complete,business_type from tbl_vendor_master where vendor_id =\"".$uid."\"";
                    $res = $this->query($sql);
                    $cntres = $this->numRows();
                   if($cntres == 1 )
                   {
                       $row=$this->fetchData($res);
                       $arr2['isComp']= $row['is_complete'];
                       $arr2['busiType']= $row['business_type'];
                   }
                }
                $arr1=array();
                $arr=array('msg'=>$arr1,'userid'=>$arr2['user_id'],'userDet'=>$arr2);
                $err=array('Code'=>1,'Msg'=>'Data matched');
            }
            $result = array('results' => $arr, 'error' => $err);
            return $result;
        }

        
        public function validOTP($params) 
        {      
            $sql = "SELECT 
                                *,
                                DATE_SUB(`updated_on`,INTERVAL - 10 MINUTE) as intervl,
                                now()
                    FROM 
                                tbl_verification_code
                    WHERE
                                mobile = " . $params['mobile'] . " 
                    AND
                                DATE_SUB(`updated_on`,INTERVAL - 10 MINUTE) > now() limit 1";
            $res = $this->query($sql);
            if($res)
            {
                $row = $this->fetchData($res);
                if ($row['vcode'] == $params['vc'])
                {
                    $vsql = "SELECT
                                        *
                             FROM 
                                        tbl_registration 
                             WHERE 
                                        logmobile=\"" . $params['mobile'] . "\"";

                    $vres = $this->query($vsql);
                    $cnt1 = $this->numRows($vres);
                    if ($cnt1 > 0)
                    {
                        $row = $this->fetchData($vres);
                        $send_otp_params = array('mobile' => $params['mobile']);
                        $userid = $row['userid'];
                        $arr = array('Msg' => 'Otp is validated successfully for existing user', 'userid' => $userid);
                        $err = array('Code' => 0, 'Msg' => 'Data matched');
                    }
                    else
                    {
                        $arr = array();
                        $err = array('Code' => 0, 'Msg' => 'Data matched');
                    }
                } 
                else 
                {
                    $arr = array();
                    $err = array('Code' => 1, 'Msg' => 'Otp validation failed');
                }
            }
            else
            {
                $arr = array();
                $err = array('Code' => 1, 'Msg' => 'Otp validation failed');
            }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }
    # OTP creation
    public function sendOTP($params)
    {
        global $comm;
        $isValidate = true;
        $sql = "SELECT
                        *,
                        DATE_SUB(`updated_on`,INTERVAL - 10 MINUTE) as intervl,
                        now()
                FROM
                        tbl_verification_code
                WHERE
                        mobile = " . $params['mb'] . "
                AND
                        DATE_SUB(`updated_on`,INTERVAL - 10 MINUTE) > now() limit 1";
        $res = $this->query($sql);
        if ($res)
        {
            $row = $this->fetchData($res);
            if ($row['vcode'])
            {
                $rno = $row['vcode'];
                $isValidate = false;
            }
        }
        if ($isValidate)
        {
            $rno = rand(100000, 999999);
            $sql = "INSERT
                    INTO 
                                tbl_verification_code (mobile,vcode) 
                    VALUES
                                (" . $params['mb'] . ",
                                 " . $rno . ")";
            $res = $this->query($sql);
        }
        if($rno)
        {
            $txt = 'Your OTP is ' . $rno;
            $url = str_replace('_MOBILE', $params['mb'], SMSAPI);
            $url = str_replace('_MESSAGE', urlencode($txt), $url);
            $res = $comm->executeCurl($url, true);
            if (!empty($res))
            {
                $result = array('result'=>'','code'=>1);
                return $result;
            }
            else
            {
                $result = array('result'=>'','code'=>0);
                return $result;
            }
        }
    }
    
        public function userReg($params) // USER LOGIN PROCESS
        {   
           $isql = "INSERT INTO 
						tbl_registration 
						(
							user_name,
							password,
							logmobile,
							email,
							is_vendor,
							is_active,
                                                        city,
							date_time,
							update_time,
							updated_by
						)
					VALUES
						(
							\"".$params['username']."\",
                                                    MD5(\"".$params['password']."\"),
							\"".$params['mobile']."\",
							\"".$params['email']."\",
							\"".$params['isvendor']."\",
                                                            1,
                                                        \"".$params['cityname']."\",
                                                            now(),
                                                            now(),
							\"".$params['username']."\"
						)
					";
            $ires=$this->query($isql);
            $uid=$this->lastInsertedId();
            
            if($params['isvendor']==1)
            {
            $isql= "INSERT
                    INTO
                                tbl_vendor_master
                               (vendor_id,
                                email,
                                date_time,
                                is_complete,
                                active_flag,
                                city)
                    VALUES
                             (".$uid.",
                             '".$params['email']."',
                                now(),
                                0,
                                0,
                            \"".$params['cityname']."\")";
            $res=$this->query($isql);
                if($res)
                {
                $arr="SignUp process Is Complete";
                $err=array('code'=>0,'msg'=>"Insert Operation Done");
				$this->sendMail($params);
                }
                else
                {       
                    $arr="Problem in SignUp";
                    $err=array('code'=>1,'msg'=>"Error in insert operation");
                }
            }
           else if($params['isvendor']==0)
            {
                $arr="SignUp process Is Complete";
                $err=array('code'=>0,'msg'=>"Insert Operation Done");
				$this->sendMail($params);
            }
            else
            {
                $arr=array();
                $err=array('code'=>1,'msg'=>'Error in insertion ');
            }
            $result = array('results' =>$arr, 'userid' => $uid,'error'=>$err);
            return $result;
        }
        
        public function udtProfile($params) // Update vendor details
        {
            $dt= json_decode($params['dt'],1);
            $detls  = $dt['result'];
            
            $sql="SELECT is_vendor,user_id from tbl_registration where user_id=".$detls['uid']."";
            $res=$this->query($sql);
            $row=$this->fetchData($res);
            $isv=$row['is_vendor'];
            $uid=$row['user_id'];

            $vstat  = "SELECT is_complete from tbl_vendor_master where vendor_id=\"".$uid."\"";
            $vres   = $this->query($vstat);
            $statrow= $this->fetchData($vres);
            $completeStat = $statrow['is_complete'];
          
            if($isv == 1)
            {
                $tmp_params = array('uid' => $detls['uid']);
                $vdtls = $this->viewAll($tmp_params);
                if(!empty($vdtls) && !empty($vdtls['results']))
                {
                    $vfulldtls = $vdtls['results'][1];
                    if(!empty($vfulldtls))
                    {
                        $vendor_id = $vfulldtls['vendor_id'];
                        $orgName = $vfulldtls['orgName'];
                        $address1 = $vfulldtls['address1'];
                        $area = $vfulldtls['area'];
                        $postal_code = $vfulldtls['postal_code'];
                        $city = $vfulldtls['city'];
                        $country = $vfulldtls['country'];
                        $state = $vfulldtls['state'];
                        $position = $vfulldtls['position'];
                        $contact_person = $vfulldtls['contact_person'];
                        $contact_mobile = $vfulldtls['contact_mobile'];
                        $email = $vfulldtls['email'];
                        $memship_Cert = $vfulldtls['memship_Cert'];
                        $vatno = $vfulldtls['vatno'];
                        $landline = $vfulldtls['landline'];
                        $banker = $vfulldtls['banker'];
                        $pancard = $vfulldtls['pancard'];
                        $business_type = $vfulldtls['business_type'];
                        $showroom_name = $vfulldtls['showroom_name'];

                        if(!empty($vendor_id) && !empty($orgName) && !empty($address1) && !empty($area) && !empty($postal_code) && !empty($city) && !empty($country) && !empty($state) && !empty($position) && !empty($contact_person) && !empty($contact_mobile) && !empty($email) && !empty($memship_Cert) && !empty($vatno) && !empty($landline) && !empty($banker) && !empty($pancard) && !empty($business_type) && !empty($showroom_name) && ($params['isC'] == 2 || $params['isC'] == '2' || $completeStat == 2 || $completeStat == '2'))
                        {
                                $params['isC'] = 2;
                        }
                        else
                        {
                                $params['isC'] = 0;
                        }
                    }
                }
            $vsql1='UPDATE tbl_registration SET ';
            $vsql1 .= " city = \"".$vfulldtls['city']."\"";
            
            $vsql='UPDATE tbl_vendor_master SET ';
            if (!empty($detls['orgname'])) {
                $vsql .= " orgName = '".$detls['orgname']."', ";
            }
            
            if (!empty($detls['fulladd'])) {
                $vsql .= " fulladdress = '".$detls['fulladd']."',";
            }
            if (!empty($detls['add1'])) {
                $vsql .= " address1 = '".$detls['add1']."',";
            }
            if (!empty($detls['area'])) {
                $vsql .= " area = '".$detls['area']."',";
            }
            if (!empty($detls['pincode'])) {
                $vsql .= " postal_code = '".$detls['pincode']."',";
            }
            if (!empty($detls['city'])) {
                $vsql .= " city = '".$detls['city']."',";
            }
            if (!empty($detls['country'])) {
                $vsql .= " country = '".$detls['country']."',";
            }
            if (!empty($detls['state'])) {
                $vsql .= " state = '".$detls['state']."',";
            }
            if (!empty($detls['tel'])) {
                $vsql .= " telephones = '".$detls['tel']."',";
            }
            if (!empty($detls['altmail'])) {
                $vsql .= " alt_email = '".$detls['altmail']."',";
            }
            if (!empty($detls['ofcity'])) {
                $vsql .= " officecity = '".$detls['ofcity']."',";
            }
            if (!empty($detls['ofcountry'])) {
                $vsql .= " officecountry = '".$detls['ofcountry']."',";
            }
            if (!empty($detls['cperson'])) {
                $vsql .= " contact_person = '".$detls['cperson']."',";
            }
            if (!empty($detls['position'])) {
                $vsql .= " position = '".$detls['position']."',";
            }
            if (!empty($detls['cmobile'])) {
                $vsql .= " contact_mobile = '".$detls['cmobile']."',";
            }
            if (!empty($detls['alt_cmobile'])) {
                $vsql .= " alt_cmobile = '".$detls['alt_cmobile']."',";
            }
            if (!empty($detls['email'])) {
                $vsql .= " email = '".$detls['email']."',";
            }
            if (!empty($detls['memcert'])) {
                $vsql .= " memship_Cert = '".$detls['memcert']."',";
            }
            if (!empty($detls['bdbc'])) {
                $vsql .= " bdbc = '".$detls['bdbc']."',";
            }
            if (!empty($detls['othdbaw'])) {
                $vsql .= " other_dbaw = '".$detls['othdbaw']."',";
            }
            if (!empty($detls['showroomname'])) {
                $vsql .= " showroom_name = '".$detls['showroomname']."',";
            }
            if (!empty($detls['showroomno'])) {
                $vsql .= " no_showrooms = '".$detls['showroomno']."',";
            }
            if (!empty($detls['vat'])) {
                $vsql .= " vatno = '".$detls['vat']."',";
            }
            if (!empty($detls['wbst'])) {
                $vsql .= " website = '".$detls['wbst']."',";
            }
            if (!empty($detls['landline'])) {
                $landline=str_replace(' ', '-', $detls['landline']);
                $vsql .= " landline = '".$landline."',";
            }
            if (!empty($detls['mdbw'])) {
                $vsql .= " mdbw = '".$detls['mdbw']."',";
            }
            if (!empty($detls['bul_mdbw'])) {
                $vsql .= " bullion_mdbw = '".$detls['bul_mdbw']."',";
            }
            if (!empty($detls['banker'])) {
                $vsql .= " banker = '".$detls['banker']."',";
            }
            if (!empty($detls['pan'])) {
                $vsql .= " pancard = '".$detls['pan']."',";
            }
            if (!empty($detls['tovr'])) {
                $vsql .= " turnover = '".$detls['tovr']."',";
            }
            if (!empty($detls['busiType'])) {
                $vsql .= " business_type = '".$detls['busiType']."',";
            }
            if (!empty($params['lat'])) {
                $vsql .= " lat = '".$params['lat']."',";
            }
            if (!empty($params['lng'])) {
                $vsql .= " lng = '".$params['lng']."',";
            }
            if (isset($params['isC'])) {
                $vsql .= " is_complete = ".$params['isC'].",";
            }
            $vsql.=" updatedby='vendor' WHERE vendor_id=".$uid."";
            $vsql1 .= " WHERE user_id=".$uid;
            $vres = $this->query($vsql);
            $vres2 = $this->query($vsql1);
            if ($vres && $vres2) {
                $arr = "Vendor table is updated";
                $err = array('code' => 0, 'msg' => 'Update operation is done successfully');
            } else {
                $arr = array();
                $err = array('code' => 1, 'msg' => 'Update operation unsuccessfull');
            }
        }
        else if($isv==0)
        {
             $vsql = "UPDATE tbl_registration 
                      SET 
                                            user_name='".$detls['username']."',
                                            email='".$detls['email']."',
                                            updatedby='".$detls['username']."',
                                            city = \"".$detls['city']."\"
                     WHERE 
                                            user_id=".$uid."";
             $vres=$this->query($vsql);
             if($vres)
             {
                $arr="User Profile is updated";
                $err=array('code'=>0,'msg'=>'Update operation is done successfully');
             }
             else
             {
                $arr=array();
                $err=array('code'=>1,'msg'=>'Update operation unsuccessfull');
             }
		}
		else 
		{
			$arr=array();
			$err=array('code'=>0,'msg'=>'Update operation unsuccessfull');
		}
          
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }
                
        public function logUser($params) // USER LOGIN CHECK
        {
            $vsql="SELECT 
                          user_id,
                          user_name,
                          logmobile,
                          password,
                          is_vendor,
                          pass_flag,
						  email
                   FROM 
                          tbl_registration
                   WHERE
                          ((logmobile=\"".$params['mobile']."\")
                              OR
                          (email =\"".$params['mobile']."\"))
                   AND 
                          password=MD5('".$params['password']."')
                   AND
                          is_active=1";
            $vres=$this->query($vsql);
            $cntres=$this->numRows($vres);
            if($cntres==1)
            {
                while($row=$this->fetchData($vres))
                {
                    $arr['uid']=$row['user_id'];
                    $arr['utype']=$row['is_vendor'];
                    $arr['username']=$row['user_name'];
                    $arr['mobile']=$row['logmobile'];
                    $arr['email']=$row['email'];
                    $arr['pass_flag']=$row['pass_flag'];
                }
                $ut=$arr['utype'];
                if($ut==0)
                {
                }
                else if($ut==1)
                {
                      $sql="SELECT 
                              business_type,
                              is_complete
                       FROM 
                              tbl_vendor_master
                       WHERE
                              vendor_id=".$arr['uid'];
                    $res=$this->query($sql);
                    if($this->numRows($res)==1)
                    {
                        while($vrow=$this->fetchData($res))
                        {
                            $arr['busiType']=$vrow['business_type'];
                            $arr['isC']=$vrow['is_complete'];
                        }
                    }
                }
                $err=array('code'=>0,'msg'=>'Parameters matched');
            }
            else if($cntres > 1)
            {
                while($row=$this->fetchData($vres))
                {
                    $allRows[] = $row;
                    if($row['is_vendor'] == 2)
                    {
                        $arr['uid']=$row['user_id'];
                        $arr['utype']=$row['is_vendor'];
                        $arr['username']=$row['user_name'];
                        $arr['mobile']=$row['logmobile'];
                        $arr['email']=$row['email'];
                        $arr['pass_flag']=$row['pass_flag'];
                        break;
                    }
                    else if($row['is_vendor'] == 1)
                    {
                        $arr['uid']=$row['user_id'];
                        $arr['utype']=$row['is_vendor'];
                        $arr['username']=$row['user_name'];
                        $arr['mobile']=$row['logmobile'];
                        $arr['email']=$row['email'];
                        $arr['pass_flag']=$row['pass_flag'];
                        break;
                    }
                }
                if(empty($arr['uid']))
                {
                    $arr['uid']=$allRows[0]['user_id'];
                    $arr['utype']=$allRows[0]['is_vendor'];
                    $arr['username']=$allRows[0]['user_name'];
                    $arr['mobile']=$allRows[0]['logmobile'];
                    $arr['email']=$allRows[0]['email'];
                    $arr['pass_flag']=$allRows[0]['pass_flag'];
                }

                if($arr['utype'] == 1)
                {
                      $sql="SELECT 
                              business_type,
                              is_complete
                       FROM 
                              tbl_vendor_master
                       WHERE
                              vendor_id=".$arr['uid'];
                    $res=$this->query($sql);
                    if($this->numRows($res)==1)
                    {
                        while($vrow=$this->fetchData($res))
                        {
                            $arr['busiType']=$vrow['business_type'];
                            $arr['isC']=$vrow['is_complete'];
                        }
                    }
                }
               $err=array('code'=>0,'msg'=>'Details Fetched successfully');
            }
            else
            {
                $arr = array();
                $err=array('code'=>1,'msg'=>'Error in fetching data');
            }
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }
                
        public function actUser($params) // Activate Status
        {   
            $vsql="SELECT
                                active_flag 
                   FROM 
                                tbl_vendor_master  
                   WHERE 
                                vendor_id=".$params['userid']."";
            $vres=$this->query($vsql);
            if($this->numRows($vres)==1) //If user is registered
            {
                $usql="UPDATE
                                    tbl_vendor_master
                       SET
                                    active_flag=\"".$params['active_flag']."\" 
                       WHERE 
                                    vendor_id=".$params['userid'];
                $usql1="UPDATE
                                    tbl_registration
                       SET
                                    is_active=\"".$params['active_flag']."\" 
                       WHERE 
                                    user_id=".$params['userid'];
                $ures=$this->query($usql);
                $ures1=$this->query($usql1);
                if($ures)
                {
                    $arr="User profile is activated";
                    $err=array('code'=>0,'msg'=>'Value has been changed');
                }
                else
                {
                    $arr=array();
                    $err=array('code'=>1,'msg'=>'Problem in deactivating status');
                }
            }
            else
            {
                $arr=array();
                $err=array('code'=>1,'msg'=>'No such user');
            }
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }
        
        public function activateVendor($params) // Activate Status
        {   
            $vsql="SELECT
                                active_flag 
                   FROM 
                                tbl_vendor_master  
                   WHERE 
                                vendor_id=".$params['user_id']."";
            $vres=$this->query($vsql);
            $row =$this->fetchData($vres);
            if($this->numRows($vres) == 1) //If user is registered
            {
                $flag=$row['active_flag'];
                $arr['flag']=$flag;
                $err=array('code'=>0,'msg'=>'vendor profile status retrieved');
            }
            else
            {
                    $arr=array();
                    $err=array('code'=>1,'msg'=>'Problem in fetching status');
            }
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }

        public function deactUser($params) // DeActivate Status
        {   
            $vsql="SELECT 
                                active_flag 
                   FROM 
                                tbl_vendor_master 
                   WHERE 
                                vendor_id=".$params['userid']."";
            $vres=$this->query($vsql);
            if($this->numRows($vres)==1) //If user is registered
            {
            $usql="UPDATE 
                                tbl_registration 
                   SET 
                                is_active=0 
                   WHERE 
                                vendor_id=".$params['userid'];
            $ures=$this->query($usql);
                if($ures)
                {
                    $arr="User profile is deactivated";
                    $err=array('code'=>0,'msg'=>'Row has been Updated');
                }
                else
                {
                    $arr=array();
                    $err=array('code'=>1,'msg'=>'Error in updating data');
                }
            }
            else
            {
                $arr=array();
                $err=array('code'=>1,'msg'=>'Problem in fetching data');
            }  // If user is not registered
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }

        public function updatePass($params)
        {
          $vsql="SELECT
                            logmobile,
                            user_name,
                            email 
                 FROM 
                            tbl_registration 
                 WHERE 
                            logmobile=".$params['mobile']."
                  AND 
                            is_active <> 2";
            $vres=$this->query($vsql);
            if($this->numRows($vres)==1) //If user is registered
            {
               $usql="UPDATE
                                tbl_registration 
                      SET 
                                password=MD5('".$params['password']."')
                      WHERE 
                                logmobile=".$params['mobile'];
                $ures=$this->query($usql);
                if($ures)
                {
                    $arr="User profile password is updated";
                    $err=array('code'=>0,'msg'=>'Row has been Updated');
                }
                else
                {
                $arr=array();
                $err=array('code'=>1,'msg'=>'Error in updating data');
                }
            }
            else
            {
                $arr=array();
                $err=array('code'=>1,'msg'=>'Problem in fetching data');
            }  // If user is not registered
            $result = array('results'=>$arr,'error'=>$err);
            return $result;
        }
        

    public function viewAll($params) {
        $vsql = "SELECT 
                                    is_vendor,
                                    user_id,
                                    user_name,
                                    logmobile,
                                    email 
                   FROM 
                                    tbl_registration 
                   WHERE 
                                    user_id=" . $params['uid'] . " 
                   AND
                                    is_active <> 2";
        $vres = $this->query($vsql);
        $chkres = $this->numRows($vres);
        if ($chkres > 0)
        {//If user is registered and is customer
            while ($row1 = $this->fetchData($vres)) 
            {
                $arr1['isv'] = $row1['is_vendor'];
                $arr1['uid'] = $row1['user_id'];
                $arr[] = $row1;
                $err = array('code' => 0, 'msg' => 'Values fetched');
            }

            if ($arr1['isv'] == 1)
            {    // check if it is Vendor
                $vensql = "SELECT * FROM tbl_vendor_master WHERE vendor_id =" . $arr1['uid'];
                $res = $this->query($vensql);
                while ($row = $this->fetchData($res))
                {
                    //$arr[] = $row;
                    array_push($arr, $row);
                }
                $err = array('code' => 0, 'msg' => 'Data fetched successfully');
            }
        } 
        else 
        {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Problem in fetching data');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }
    
    public function profileComplete($params)
   {
        $sql="SELECT 
                              business_type,
                              is_complete
                       FROM 
                              tbl_vendor_master
                       WHERE
                              vendor_id=".$params['uid'];
        $res=$this->query($sql);
        if($this->numRows($res)==1)
        {
            while($vrow=$this->fetchData($res))
            {
                $arr['busiType']=$vrow['business_type'];
                $arr['isC']=$vrow['is_complete'];
            }
            $err=array('code'=>0,'msg'=>'data fetched');
        }
        else
        {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Problem in fetching data');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result['results'];
    }

	public function generatePassword($params)
	{
		$pwdLen = $params['length'];
		$pwd = '';
		if(empty($pwdLen))
		{
			$pwdLen = 6;
		}

		$i = 0;
		while($i < $pwdLen)
		{
			$pwd .= mt_rand(1, 9);
			$i++;
		}

		return $pwd;
	}
        
        public function forgotPwd($params) {
            $vsql = "SELECT email FROM tbl_registration WHERE logmobile=\"" . $params['email'] . "\"";
            $vres = $this->query($vsql);
            $row = $this->fetchData($vres);
            $cnt1 = $this->numRows($vres);

            if ($cnt1 > 0) {
                $password = mt_rand(111111, 999999);
                $vsql1 = "UPDATE tbl_registration SET password=MD5(\"".$password."\"), pass_flag=1 WHERE logmobile=\"" . $params['email'] . "\"";

                $vres1 = $this->query($vsql1,1);
                if ($vres1) {
                    $subject = 'Your Password Changed';
                    $message = 'Your password was successfully Changed. Your new password is ' . $password;

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: <info@iftosi.com>' . "\r\n";
                    
                    $mail = mail($row['email'], $subject, $message, $headers);
                    if ($mail) {
                        $arr = array();
                        $err = array('Code' => 0, 'Msg' => 'Email sent with the password');
                    } else {
                        $arr = array();
                        $err = array('Code' => 1, 'Msg' => 'Mail not Sent');
                    }
                } else {
                    $arr = array();
                    $err = array('Code' => 1, 'Msg' => 'Failed to Update Password');
                }
            } else {
                $arr = array();
                $err = array('Code' => 1, 'Msg' => 'Invalid Mobile Number');
            }
            $result = array('results' => $arr, 'error' => $err);
            return $result;
        }
        
        public function changePwd($params) {
            $vsql = "SELECT * FROM tbl_registration WHERE user_id='" . $params['uid'] . "' AND password=MD5('".$params['cpass']."') ";
            $vres = $this->query($vsql);
            $row = $this->fetchData($vres);
            $cnt1 = $this->numRows($vres);

            if ($cnt1 > 0) {
                $vsql1 = "UPDATE tbl_registration SET password=MD5('".$params['rpass']."'), pass_flag=0 WHERE user_id='" . $params['uid'] . "'";

                $vres1 = $this->query($vsql1);
                if ($vres1) {
                        $arr = array();
                        $err = array('Code' => 0, 'Msg' => 'Password Successfully Changed');
                    } else {
                        $arr = array();
                        $err = array('Code' => 1, 'Msg' => 'Password failed to change');
                    }
            } else {
                $arr = array();
                $err = array('Code' => 1, 'Msg' => 'Old Password Not Matching');
            }
            $result = array('results' => $arr, 'error' => $err);
            return $result;
        }

		private function sendMail($params)
		{
			$subject = 'Your Password for IFtoSI';
			$message = 'Dear ' . $params['username'] . ',';
			$message .= "\r\n";
			$message .= "Your new password is " . $params['password'];
			$message .= "\r\n \r\n";
			$message .= "Team IFtoSI";

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <info@iftosi.com>' . "\r\n";

			if(!empty($params['email']))
			{
				mail($params['email'], $subject, $message, $headers);
			}
		}

}
?>
