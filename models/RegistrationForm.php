<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegistrationForm extends User
{

    /**
     * @var string User email address
     */
    public $email;
    /**
     * @var string Username
     */
    public $username;
    /**
     * @var string Password
     */
    public $password;
    /**
     * @var string Password confirm
     */
    public $password_repeat;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'password_repeat', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }
    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var User $user */
        $user = New User;
        $this->loadAttributes($user);


        If (Yii::$app->request->post()['register-form']) {
                $user->email = Yii::$app->request->post()['register-form']['email'];
                $user->username = Yii::$app->request->post()['register-form']['username'];
                $user->password_hash = md5(Yii::$app->request->post()['register-form']['password']);

                If ($user->save()) {
                    return true;
                }

                return false;
            }

        return false;
    }
    /**
     * Loads attributes to the user model. You should override this method if you are going to add new fields to the
     * registration form. You can read more in special guide.
     *
     * By default this method set all attributes of this model to the attributes of User model, so you should properly
     * configure safe attributes of your User model.
     *
     * @param User $user
     */
    protected function loadAttributes(User $user)
    {
        $user->setAttributes($this->attributes);
    }
}
