<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request){
        
        return "Accion de pruebas user controller";
    }

public function register (Request $request){
   
  
       
        //recoger datos
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);
        
        if(!empty($params) && !empty($params_array)){
        //limpiar datos
        $params_array = array_map('trim',$params_array);

        // validar
         $validate = validator($params_array,[
             'name'     => 'required|alpha',
             'surname'  => 'required|alpha',
             'email'    => 'required|email|unique:users',
             'password' => 'required'
         ]);
         
         if($validate->fails()){
             //la validacion a fallado
             $data = array(
             'status'  => 'error',
             'code' =>  404,
             'mensaje' =>  'El usuario no se ha creado',
             'errors'   => $validate->errors()    
           );
             
         }else {
             
                 //cifrar pass  
                 $pwd = hash('sha256', $params->password);      
                 //comprar si el usuario no esta duplicado 
                 //crear usuario
                 $user = new User();
                 $user-> name = $params_array['name'];
                 $user-> surname = $params_array['surname'];
                 $user-> email = $params_array['email'];
                 $user-> password = $pwd;
                 $user-> role = 'Rol_Tecnico';
                 
                 //guardar el usuario
                 $user->save();
                 
             $data = array(
             'status'  => 'success',
             'code' =>  200,
             'mensaje' =>  'El usuario se ha creado',
             'user' => $user    
                 );
        }
        }else{
            
            $data = array(
             'status'  => 'error',
             'code' =>  404,
             'mensaje' =>  'los datos no son correctos',
             );    
           
            
        }
        //cifrar pass        
        //comprar si el usuario no esta duplicado 
        //crear usuario
      return response()->json($data, $data['code']);
        
    
}
    public function login (Request $request){
    
       $jwtAuth = new \JwtAuth();
         //recibir datos
         $json = $request->input('json', null);
         $params = json_decode($json);
         $params_array = \GuzzleHttp\json_decode($json, true);
         ////validar 
         $validate = validator($params_array,[
             'email'    => 'required|email',
             'password' => 'required'
         ]);
         
         if($validate->fails()){
             //la validacion a fallado
             $signup= array(
             'status'  => 'error',
             'code' =>  404,
             'mensaje' =>  'El usuario no se ha loggeado',
             'errors'   => $validate->errors()    
           );
             
         }else {
         //cifrar
         $pwd = $pwd = hash('sha256', $params->password); 
         // dovelver token o datos
         $signup = $jwtAuth->signup($params->email,$pwd);   
         if(!empty($params->gettoken)){
             $signup = $jwtAuth->signup($params->email, $pwd,true);
             
         }
         }
          
       return response()->json($signup,200);
    }
    public function update(Request $request){
        
       //comprar usuario
        
        $token = $request->header('Authorization');
        $jwtAuth = new  \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
         //rocoger datos
        $json = $request -> input('json', null);
         $params_array = json_decode($json, true);
        if($checkToken && !empty($params_array)){
         //sacar usuario identificado 
            $user = $jwtAuth->checkToken($token, true);
         
  
               //validar datos
            $validate = \validator($params_array,[              
             'name'     => 'required|alpha',
             'surname'  => 'required|alpha',
             'email'    => 'required|email|unique:users,'.$user->sub 
              ]);
         // quitar campos que no quiero
         unset($params_array['id']);
         unset($params_array['role']);
         unset($params_array['password']);
         unset($params_array['created_at']);
         unset($params_array['remenber_token']);
         // actualizar usuario en bbdd
            $user_update = User::where('id', $user->sub)->update($params_array);
         //devolver array con resultados
        $data = array (
               'code'   =>200,
               'status' =>'success',
               'message' =>$user,
               'changes' => $params_array
           );
           
       }else{
           $data = array (
               'code'   =>400,
               'status' =>'error',
               'message' =>'el usuario no esta indetificado correctamente'   
           );
           
       }
       return response()->json($data, $data['code']);
    }
    public function upload(Request $request){
        //  recoger datos
        $image = $request->file('file0');
        
        // validar que sea una imagen
        $validate = \Validator::make($request->all(),[
            'file0'=>'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        //  guardar imagen
        if(!$image || $validate->fails()){
              $data = array (
               'code'   =>400,
               'status' =>'error',
               'message' =>'Error al subir la imagen'  
                 );
        }else{
             $image_name =time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            
            $data = array (
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
         
      );      
        }
        
        return  response()->json($data, $data['code']);
        
    }
     
    public function  getImage($filename){
        $isset = \Storage::disk('users')->exists($filename);
        if($isset){
        $file = \Storage::disk('users')->get($filename);
        
        return new Response($file, 200);
        }else {
            $data = array (
               'code'   =>400,
               'status' =>'error',
               'message' =>'la imagen no existe'  
                 );
             return  response()->json($data, $data['code']);
        }
        
        }
        public function detail($id){
         $user = User::find($id);
         
         if(is_object($user)){
             $data = array (
               'code'   =>200,
               'status' =>'success',
               'user' => $user  
                 );
             
         }else{
             $data = array (
               'code'   =>400,
               'status' =>'error',
               'message' =>'el usuario no existe'  
                 );
           }
           return  response()->json($data, $data['code']);
        }
    }
 