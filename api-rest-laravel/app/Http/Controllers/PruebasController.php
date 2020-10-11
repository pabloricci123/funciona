<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function  index (){
        $titulo = 'Animales';
        $animales = ['Perro','Gato','Tigre'];
        
        return view('pruebas.index', array (
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }
    
    public function testOrm (){
      /*$posts = Post::all();
       foreach($posts as $post){
        echo "<h1>".$post->title."</h1>";
        echo "<span style='color:red'>{$post->user->name} - {$post->Category->name}</span>";
        echo "<p>".$post->content."</p>";
        echo '<hr>'; 
        
       }
*
               */
       $categories = Category::all();
       foreach ($categories as $Category){
           
           echo "<h1>{$Category->name}</h1>";
            foreach($Category->posts as $post){
        echo "<h3>".$post->title."</h3>";
        echo "<span style='color:red'>{$post->user->name} - {$post->Category->name}</span>";
        echo "<p>".$post->content."</p>";
                    
       }
       echo '<hr>';
       }
        die();
       
    }
    }

