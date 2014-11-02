<?php

namespace App\FrontModule\Presenters;

use App\Model;

class BasePresenter extends \App\Presenters\BasePresenter
{
	const MODULE = 'Front';

	public function startup()
	{
		parent::startup();
        $this->addFilesJs(array(
            'jquery.js',
        ), false);
        $this->addFilesJs(array(
        	'../bootstrap/js/bootstrap.min.js',
            'netteForms.js',
            'main.js'
        ));
        $this->addFilesCss(array(
        	'../bootstrap/css/bootstrap.min.css',
            'screen.css'
        ));
	}
}