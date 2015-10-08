<?php

namespace Croogo\Users\Model\Table;

use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Query;
use Cake\Validation\Validator;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Users\Model\Entity\User;

class UsersTable extends CroogoTable {

	use MailerAwareTrait;

	protected $_displayFields = [
		'id',
		'role.title' => 'Role',
		'username',
		'name',
		'status' => ['type' => 'boolean'],
		'email',
	];

	protected $_editFields = [
		'role_id',
		'username',
		'name',
		'email',
		'website',
		'status',
	];

	public $filterArgs = [
		'name' => ['type' => 'like', 'field' => ['Users.name', 'Users.username']],
		'role_id' => ['type' => 'value'],
	];

	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Croogo/Users.Roles');

		$this->addBehavior('Acl.Acl', [
			'className' => 'Croogo/Core.CroogoAcl',
			'type' => 'requester'
		]);
		$this->addBehavior('Search.Searchable');

		$this->eventManager()->on($this->getMailer('Croogo/Users.User'));
	}

	/**
	 * Used to register a new user
	 *
	 * @param User $user
	 * @param array $data
	 * @return bool|User
	 */
	public function register(User $user, array $data)
	{
		$user = $this->patchEntity($user, $data, [
			'fieldList' => [
				'username',
				'website',
				'name',
				'password',
				'email'
			]
		]);

		$user->set([
			'role_id' => RolesTable::ROLE_REGISTERED,
			'activation_key' => Text::uuid(),
		]);

		if (!$this->save($user)) {
			return false;
		}

		$this->dispatchEvent('Users.registered', [
			'user' => $user
		]);

		return $user;
	}

	/**
	 * Activate the user
	 *
	 * @param User $user
	 * @return bool|User
	 */
	public function activate(User $user)
	{
		$user->activation_key = null;

		if (!$this->save($user)) {
			return false;
		}

		$this->dispatchEvent('Users.activated', [
			'user' => $user
		]);

		return $user;
	}

	/**
	 * Starts an password reset procedure and sets out an email to the user
	 *
	 * @param User $user User to run the procedure for
	 * @return bool Returns true when successful, false if not
	 */
	public function resetPassword(User $user)
	{
		// Generate a unique activation key
		$user->activation_key = Text::uuid();

		$user = $this->save($user);
		if (!$user) {
			return false;
		}

		// Send out an password reset email
		$email = $this
			->getMailer('Croogo/Users.User')
			->send('resetPassword', [$user]);
		if (!$email) {
			return false;
		}

		return true;
	}

	public function changePasswordFromReset(User $user, array $data)
	{
		$user = $this->patchEntity($user, $data, [
			'fieldList' => [
				'password',
				'verify_password',
			]
		]);
		if ($user->errors()) {
			return $user;
		}

		$user->activation_key = null;

		return $user;
	}

	public function findByActivationKey(Query $query, array $options)
	{
		return $query->where([
			'username' => $options['username'],
			'activation_key' => $options['activationKey'],
		]);
	}

	public function validationDefault(Validator $validator) {
		return $validator
			->add('username', [
				'notBlank' => [
					'rule' => 'notBlank',
					'message' => 'The username has already been taken.',
					'last' => true
				],
				'validateUnique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => 'The username has already been taken.',
					'last' => true
				],
				'alphaNumeric' => [
					'rule' => 'alphaNumeric',
					'message' => 'This field must be alphanumeric',
					'last' => true
				]
			])
			->add('email', [
				'notEmpty' => [
					'rule' => 'notBlank',
					'message' => 'The username has already been taken.',
					'last' => true
				],
				'email' => [
					'rule' => 'email',
					'message' => 'Please provide a valid email address.',
					'last' => true
				],
				'validateUnique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => 'Email address already in use.',
					'last' => true
				]
			])
			->add('password', [
				'minLength' => [
					'rule' => ['minLength', 6],
					'message' => 'Passwords must be at least 6 characters long.',
					'last' => true
				],
				'compareWith' => [
					'rule' => ['compareWith', 'verify_password'],
					'message' => 'Passwords do not match. Please, try again',
					'last' => true
				]
			])
			->add('name', [
				'notBlank' => [
					'rule' => 'notBlank',
					'message' => 'This field cannot be left blank.',
					'last' => true
				],
				'name' => [
					'rule' => ['custom', '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}-_\[\]\(\) ]+$/mu'],
					'message' => 'This field must be alphanumeric',
					'last' => true
				]
			])
			->allowEmpty('website')
			->add('website', [
				'url' => [
					'rule' => 'url',
					'message' => 'This field must be a valid URL',
					'last' => true
				]
			]);
	}


	/**
	 * afterSave
	 *
	 * @param Model $model
	 * @param boolean $created
	 * @return void
	 */
	public function afterSave(Event $event, Entity $entity, ArrayObject $options) {
		// update ACO alias
		if (!empty($model->data['User']['username'])) {
			$node = $model->node();
			$aro = $node[0];
			$model->Aro->id = $aro['Aro']['id'];
			$model->Aro->saveField('alias', $model->data['User']['username']);
		}
		Cache::clearGroup('acl', 'permissions');
	}

	/**
	 * afterDelete
	 */
	public function afterDelete(Model $model) {
		Cache::clearGroup('acl', 'permissions');
	}
}
