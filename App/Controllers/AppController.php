<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

  public function userView() {
    $usuario = Container::getModel('Usuario');

    $usuario->__set('id_usuario', $_SESSION['id']);

    $this->view->seguindo = $usuario->getTotalSeguindo();
    $this->view->ttweets = $usuario->getTotalTweets();
    $this->view->seguidores = $usuario->getTotalSeguidores();
  }

  public function timeline() {

    $this->validaAutenticacao();
    $this->userView();
    $tweet = Container::getModel('Tweet');
    $usuario = Container::getModel('Usuario');

    $tweet->__set('id_usuario', $_SESSION['id']);
    $usuario->__set('id_usuario', $_SESSION['id']);

    // $total_registros_pagina = ceil($usuario->getTotalTweets() / 10) * 10;
    // $deslocamentos = $i;
    // $pagina =  1;


    $total_registros_pagina = 10;
    $deslocamentos = 10;
    $pagina = 2;

    $total_registros_pagina = 10;
    $deslocamentos = 10;
    $pagina = 3;

    echo "<br><br><br>Pagina: $pagina | total de registros por pagina: $total_registros_pagina | Deslocamento: $deslocamentos";
    $total_tweets = $usuario->getTotalTweets();
    $tweets = $tweet->getAll($total_registros_pagina, $deslocamentos);


    if($_SESSION['id'] != '' && $_SESSION['nome'] != '') {
      // recuperacao dos tweets

      $this->view->tweets = $tweets;

      $this->render('timeline');
      
    } else {
      header('Location: /?login=erro');
    }


  }

  public function tweet() {

    $this->validaAutenticacao();
    $tweet = Container::getModel('Tweet');


      $tweet->__set('id_usuario', $_SESSION['id']);
      $tweet->__set('tweet', $_POST['tweet']);
      $tweet->salvar();
      header('Location: /timeline');

  }

  public function quemSeguir() {
    $this->validaAutenticacao();
    $this->userView();


    $res = array();
    if(isset($_GET['pesquisarPor'])) {
      $search = Container::getModel('Usuario');
      $search->__set('nome', $_GET['pesquisarPor']);
      $search->__set('id', $_SESSION['id']);
      $res = $search->pesquisaUsuario();
    }
    $this->view->usuarios = $res;
    $this->render('quemSeguir');

  }

  public function validaAutenticacao() {
    session_start();
    
    if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
      header('Location: /?login=erro');
    }
  }

  public function acao() {
    $this->validaAutenticacao();
    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
    $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
    $usuario = Container::getModel('Usuario');
    $usuario->__set('id', $_SESSION['id']);

    if($acao == 'seguir') {
      $follow = $usuario->seguirUsuario($id_usuario_seguindo);
      

    }else if($acao == 'deixar_de_seguir') {
      $usuario->deixarSeguirUsuario($id_usuario_seguindo);
    }

    header('Location: /quem_seguir?');

  }

  public function remover() {
    echo'toaqui';
    print_r($_POST);

    $this->validaAutenticacao();
    $tweets = $_POST['tweet'];

    $tweet = Container::getModel('Tweet');


    $id_usuario = $_SESSION['id'];
    
      $tweet->__set('id_usuario', $id_usuario );
      $tweet->__set('id', $tweets);
  
      $tweet->remover();
  
    



    header("Location: /timeline");



  }


}