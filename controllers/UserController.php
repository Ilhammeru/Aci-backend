<?php

namespace app\controllers;

use app\models\Users;
use Yii;

class UserController extends \yii\web\Controller
{
    /**
     * Define model
     */
    public $modelClass = 'app\models\Users';

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
        ];
    }

    /**
     * @return {JsonResponse}
     * @param user
     * @param password
     */
    public function actionLogin() {
        $toArray = array();
        $check = false;

        // define response to json format
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // define request
        $request = Yii::$app->request;

        // check request in database
        $model = new Users();
        // check username
        $users = $model->getByName($request->post('user'));
        if ($users) {
            // check password
            if ($request->post('password') == $users['password']) {
                $check = true;
            } else {
                $check = false;
            }
        }

        if (!$check) {
            // TODO: action create jwt from credential
            $toArray = ['rc' => '01', 'rd' => 'Invalid user/password'];
        } else {
            // TODO: action based on test question
            // curl section
            $url = "https://devel.bebasbayar.com/web/test_programmer.php";
            $resp = $this->curl([
                'url' => $url,
                'user' => $request->post('user'),
                'password' => $request->post('password')
            ]);
            $toArray = json_decode($resp);
        }

        
        $data = [
            'message'   => 'Data retrieve',
            'data' => $toArray ?? ['rc' => '01', 'rd' => 'Invalid format']
        ];
        return $data;
    }

    /**
     * @param payload
     * @return {Object}
     */
    public function curl($payload) {
        // curl action
        $curl = curl_init($payload['url']);
        curl_setopt($curl, CURLOPT_URL, $payload['url']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = [
            "user" => $payload['user'],
            "password" => $payload['password']
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;
    }

    /**
     * Dummy action to insert user
     * @param user
     * @param register
     * @return {JsonResponse}
     */
    public function actionRegister() {
        // define request
        $request = Yii::$app->request;

        // define response
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new Users();

        // check data if exist
        $current = $model->getByName($request->post('user'));
        if ($current) {
            $message = 'Save failed';
            $data = [];
        } else {
            $model->user = $request->post('user');
            $model->password = $request->post('password');
            $model->created_at = date('Y-m-d');
            $model->save();

            $message = 'Save success';
            $data = $model;
        }

        return [
            'message' => $message,
            'data' => $data
        ];
    }
}
