<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;


class UserController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['index','userlist'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function accessRules()
    {
        return array(
            array('allow', 'actions'=>array('REST.GET', 'REST.PUT', 'REST.POST', 'REST.DELETE', 'REST.OPTIONS'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        Yii::$app->controller->enableCsrfValidation = false;


        return parent::beforeAction($action);
    }


    public function actions()
    {
        return array(
            'REST.'=>'RestfullYii.actions.ERestActionProvider',
        );
    }


    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            array(
                'RestfullYii.filters.ERestFilter + 
                REST.GET, REST.PUT, REST.POST, REST.DELETE, REST.OPTIONS'
            ),
        );
    }

    public function actionIndex()
    {

        $user = new User();
        $user = $user->findOne(Yii::$app->user->identity->id);
        $jwt = $user->getJWT();

        $user->jwt = $jwt;

        if($user->save(false)) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user->find()->all();
            $items = ['access_token' => $user->jwt, 'token_type'=> 'bearer', 'expires_in'=>3600];

            $URL='http://newproject/user/userlist';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$URL);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ',
                'token:'.$items['access_token']
            ));
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
            $result=curl_exec ($ch);

            curl_close ($ch);

//            print_r($status_code);
            return $result;
        };



//        return $this->render('index');
    }

    public function actionUserlist()
    {

        $user = New User();

        foreach (getallheaders() as $name => $value) {

            if ($name == 'token'  ) {
                $user->findIdentityByAccessToken($value);

            }
        }


    }

    /**
     * $method e.g POST, GET, PUT
     * $data = [
    'param' => 'value',
    ]
     */

    public function curlToRestApi($method, $url, $data = null)
    {
        $curl = curl_init();

        $username = Yii::$app->user->identity->username;
        $pas = Yii::$app->user->identity->password_hash;

        $model = new User();

        // switch $method
        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);


                if($data !== null) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            // logic for other methods of interest
            // .
            // .
            // .

            default:
                if ($data !== null){
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }

        }

        // Authentication [Optional]
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $username.":".$pas);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}
