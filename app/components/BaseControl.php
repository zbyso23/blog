<?php
use Nette\Application\UI;

abstract class BaseControl extends UI\Control
{
    protected $presenter;

	/**
	 * Automatically registers template file.
	 *
	 * @note Idea by Jan Tvrdík (http://nette.merxes.cz/base-control/)
	 *
	 * @param    string
	 * @return   Nette\Templating\FileTemplate
	 */
	protected function createTemplate($class = null)
	{
		$template = parent::createTemplate($class);
		$this->setTemplateFile(null, $template);
        $template->registerHelper('printf', 'sprintf');
		return $template;
	}

    public function templatePrepareFilters($template)
    {
        $latte = new Nette\Latte\Engine;
        $template->registerFilter($latte);
    }
	/* --------------------------------------------------------------------- */

	protected function setTemplateFile($name = null, &$template = null)
	{
		$reflection = $this->getReflection();
		$dir = dirname($reflection->getFileName());

		if ($name === null) {
			$name = $reflection->getShortName();
		}

		$filename = $dir . \DIRECTORY_SEPARATOR . "/{$name}.latte";

		if (is_null($template)) {
			$this->template->setFile($filename);
		} else {
			$template->setFile($filename);
		}
	}

    /**
     * @param Presenter
     */
    protected function attached($presenter)
    {
        if($presenter instanceof Nette\Application\UI\Presenter) 
        {
            $this->presenter = $presenter;
            // zde byla komponenta právě připojena k presenteru
        }
        parent::attached($presenter);
    }
}