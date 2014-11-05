<?php

namespace App\FrontModule\Presenters;

use App\Model;

abstract class BasePresenter extends \Presenters\BasePresenter
{
	protected $module = 'Front';

	public function startup()
	{
		parent::startup();
        $this->addFilesJs(array(
            'jquery.js',
        ), false);
        $this->addFilesJs(array(
        	'../bootstrap/js/bootstrap.min.js',
            '../bootstrap/js/docs.min.js',
            'netteForms.js',
            'main.js'
        ));
        $this->addFilesCss(array(
        	'../bootstrap/css/bootstrap.min.css',
            'blog.css',
            'screen.css'
        ));
	}
}