<?php
/**
 * Application wide controller
 *
 * @abstract
 * @package App.Controller
 */
abstract class AppController extends Controller {

	public $theme = 'Backend';

	public $components = array(
		'Session',
		'RequestHandler',
		'Crud.Crud' => array(
			'actions' => array(
				// The controller action 'index' will map to the IndexCrudAction
				'index' => 'Crud.Index',
				// The controller action 'add' will map to the AddCrudAction
				'view'  => 'Crud.View',
				'add'  => 'Crud.add',
				'delete'  => 'Crud.delete'
			),
			'listeners' => array(
				'Api' => array(
					'className' => 'Crud.Api'
				)
			)
		),
		'Auth' => array(
			'loginRedirect' => array(
				'plugin' => 'users', 
				'controller' => 'users', 
				'action' => 'index'
			),
			'logoutRedirect' => array(
				'plugin' => 'users', 
				'controller' => 'users',
				'action' => 'asd'
			)
		)	
	);

	public function beforeFilter() {
		if(!isset($this->request->params['admin'])) {
			$this->Auth->allow();
		}		

		if($this->request->prefix === 'admin' && $this->Auth->user('rank') != 'admin') {
			$this->Auth->deny();
		}

		if($this->request->prefix === 'admin') {
			$this->theme = 'Backend';
		}
		
		parent::beforeFilter();
	}

	function beforeRender(){
		if ($this->Session->check('Message.flash')) {
			$flash = $this->Session->read('Message.flash');
			if ($flash['element'] == 'default') {
				$flash['element'] = 'flash_success';
				$this->Session->write('Message.flash', $flash);
			}
		}
	}

/**
 * List of components which can handle action invocation
 * @var array
 */
	public $dispatchComponents = array();

/**
 * Dispatches the controller action. Checks that the action exists and isn't private.
 *
 * If Cake raises MissingActionException we attempt to execute Crud
 *
 * @param CakeRequest $request
 * @return mixed The resulting response.
 * @throws PrivateActionException When actions are not public or prefixed by _
 * @throws MissingActionException When actions are not defined and scaffolding and CRUD is not enabled.
 */
	public function invokeAction(CakeRequest $request) {
		try {
			return parent::invokeAction($request);
		} catch (MissingActionException $e) {
			// Check for any dispatch components
			if (!empty($this->dispatchComponents)) {
				// Iterate dispatchComponents
				foreach ($this->dispatchComponents as $component => $enabled) {
					// Skip them if they aren't enabled
					if (empty($enabled)) {
						continue;
					}

					// Skip if isActionMapped isn't defined in the Component
					if (!method_exists($this->{$component}, 'isActionMapped')) {
						continue;
					}

					// Skip if the action isn't mapped
					if (!$this->{$component}->isActionMapped($request->params['action'])) {
						continue;
					}

					// Skip if executeAction isn't defined in the Component
					if (!method_exists($this->{$component}, 'executeAction')) {
						continue;
					}

					// Execute the callback, can return CakeResponse object
					return $this->{$component}->executeAction();
				}
			}

			// No additional callbacks, re-throw the normal Cake exception
			throw $e;
		}
	}
}
