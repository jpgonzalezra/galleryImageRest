<?php
class ImageRestController extends ERestController {
	
	/**
	 * Returns the model assosiated with this controller.
	 * The assumption is that the model name matches your controller name
	 * If this is not the case you should override this method in your controller
	 */
	public function getModel() {
		if ($this->model === null) {
			/* $modelName = str_replace('Controller', '', get_class($this)); */
			$modelName = 'Image';
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
								'imageRest' 
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
		$this->outputHelper ( 'Images Retrieved Successfully', $this->getModel ()->with ( $this->nestedRelations )->filter ( $this->restFilter )->orderBy ( $this->restSort )->limit ( $this->restLimit )->offset ( $this->restOffset )->findAll (), $this->getModel ()->with ( $this->nestedRelations )->filter ( $this->restFilter )->count () );
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
	
	/**
	 * This is broken out as a sperate method from actionRestUpdate
	 * To allow for easy overriding in the controller
	 * and to allow for easy unit testing
	 */
	public function doRestUpdate($id, $data) {
		$collections = $data ['collections'];
		$collectionIDs = array ();
		foreach ( $collections as $collection ) {
			$collectionIDs [] = $collection ['id'];
		}
		
		$model = $this->loadOneModel ( $id );
		if (is_null ( $model )) {
			$this->HTTPStatus = $this->getHttpStatus ( 404 );
			throw new CHttpException ( 404, 'Image  Not Found' );
		} else {
			if (! (empty ( $collections )))
				$model->collections = $collectionIDs;
				// else $model->collections = array(Collection::WAREHOUSE_ID);
			else
				$model->collections = array ();
			$model->attributes = $data;
			// $model->collections =$categoryIDs;
			
			if ($model->save ()) {
				
				header ( 'success', true, 200 );
				echo json_encode ( array (
						'success' => true,
						strtolower ( get_class ( $model ) ) => $model->attributes,
						'collections' => $collections 
				) );
				exit ();
			} else {
				header ( 'error', true, 400 );
				$errors = $model->getErrors ();
				echo json_encode ( array (
						'success' => false,
						'errors' => $errors 
				) );
				exit ();
			}
		}
	}
	public function doRestDelete($id) {
		$model = $this->loadOneModel ( $id );
		if (is_null ( $model )) {
			$this->HTTPStatus = $this->getHttpStatus ( 404 );
			throw new CHttpException ( 404, 'Image Not Found' );
		} else {
			if ($model->delete ())
				// $data = array('success' => true, 'message' => 'Image Deleted', 'data' => array('id' => $id));
				$data = array (
						'success' => true,
						'message' => 'Image Deleted',
						'id' => $id 
				);
			else {
				$this->HTTPStatus = $this->getHttpStatus ( 406 );
				throw new CHttpException ( 406, 'Could not delete Image  with ID: ' . $id );
			}
			$this->renderJson ( array (
					'data' => $data 
			) );
		}
	}
	public function doRestCreate($data) {
		$collections = array();
		if (! empty ( $data ['collections'] )) {
			$collections = explode ( ',', $data ['collections'] );
		}
		
		$model = new Image ();
		$model->attributes = $data;
		$model->collections = $collections;
		
		if (! $model->save ()) {
			
			header ( 'error', true, 400 );
			$errors = $model->getErrors ();
			echo json_encode ( array (
					
					'success' => false,
					'message' => $errors,
					'errorCode' => '400' 
			) );
			exit ();
		}
		header ( 'success', true, 200 );
		echo json_encode ( array (
				'data' => array (
						'success' => true,
						strtolower ( get_class ( $model ) ) => $model->attributes,
						'collections' => $model->collections 
				) 
		) );
		exit ();
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