<?php
include 'mail.php';

$file='data.json';
$json = json_decode(file_get_contents($file), true);

if(isset($json['macro'])){
    switch ($json['macro']) {
        case 'stop':
            $json['macroStatus']='Durmuş';
            break;
        case 'turn_right':
            $json['macroStatus']='Sağa Dönüyor';
            break;
        case 'turn_left':
            $json['macroStatus']='Sola Dönüyor';
            break;
        case 'go_backward':
            $json['macroStatus']='Geri Gidiyor';
            break;
        case 'go_forward':
            $json['macroStatus']='İleri Gidiyor';
            break;
        default:
            $json['macroStatus']='Durmuş';
            break;
    }
}else{
    $json['macroStatus']='Durmuş';
}
if(isset($_REQUEST['action'])){
    
    $response=[];
    $value=NULL;
    if(isset($_REQUEST['value'])){
        $value=$_REQUEST['value'];
    }
    header('Content-Type: application/json; charset=utf-8');
    $action=$_REQUEST['action'];
    switch ($action) {
        case 'set_macro':
            $json['macro']=$value;
             file_put_contents($file, json_encode($json));
            break;
        case 'set_role':
            $json['role']=filter_var($value, FILTER_VALIDATE_BOOLEAN);
             file_put_contents($file, json_encode($json));
            break;
        case 'get_macro':
            if(!isset($json['macro'])){
                $json['macro']='';
            }
            $response['value']=$json['macro'];
            break;
        case 'get_role':
            if(!isset($json['role'])){
                $json['role']=false;
            }
            $response['value']=$json['role'];
            break;
        case 'get_ldr':
            if(!isset($json['ldr'])){
                $json['ldr']=false;
            }
            $response['value']=$json['ldr'];
            break;
        case 'get_ultra_sonic_1':
            if(!isset($json['ultra_sonic_1'])){
                //$json['ultra_sonic_1']=[];
            }
            $response['value']=$json['ultra_sonic_1'];
            break;
        case 'get_ultra_sonic_2':
            if(!isset($json['ultra_sonic_2'])){
                $json['ultra_sonic_2']=[];
            }
            $response['value']=$json['ultra_sonic_2'];
            break;
        case 'add_email':
            if(!isset($json['emails'])){
                $json['emails']=[];
            }
            $json['emails'][]=$value;
             file_put_contents($file, json_encode($json));
            break;
        case 'remove_email':
            if(isset($json['emails'])){
                if(count($json['emails'])>intval($value)){
                    unset($json['emails'][$value]);
                }
            }
             file_put_contents($file, json_encode($json));
            break;
        case 'send_email':
            if(isset($json['emails'])){
                foreach($json['emails'] as $email){
                    email_send($email,'Arabanın Yanında Biri Var');
                }
            }
            break;
        default:
            if(!isset($json['macro'])){
                $json['macro']='stop';
            }
            if(!isset($json['role'])){
                $json['role']=false;
            }
            if(!isset($json['ldr'])){
                $json['ldr']=false;
            }
            if(!isset($json['ultra_sonic_1'])){
                //$json['ultra_sonic_1']=[];
            }else{
                $json['ultra_sonic_1']=$json['ultra_sonic_1'][count($json['ultra_sonic_1'])-1];
            }
            if(!isset($json['ultra_sonic_2'])){
                $json['ultra_sonic_2']=[];
            }else{
                $json['ultra_sonic_2']=$json['ultra_sonic_2'][count($json['ultra_sonic_2'])-1];
            }
            if(!isset($json['emails'])){
                $json['emails']=[];
            }
            $response=$json;
            break;
        
    }
   
    echo json_encode($response);
    die();
}
?>
<html>
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row" style="width: 100%; height:100px;">
            <div class="col">

            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row"></div>
                <div class="row">
                    <div class="col">
                        <table class="table">
                        
                            <tbody>
                                <tr>
                                    
                                    <td>Ampul</td>
                                    <td id="table_role"><?= (isset($json['role']) && $json['role'])? 'Yanıyor':'Yanmıyor' ?></td>
                                </tr>
                                <tr>
                                    
                                    <td>Ortam</td>
                                    <td id="table_ldr"><?= (isset($json['ldr']) && $json['ldr'])? 'Ortam Aydınlık':'Ortam Aydınlık Değil' ?></td>
                                </tr>
                                <tr>
                                    
                                    <td>Araba Durum</td>
                                    <td id="table_macro"><?= (isset($json['macroStatus']) && $json['macroStatus'])? $json['macroStatus']:'Hareket Etmiyor' ?></td>
                                </tr>
                                <tr>
                                    
                                    <td>Ultra Sonic Sensor 1</td>
                                    <td id="table_ultra_sonic_1"><?= (isset($json['ultra_sonic_1']) && count($json['ultra_sonic_1'])>0)? $json['ultra_sonic_1'][count($json['ultra_sonic_1'])-1]:0 ?> cm</td>
                                </tr>
                            
                                <tr>
                                
                                    <td>Ultra Sonic Sensor 2</td>
                                    <td id="table_ultra_sonic_2"><?= (isset($json['ultra_sonic_2']) && count($json['ultra_sonic_2'])>0)? $json['ultra_sonic_2'][count($json['ultra_sonic_2'])-1]:0 ?> cm</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row"></div>
            </div>
            <div class="col">
                <div class="row"></div>
                <div class="row" >
                    <div class="col-4">
                        <p>Ampul:</p>
                        <button class="btn btn-primary" onclick="set_role('true')">Yak</button>
                        <button class="btn btn-primary" onclick="set_role('false')">Söndür</button>
                    </div>
                    <div class="col-8">
                        <div class="row" style="margin: 10px;">
                            <div class="col"></div>
                            <div class="col"><button class="btn btn-primary" onclick="set_macro('go_forward')">İleri</button></div>
                            <div class="col"></div>
                        </div>
                        <div class="row" style="margin: 10px;">
                            <div class="col"><button class="btn btn-primary" onclick="set_macro('turn_left')">Sola</button></div>
                            <div class="col"><button class="btn btn-primary" onclick="set_macro('stop')">Dur</button></div>
                            <div class="col"><button class="btn btn-primary" onclick="set_macro('turn_right')">Sağa</button></div>
                        </div>
                        <div class="row" style="margin: 10px;">
                            <div class="col"></div>
                            <div class="col"><button class="btn btn-primary" onclick="set_macro('go_backward')">Geri</button></div>
                            <div class="col"></div>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="row" style="margin-top: 100px;">
                            <div class="col">
                            <table class="table">
                                <tbody id="table_emails">
                                    <?php
                                    if(isset($json['emails'])){
                                        foreach($json['emails'] as $key=>$email){
                                    ?>
                                            <tr>
                                                <td><?= $email ?></td>
                                                <td><button data-id="<?= $key ?>" onclick="remove_email(this)" class="btn btn-primary"> X </button></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <input type="email" id="email_input" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email">
                                </div>
                                <button type="submit" onclick="add_email()" class="btn btn-primary">Ekle</button>
                            </div>
                            <button type="submit" onclick="test_send_email()" class="btn btn-primary">Test Email</button>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
       function set_role(status){
            $.get( "/", { action: "set_role" ,value:status=='true'} );
        }
        function set_macro(status){
            $.get( "/", { action: "set_macro" ,value:status} );
        }
        function add_email(){
            var email= $('#email_input').val();
            $.get( "/", { action: "add_email" ,value:email} );
        }
        function remove_email(self){
            var email_id= $(self).data('id');
            $.get( "/", { action: "remove_email" ,value:email_id} );
        }
        function test_send_email(){
            $.get( "/", { action: "send_email"} );
        }
    window.onload=function(){
        setInterval(function(){
            $.get( "/", { action: "list" } ).then((res)=>{
                $('#table_role').html((res['role'])? 'Yanıyor':'Yanmıyor');
                $('#table_ldr').html((res['ldr'])? 'Ortam Aydınlık':'Ortam Aydınlık Değil');
                $('#table_macro').html(res['macroStatus']);
                $('#table_ultra_sonic_1').html(res['ultra_sonic_1'] + ' cm');
                $('#table_ultra_sonic_2').html(res['ultra_sonic_2'] + ' cm');
                var emails='';
                for(var i=0;i<res.emails.length;i++){
                    emails +='<tr><td>'+res.emails[i]+'</td><td><button onclick="remove_email(this)" data-id="'+i+'" class="btn btn-primary"> X </button></td></tr>';
                }
                $('#table_emails').html(emails);
            });
        }, 1000);
     
    }

</script>
</body>
</html>