<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $user
 * @property string|null $password
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password'], 'string'],
            // [['password'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['user'], 'string', 'max' => 255],
            // [['user'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'password' => 'Password',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param name
     * @return object
     */
    public function getByName($name) {
        $query = new Query;
        // compose the query
        $query->select('id, user, password')
            ->from('users')
            ->where(`user = '$name'`);
        $rows = $query->one();
        return $rows;
    }
}