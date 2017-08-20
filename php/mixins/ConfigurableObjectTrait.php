<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

namespace common\csv\mixins;

/**
 * The trait provides methods to configure object from array.
 */
trait ConfigurableObjectTrait
{
    /**
     * Assigns specified properties.
     *
     * @param array $config
     */
    protected function _configureFrom($config = []): void
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
    }

}