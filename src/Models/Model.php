<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

use Carbon\Carbon;

class Model {

    /**
     * Properties which should be converted to booleans.
     *
     * @var array
     */
    protected $booleans = [];

    /**
     * Properties which should be converted to Carbon dates.
     *
     * @var array
     */
    protected $dates = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    protected function fill($attributes)
    {
        foreach ($attributes as $key => $value)
        {
            $this->setProperty($key, $value);
        }
    }

    public function __get($key)
    {
        // If the attribute has a get mutator, we will call that then return what
		// it returns as the value, which is useful for transforming values on
		// retrieval from the model to a form that is more useful for usage.
		if ($this->hasGetMutator($key))
		{
			return $this->mutateAttribute($key);
		}

		return $this->getProperty($key);
    }

    /**
	 * Determine if a get mutator exists for an attribute.
	 *
	 * @param  string  $key
	 * @return bool
	 */
    private function hasGetMutator($key)
    {
        return method_exists($this, 'get'.$this->studlyCase($key).'Attribute');
    }

    /**
	 * Get the value of an attribute using its mutator.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function mutateAttribute($key)
	{
		return $this->{'get'.$this->studlyCase($key).'Attribute'}();
	}

    /**
     * Converts value to studly case.
     *
     * @param string $value
     */
    private function studlyCase($value)
    {
       $value = ucwords(str_replace(array('-', '_'), ' ', $value));

       return str_replace(' ', '', $value);
    }

    public function getProperty($key)
    {
        if ( ! property_exists($this, $key))
        {
            return null;
        }

        return $this->$key;
    }

    protected function setProperty($key, $value)
    {
        if ( ! property_exists($this, $key))
        {
            return;
        }

        if ($this->isBooleanProperty($key))
        {
            $this->setBooleanProperty($key, $value);
            return;
        }

        if ($this->isDateProperty($key))
        {
            $this->setDateProperty($key, $value);
            return;
        }

        $this->$key = $value;
    }

    protected function isBooleanProperty($key)
    {
        return (in_array($key, $this->booleans));
    }

    protected function setBooleanProperty($key, $value)
    {
        $this->$key = (bool) $value;
    }

    protected function isDateProperty($key)
    {
        return (in_array($key, $this->dates));
    }

    protected function setDateProperty($key, $value)
    {
        if ( ! empty($value) && '0000-00-00' !== $value)
        {
            $this->$key = Carbon::parse($value);
        }
    }
}
