<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;

class BlogPresenter extends BasePresenter
{
	/** @var Model\BlogRepository */
	protected $dbBlog;

    public function startup()
	{
		parent::startup();
		$this->dbBlog  = $this->getContext()->getService('blog');
	}

	public function actionDefault()
	{
		$this->template->stories   = $this->dbBlog->findAll();
	}

	public function actionPublish($id)
	{
		$query = array('id' => (int) $id);
		$this->dbBlog->update($query, array('published' => 1));
		$this->redirect('default');
	}

	public function actionUnpublish($id)
	{
		$query = array('id' => (int) $id);
		$result = $this->dbBlog->update($query, array('published' => 0));
		$this->redirect('default');
	}

	/**
	 * CreateStory form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentStoryForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('name', 'Name:')
			->setRequired('Please enter story name.');
		$form->addHidden('id', '');
		$form->addSubmit('send', 'Send');
		$form->onSuccess[] = $this->StoryFormSucceeded;
		return $form;
	}

	public function StoryFormSucceeded($form, $values)
	{
		try
		{
			$storyData = array();
			$isUpdate = ($values->id !== '');
			if($isUpdate)
			{
				$query = array('id' => (int) $values->id);
				$editItem = $this->dbBlog->find($query);
				if(NULL === $editItem)
				{
					$isUpdate = false;
				}
				else
				{
					$storyData = $editItem;
				}
			}
			$storyData['name'] = $values->name;
			$storyData['modified'] = time();
			if(false === $isUpdate)
			{
				$id = 1;
				$lastId = $this->dbBlog->findAll()->sort(array('id' => -1));
				foreach ($lastId as $row) 
				{
					$id = $row['id'] + 1;
					break;
				}
				$storyData['id'] = $id;
				$storyData['created'] = $storyData['modified'];
				$storyData['published'] = 0;
				$storyData['deleted'] = 0;
				$this->dbBlog->insert($storyData);
				$this->flashMessage('Story with name '.$values->name.' has been successfully added to blog');
			}
			else
			{
				$this->dbBlog->update($query, $storyData);
				$this->flashMessage('Story with name '.$values->name.' has been successfully updated id blog');
			}
			$this->redirect('Blog:default');
		}
		catch (BlogException $e) 
		{
			$form->addError($e->getMessage());
		}
	}

}
