<?php

namespace Arachne\Tests\Verifier\Integration\Classes;

use Arachne\Verifier\Application\TVerifierPresenter;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek
 */
class ArticlePresenter extends Presenter
{

	use TVerifierPresenter;

	/**
	 * @Enabled(TRUE)
	 */
	public function actionDefault()
	{
	}

	/**
	 * @Enabled(FALSE)
	 * @param int $id
	 */
	public function actionEdit($id)
	{
	}

	/**
	 * @Enabled(TRUE)
	 * @param int $id
	 */
	public function actionModify($id)
	{
		$this->redirectVerified('edit', $id);
	}

	/**
	 * @Enabled(TRUE)
	 * @param int $id
	 */
	public function actionDelete($id)
	{
	}

	/**
	 * @Enabled(TRUE)
	 * @param int $id
	 */
	public function actionRemove($id)
	{
		$this->redirectVerified('delete', $id);
	}

	/**
	 * @Enabled(TRUE)
	 * @param int $id	 
	 */
	public function actionRedirect($id)
	{
		$this->redirectVerified(301, 'delete', $id);
	}

	/**
	 * @Enabled(TRUE)
	 * @return BlockControl
	 */
	protected function createComponentHeader()
	{
		return new BlockControl();
	}

	/**
	 * @Enabled(FALSE)
	 * @return BlockControl
	 */
	protected function createComponentFooter()
	{
		return new BlockControl();
	}

}
