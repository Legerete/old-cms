<?php

namespace App\PrivateModule;

use App\PrivateModule\Component\PagesTree;
use App\PrivateModule\Component\Wysiwyg;
use App\PrivateModule\AttachmentModule\Model\Service\AttachmentService;

/**
 * PrivatePresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class BasePresenter extends \App\Presenters\SecuredPresenter
{

	/**
	 * @inject
	 * @var \WebLoader\Nette\LoaderFactory
	 */
	public $webloaderLoaderFactory;


	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	public function createComponentCss()
	{
		return $this->webloaderLoaderFactory->createCssLoader('private');
	}

	/**
	 * @return \WebLoader\Nette\JavaScriptLoader
	 */
	public function createComponentJs()
	{
		return $this->webloaderLoaderFactory->createJavaScriptLoader('private');
	}

	/**
	 * Finds layout template file name.
	 * @return string
	 * @internal
	 */
	public function findLayoutTemplateFile()
	{
		if ($this->layout === FALSE) {
			return;
		} elseif (preg_match('#[/\\\\]#', $this->layout) && file_exists($this->layout)) {
			return $this->layout;
		}

		$files = $this->formatLayoutTemplateFiles();
		foreach ($files as $file) {
			if (is_file($file)) {
				return $file;
			}
		}

		if ($this->layout) {
			$file = preg_replace('#^.*([/\\\\].{1,70})\z#U', "\xE2\x80\xA6\$1", reset($files));
			$file = strtr($file, '/', DIRECTORY_SEPARATOR);
			throw new \Nette\FileNotFoundException("Layout not found. Missing template '$file'.");
		}
	}
}
