<?php
class Janrain_Exception extends Exception
{
	/**
	 * Holds the context of the exception.
	 * 
	 * @var array
	 */
	protected $_context;
	
	/**
	 * Set the context of the exception.
	 * <code>
	 * $context = get_defined_vars();
	 * $ex = new Cv_Exception('This and this went wrong', 100);
	 * $ex->setContext($context);
	 * throw $ex;
	 * <code>
	 * 
	 * @param array $context
	 * @return Cv_Exception
	 */
	public function setContext(array $context)
	{
		$this->_context = $context;
		return $this;
	}
	
	/**
	 * Get the context of the exception.
	 * 
	 * @return array
	 */
	public function getContext()
	{
		return $this->_context;
	}
}