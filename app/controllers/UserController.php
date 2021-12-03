<?php

namespace app\controllers;

use app\core\App;
use app\core\controllers\ControllerApi;
use app\core\request\Request;
use app\core\response\Response;
use app\models\mongodb\collection\e_commerce\User;
use app\models\mongodb\model\LoginModel;
use app\models\mongodb\model\RegisterModel;
use Exception;
use Google_Client;
use Google_Service_Oauth2;
use PHPMailer\PHPMailer\PHPMailer;

class UserController extends ControllerApi
{
    private array $result = [];
    private string $email;
    private string $pass;

    public function setResult(): array
    {
        return $this->result;
    }

    public function actionsMiddle(): array
    {
        return [];
    }

    public function __construct(Request $request)
    {
        $this->req = $request->getBody();
        $this->email = $request->getBody()['email'] ?? '';
        $this->pass = $request->getBody()['pass'] ?? '';
    }

    public function show()
    {
        if (App::$app->login()) {
            $this->result[0] = App::$app->session->get('user');
        } else {
            $this->result[0] = false;
        }
    }

    public function login()
    {
        $login = new LoginModel();
        if ($login->validate($this->req)) {
            if ($login->active($this->email)) {
                $this->result[0] =  App::$app->session->get('user');
            } else {
                $this->result[0] =  'unactive';
            }
        } else {
            $this->result[0] =  $login->errors;
            $this->result[1] = 412;
        }
    }

    public function register()
    {
        $register = new RegisterModel();
        if ($register->validate($this->req)) {
            $register->insert($this->req);
            $this->mail($this->req['email'], $register->hashCode);
        } else {
            $this->result[0] =  $register->errors;
            $this->result[1] = 412;
        }
    }

    public function logout()
    {
        $session = App::$app->session;
        $session->remove($session::USER);
    }

    public function active(Request $request, Response $response)
    {
        $hash = $request->getBody()['hash'];
        $email = $request->getBody()['email'];
        $user = new User();
        $user->filter = ['email' => $email];
        if (password_verify($user->findOne()['code'], $hash)) {
            $user->updateOne([
                'email' => $email
            ], [
                '$set' => [
                    'active' => 1
                ]
            ]);
            $this->result[0] = true;
        }
        $response->redirect("/user?action=login&active=1&email=$email");
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
            $email = $googleInfo->email;
            $name = $googleInfo->name;
            $img = $googleInfo->img = $googleInfo->picture;
            $user = new User();
            $user->filter = [
                'email' => $email,
            ];
            if ($user->count() < 1) {
                $user->insert([
                    'name' => $name,
                    'email' => $email,
                    'img' => $img,
                    'active' => 1
                ]);
            };
            App::$app->session->set('user', $user->findOne());
            $this->result[0] = 'success';
            $response->redirect('/');
        } else {
            $this->result[0] =  $client->createAuthUrl();
        }
    }

    public function index()
    {
    }

    public function store()
    {
    }

    public function insert()
    {
    }

    public function update()
    {
    }

    public function mail($email, $hash)
    {


        $mailer = new PHPMailer();
        try {
            //Server settings

            // $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mailer->isSMTP();                                            //Send using SMTP
            $mailer->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mailer->Username   = 'paultran250891@gmail.com';                     //SMTP username
            $mailer->Password   = 'Toikhongbiet!2';                               //SMTP password
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mailer->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mailer->setFrom('localhost', 'localhost');
            $mailer->addAddress($email, 'localhost');     //Add a recipient
            // $mailer->addAddress('ellen@example.com');               //Name is optional
            // $mailer->addReplyTo('info@example.com', 'Information');
            // $mailer->addCC('cc@example.com');
            // $mailer->addBCC('bcc@example.com');

            //Attachments
            // $mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            // $mailer->isHTML(true);                                  //Set email format to HTML
            $mailer->Subject = "active $email";
            $mailer->Body    = " Nhan vao day de active
            </br> https://localhost/active?hash=$hash&email=$email";
            $mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mailer->send();
            $this->result[0] = 'success';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
        }
    }
}
