<?php namespace Exolnet\Test;

abstract class TestCaseFunctionalResource extends TestCaseFunctional {
	/**
	 * @return string
	 */
	public abstract function getBasePath();

	/**
	 * @return string
	 */
	public abstract function getController();

	public function getTestedActions()
	{
		return ['index', 'create', 'store', 'edit', 'update', 'destroy'];
	}

	//==========================================================================
	// Index
	//==========================================================================

	public function testIndexRouteExists()
	{
		$this->checkTested('index');

		$this->assertRouteExists('GET', $this->getBasePath());
		$this->assertRouteMatchesAction('GET', $this->getBasePath(), $this->getController().'@index');
	}

	public function testIndexGet()
	{
		$this->checkTested('index');

		$this->get($this->getBasePath());

		$this->assertResponseOk();
	}

	//==========================================================================
	// Create
	//==========================================================================

	public function testCreateRouteExists()
	{
		$this->checkTested('create');

		$this->assertRouteExists('GET', $this->getBasePath().'/create');
		$this->assertRouteMatchesAction('GET', $this->getBasePath().'/create', $this->getController().'@create');
	}

	public function testCreateGet()
	{
		$this->checkTested('create');

		$this->get($this->getBasePath().'/create');

		$this->assertResponseOk();
	}

	//==========================================================================
	// Store
	//==========================================================================

	public function testStoreRouteExists()
	{
		$this->checkTested('store');

		$this->assertRouteExists('POST', $this->getBasePath());
		$this->assertRouteMatchesAction('POST', $this->getBasePath(), $this->getController().'@store');
	}

	public function testStorePost(array $data = [])
	{
		$this->checkTested('store');

		$this->post($this->getBasePath(), $data);

		$this->displayErrors();
		$this->assertRedirectedTo($this->storeRedirect());
		$this->assertNotice('success');
	}

	public function storeRedirect()
	{
		return $this->getBasePath();
	}

	public function testStoreEmptyPost()
	{
		$this->checkTested('store');

		$this->post($this->getBasePath());

		$this->assertRedirectedTo($this->getBasePath().'/create');
		$this->assertNotice('error');
	}

	//==========================================================================
	// Edit
	//==========================================================================

	public function testEditRouteExists()
	{
		$this->checkTested('edit');

		$this->assertRouteExists('GET', $this->getBasePath().'/1/edit');
		$this->assertRouteMatchesAction('GET', $this->getBasePath().'/1/edit', $this->getController().'@edit');
	}

	public function testEditGet()
	{
		$this->checkTested('edit');

		$this->get($this->getBasePath().'/1/edit');

		$this->assertResponseOk();
	}

	//==========================================================================
	// Update
	//==========================================================================

	public function testUpdateRouteExists()
	{
		$this->checkTested('update');

		$this->assertRouteExists('PUT', $this->getBasePath().'/1');
		$this->assertRouteMatchesAction('PUT', $this->getBasePath().'/1', $this->getController().'@update');
	}

	public function testUpdateMissingPost()
	{
		$this->checkTested('update');

		$this->expectResponseMissing();

		$this->put($this->getBasePath().'/0');
	}

	public function testUpdatePost(array $data = [])
	{
		$this->checkTested('update');

		$this->put($this->getBasePath().'/1', $data);

		$this->displayErrors();
		$this->assertRedirectedTo($this->updateRedirect());
		$this->assertNotice('success');
	}

	public function updateRedirect()
	{
		return $this->getBasePath();
	}

	public function testUpdateEmptyPost()
	{
		$this->checkTested('update');

		$this->put($this->getBasePath().'/1');

		$this->assertRedirectedTo($this->getBasePath().'/1/edit');
		$this->assertNotice('error');
	}

	public function testUpdateInvalidPost(array $data = [])
	{
		$this->checkTested('update');

		$this->put($this->getBasePath().'/1', $data);

		$this->assertRedirectedTo($this->getBasePath().'/1/edit');
		$this->assertNotice('error');
	}

	//==========================================================================
	// Destroy
	//==========================================================================

	public function testDestroyRouteExists()
	{
		$this->checkTested('destroy');

		$this->assertRouteExists('DELETE', $this->getBasePath().'/1');
		$this->assertRouteMatchesAction('DELETE', $this->getBasePath().'/1', $this->getController().'@destroy');
	}

	public function testDestroyGet()
	{
		$this->checkTested('destroy');

		$this->delete($this->getBasePath().'/1');

		$this->displayErrors();
		$this->assertRedirectedTo($this->destroyRedirect());
		$this->assertNotice('success');
	}

	public function destroyRedirect()
	{
		return $this->getBasePath();
	}

	public function testDestroyMissingGet()
	{
		$this->checkTested('destroy');

		$this->expectResponseMissing();

		$this->delete($this->getBasePath().'/0');
	}

	protected function checkTested($action)
	{
		if ( ! in_array($action, $this->getTestedActions())) {
			$this->markTestSkipped('Action "'.$action.'" is not tested for this controller');
		}
	}
}