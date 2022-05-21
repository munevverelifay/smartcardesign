<?php

include 'mail.php';
$file='data.json';
$json = json_decode(file_get_contents($file), true);
$response=[];
$value=NULL;
if(isset($_REQUEST['value'])){
    $value=$_REQUEST['value'];
}
header('Content-Type: application/json; charset=utf-8');
$action=$_REQUEST['action'];
switch ($action) {
    case 'get_macro':
        if(!isset($json['macro'])){
            $json['macro']='';
        }
        $response['value']=$json['macro'];
        if($response['value']!='go_forward' && $response['value']!='go_backward'){
            $json['macro']='';
        }
        break;
    case 'get_role':
        if(!isset($json['role'])){
            $json['role']=false;
        }
        $response['value']=$json['role'];
        break;
    case 'set_ldr':
        $json['ldr']=filter_var($value, FILTER_VALIDATE_BOOLEAN);
        
        break;
    case 'set_ultra_sonic_1':
        if(!isset($json['ultra_sonic_1'])){
            $json['ultra_sonic_1']=[];
        }
        $json['ultra_sonic_1'][count($json['ultra_sonic_1'])]=intval($value);
        break;
    case 'set_ultra_sonic_2':
        if(!isset($json['ultra_sonic_2'])){
            $json['ultra_sonic_2']=[];
        }else{
            $json['ultra_sonic_2'][count($json['ultra_sonic_2'])]=intval($value);
            $status=false;
           if(isset($_REQUEST['mail'])){
             $mail=filter_var($_REQUEST['mail'], FILTER_VALIDATE_BOOLEAN);;
             if($mail){
                 $status=true;
             }
            }
            if(isset($json['emails']) && $status){
                foreach($json['emails'] as $email){
                    email_send($email,'Arabanın Yanında Biri Var');
                }
            }
        }
        
        break;
    default:
        break;
}
file_put_contents($file, json_encode($json));
echo json_encode($response);
die();
