<?php
class UserRestController extends ERestController {
	
	/**
	 * Returns the model assosiated with this controller.
	 * The assumption is that the model name matches your controller name
	 * If this is not the case you should override this method in your controller
	 */
	public function getModel() {
		if ($this->model === null) {
			$modelName = 'User';
			$this->model = new $modelName ();
		}
		$this->_attachBehaviors ( $this->model );
		return $this->model;
	}
	public function accessRules() {
		$restAccessRules = array (
				array (
						'allow', // allow all users to perform 'index' and 'view' actions
						'actions' => array (
								'userRest' 
						),
						'users' => array (
								'*' 
						) 
				) 
		);
		
		if (method_exists ( $this, '_accessRules' ))
			return CMap::mergeArray ( $restAccessRules, $this->_accessRules () );
		else
			return $restAccessRules;
	}
	
	/**
	 * Helper for loading a single model
	 */
	protected function loadOneModel($id) {
		return $this->getModel ()->with ( $this->nestedRelations )->findByPk ( $id );
	}
	
	/**
	 * This is broken out as a sperate method from actionRestList
	 * To allow for easy overriding in the controller
	 * and to allow for easy unit testing
	 */
	public function doRestList() {
		$success=false;
		$code=400;
		$model = new LoginForm ();
		if(!empty($_GET['username']) && !empty($_GET['password'])){
			$_POST ['LoginForm']['username']=$_GET['username'];
			$_POST ['LoginForm']['password']=$_GET['password'];
			$model->attributes = $_POST ['LoginForm'];
			if ($model->validate ( array (
					'username',
					'password',
					'verify_code'
			) ) && $model->login ()) {
				$success=true;
				$code=200;
			}
		}
		header ( 'error', true, $code );
		$errors = $model->getErrors ();
		echo json_encode ( array (
				'code' => $code, 
				'success' => $success
		) );
		exit;
	}
	
	public function outputHelper($message, $results, $totalCount = 0, $model = null) {
		if (is_null ( $model ))
			$model = lcfirst ( get_class ( $this->model ) );
		else
			$model = lcfirst ( $model );
		
		$this->renderJson ( array (
				// 'success'=>true,
				// 'message'=>$message,
				'data' => array (
						'success' => true,
						'totalCount' => $totalCount,
						'message' => $message,
						$model => $this->allToArray ( $results ) 
				) 
		) );
	}
	
	public function onException($event) {
		if (! $this->developmentFlag && ($event->exception->statusCode == 500 || is_null ( $event->exception->statusCode )))
			$message = "Internal Server Error";
		else {
			$message = $event->exception->getMessage ();
			if ($tempMessage = CJSON::decode ( $message ))
				$message = $tempMessage;
		}
		
		$errorCode = (! isset ( $event->exception->statusCode ) || is_null ( $event->exception->statusCode )) ? 500 : $event->exception->statusCode;
		
		$this->renderJson ( array (
				'errorCode' => $errorCode,
				'message' => $message,
				'success' => false 
		) );
		$event->handled = true;
		header ( 'error', true, $errorCode );
	}
}