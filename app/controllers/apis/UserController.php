<?php

namespace app\controllers\apis;

use app\core\App;
use app\core\middlewares\AuthMiddleware;
use app\core\request\Request;
use app\core\response\Response;
use app\core\Test;
use Google_Client;
use Google_Service_Oauth2;


class UserController 
{

    public function __construct()
    {
        $auth = new AuthMiddleware();
        $auth->checkSubmit(['login', 'user', '']);
        $auth->login(['user']);
        $auth->execute();
    }

    public function login(Request $request)
    {
        $this->login->loadData($request->getBody());
        $this->login->validate();
        $this->login->login();
        $result = empty($this->login->errors) ?  "success" : $this->login->errors;
        echo json_encode($result);
    }

    public function signin(Request $request)
    {
        $result = $request->getBody();
        $this->signin->loadData($result);
        $result = $this->signin->validate();
        $result = $this->signin->errors;
        if (empty($result)) {
            $this->signin->save();
            $result =   'success';
        }
        echo json_encode($result);
    }

 
    public function google(Request $request, Response $response)
    {
        $code = $request->getBody()['code'] ?? false;
        
        $client = new Google_Client();
        $client->setClientId('531537995062-dr8ie36mgr8p4hesnaql8fba31ad9is1.apps.googleusercontent.com');
        $client->setClientSecret('ZN63xGlaWM5OsgtaZ0o0Jw5c');
        $client->setRedirectUri('https://framework-commerce.tk/logingg');
        $client->addScope("email");
        $client->addScope("profile");
        if ($code) {
          
            $token = $client->fetchAccessTokenWithAuthCode($code);
            $client->setAccessToken($token);
            $gauth = new Google_Service_Oauth2($client);
            
            $googleInfo = $gauth->userinfo->get();
            $googleInfo->email;
            $googleInfo->name;
            $googleInfo->img = $googleInfo->picture;

            
        } else {
            echo $client->createAuthUrl();
        }
    }

    public function destroy(Request $request)
    {
        App::$app->session->remove('user');
        
    }
}
