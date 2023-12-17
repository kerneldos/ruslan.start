<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_portal".
 *
 * @property int $id
 * @property int $user_id
 * @property int $portal_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Portal $portal
 * @property User $user
 */
class UserPortal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string {
        return 'user_portal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['user_id', 'portal_id'], 'required'],
            [['user_id', 'portal_id', 'created_at', 'updated_at'], 'integer'],
            [['portal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Portal::class, 'targetAttribute' => ['portal_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'portal_id' => 'Portal ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Portal]].
     *
     * @return ActiveQuery
     */
    public function getPortal(): ActiveQuery {
        return $this->hasOne(Portal::class, ['id' => 'portal_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
