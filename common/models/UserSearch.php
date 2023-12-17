<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'verification_token', 'temp_domain', 'invite_verification_token'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array            $params
     * @param ActiveQuery|null $query
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, ActiveQuery $query = null): ActiveDataProvider {
        if (empty($query)) {
            $query = User::find();
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
