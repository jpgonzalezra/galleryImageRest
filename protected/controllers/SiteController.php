<?php
class SiteController extends Controller {
	
	/**
	 * Declares class-based actions.
	 */
	public function actions() {
		return array (
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha' => array (
						'class' => 'CCaptchaAction',
						'backColor' => 0xFFFFFF 
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page' => array (
						'class' => 'CViewAction' 
				) 
		);
	}
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		$this->actionLogin ();
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		$error = Yii::app ()->errorHandler->error;
		if ($error) {
			if (Yii::app ()->request->isAjaxRequest)
				echo $error ['message'];
			else
				$this->render ( 'error', $error );
		}
	}
	public function actionSuccess() {
		echo 'redireccionar a algun lado del front_end';
	}
	public function actionLogin() {
		$model = new LoginForm ();
		
		if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'login-form') {
			echo CActiveForm::validate ( $model, array (
					'username',
					'password',
					'verify_code' 
			) );
			Yii::app ()->end ();
		}
		
		if (isset ( $_POST ['LoginForm'] )) {
			$model->attributes = $_POST ['LoginForm'];
			if ($model->validate ( array (
					'username',
					'password',
					'verify_code' 
			) ) && $model->login ()) {
				Yii::app ()->user->setFlash ( 'success', 'Welcome ' . app ()->user->name );
				$this->redirect ( bu () . '/site/success' );
			}
		}
		
		$this->render ( 'login', array (
				'model' => $model 
		) );
	}
	
	/**
	 * This is the action that handles user's logout
	 */
	public function actionLogout() {
		Yii::app ()->user->logout ();
		$this->redirect ( Yii::app ()->homeUrl );
	}
	
}