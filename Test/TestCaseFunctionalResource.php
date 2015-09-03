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

	//==========================================================================
	// Index
	//==========================================================================

	public function testIndexRouteExists()
	{
		$this->assertRouteExists('GET', $this->getBasePath());
		$this->assertRouteMatchesAction('GET', $this->getBasePath(), $this->getController().'@index');
	}

	public function testIndexGet()
	{
		$this->beAdmin()->get($this->getBasePath());

		$this->assertResponseOk();
	}

	//==========================================================================
	// Create
	//==========================================================================

	public function testCreateRouteExists()
	{
		$this->assertRouteExists('GET', $this->getBasePath().'/create');
		$this->assertRouteMatchesAction('GET', $this->getBasePath().'/create', $this->getController().'@create');
	}

	public function testCreateGet()
	{
		$this->beAdmin()->get($this->getBasePath().'/create');

		$this->assertResponseOk();
	}

	//==========================================================================
	// Store
	//==========================================================================

	public function testStoreRouteExists()
	{
		$this->assertRouteExists('POST', $this->getBasePath());
		$this->assertRouteMatchesAction('POST', $this->getBasePath(), $this->getController().'@store');
	}

	public function testStorePost(array $data = [])
	{
		$this->beAdmin()->post($this->getBasePath(), $data);

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
		$this->beAdmin()->post($this->getBasePath());

		$this->assertRedirectedTo($this->getBasePath().'/create');
		$this->assertNotice('error');
	}

	//==========================================================================
	// Update
	//==========================================================================

	public function testUpdateRouteExists()
	{
		$this->assertRouteExists('PUT', $this->getBasePath().'/1');
		$this->assertRouteMatchesAction('PUT', $this->getBasePath().'/1', $this->getController().'@update');
	}

	public function testUpdateMissingPost()
	{
		$this->expectResponseMissing();

		$this->beAdmin()->put($this->getBasePath().'/0');
	}

	public function testUpdatePost(array $data = [])
	{
		$this->beAdmin()->put($this->getBasePath().'/1', $data);

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
		$this->beAdmin()->put($this->getBasePath().'/1');

		$this->assertRedirectedTo($this->getBasePath().'/1/edit');
		$this->assertNotice('error');
	}

	public function testUpdateInvalidPost(array $data = [])
	{
		$this->beAdmin()->put($this->getBasePath().'/1', $data);

		$this->assertRedirectedTo($this->getBasePath().'/1/edit');
		$this->assertNotice('error');
	}

	//==========================================================================
	// Destroy
	//==========================================================================

	public function testDestroyRouteExists()
	{
		$this->assertRouteExists('DELETE', $this->getBasePath().'/1');
		$this->assertRouteMatchesAction('DELETE', $this->getBasePath().'/1', $this->getController().'@destroy');
	}

	public function testDestroyGet()
	{
		$this->beAdmin()->delete($this->getBasePath().'/1');

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
		$this->expectResponseMissing();

		$this->beAdmin()->delete($this->getBasePath().'/0');
	}

	protected function displayErrors()
	{
		$errors = $this->app['session.store']->get('notice_error');
		if ($errors) {
			$this->assertSame([], $errors, 'There were errors...');
		}
	}
}