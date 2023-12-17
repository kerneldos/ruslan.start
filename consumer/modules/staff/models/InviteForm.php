<?php

namespace consumer\modules\staff\models;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Signup form
 */
class InviteForm extends Model
{
    public $email;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws Exception
     */
    public function invite(): ?bool {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->email;
        $user->email = $this->email;

        $user->generateAuthKey();
        $user->setPassword(Yii::$app->security->generateRandomString());
        $user->generatePasswordResetToken();

        $userSave = $user->save();
        $this->_user = $user;

        return $userSave && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     *
     * @param User $user user model to with email should be send
     *
     * @return bool whether the email was sent
     */
    protected function sendEmail(User $user): bool {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'inviteVerify-html', 'text' => 'inviteVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('You are invited at ' . Yii::$app->name)
            ->send();
    }

    /**
     * @return User|null
     */
    public function getUser(): User {
        return $this->_user;
    }
}
