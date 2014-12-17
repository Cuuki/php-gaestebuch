<?php

namespace Guestbook;

class AbstractClassAction
{

    /**
     * Get Value of passed property
     */
    public function get ( $property )
    {
        return $this->$property;
    }

    /**
     * Set Value of passed property
     * @return object
     */
    public function set ( $property, $value )
    {
        $this->$property = $value;

        return $this;
    }

    /**
     * Iterate all Protected/Public properties
     * @return object
     */
    protected function iterateProperties ()
    {
        foreach ( $this as $key => $value )
        {
            $properties[$key] = $value;
        }

        return $properties;
    }

}
