<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->maxLength('firstName', 255, 'First name is too long.')->AllowEmpty('firstName');
        $validator->maxLength('lastName', 255, 'Last name is too long.')->AllowEmpty('lastName');
        $validator->maxLength('userName', 255, 'User name is too long.')->NotEmpty('userName', 'User name can not be empty.');
        $validator->minLength('password', 8, 'Password should be at least 8 char.');

        return $validator;
    }
}
