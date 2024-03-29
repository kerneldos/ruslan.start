<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property Portal $ownPortal
 * @property Portal[] $portals
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * @return Connection
     */
    public static function getDb(): Connection {
        return Yii::$app->loginDb;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return ActiveRecord|array|null
     */
    public static function findByUsername(string $username) {
        return static::find()
            ->where(['OR', ['username' => $username], ['email' => $username]])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token): ?User {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
//            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     *
     * @return static|null
     */
    public static function findByVerificationToken(string $token): ?User {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string|null $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid(?string $token): bool {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): ?bool {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword(string $password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     *
     * @throws Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return ActiveQuery
     */
    public function getOwnPortal(): ActiveQuery {
        return $this->hasOne(Portal::class, ['user_id' => 'id']);
    }

    /**
     * @param int $portalId
     *
     * @return void
     */
    public function setOwnPortal(int $portalId) {
        $portal = Portal::findOne($portalId);
        $portal->created_by = $this->id;
        $portal->updated_by = $this->id;

        $this->populateRelation('own_portal', $portal);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPortals(): ActiveQuery {
        return $this->hasMany(Portal::class, ['id' => 'portal_id'])
            ->viaTable(UserPortal::tableName(), ['user_id' => 'id']);
    }

    /**
     * @param array|integer $portalIds
     *
     * @return void
     */
    public function setPortals($portalIds): void {
        if (!is_array($portalIds)) {
            $portalIds = [$portalIds];
        }

        $portals = [];
        foreach ($portalIds as $portalId) {
            $portals[] = Portal::findOne($portalId);
        }

        $this->populateRelation('portals', $portals);
    }

    /**
     * @param $insert
     * @param $changedAttributes
     *
     * @return void
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        $relatedRecords = $this->getRelatedRecords();

        if (isset($relatedRecords['portals'])) {
            foreach ($relatedRecords['portals'] as $portal) {
                $this->link('portals', $portal);
            }
        }

        if (isset($relatedRecords['own_portal'])) {
            $this->link('ownPortal', $relatedRecords['own_portal']);
        }
    }
}
