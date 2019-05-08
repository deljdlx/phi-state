<?php

namespace Phi\State;


use Phi\Event\Event;
use Phi\Event\Traits\Listenable;

class State
{

    use Listenable;


    /**
     * @var Dimension[]
     */
    protected $dimensions;


    /**
     * @var DimensionValue[]
     */
    private $values = array();



    public function addDimension(Dimension $dimension)
    {
        $this->dimensions[$dimension->getName()] = $dimension;
        $this->setDimensionValue($dimension->getName(), null);
        return $this;
    }

    public function addDimensionEventListener($dimensionName, $eventName, $callback, $listenerName = null)
    {
        $this->getDimensionValueObject($dimensionName)->addEventListener(
            $eventName,
            $callback,
            $listenerName
        );

        return $this;
    }


    public function getDimensions()
    {
        return $this->dimensions;
    }



    public function getDimensionValue($name)
    {
        return $this->getDimensionValueObject($name)->getValue();
    }


    /**
     * @param $name
     * @return DimensionValue
     */
    public function getDimensionValueObject($name)
    {
        $dimension = $this->getDimension($name);

        if(array_key_exists($name, $this->values)) {
            return $this->values[$dimension->getName()];
        }
        else {

            $dimension = $this->getDimension($name);
            $valueObject= new DimensionValue(
                $dimension,
                null
            );

            $self = $this;
            $valueObject->addEventListener(
                get_class($valueObject).'.'.DimensionValue::EVENT_CHANGE,
                function($event) use ($self) {
                    $self->relayEvent($event);
                });

            $this->values[$dimension->getName()] = $valueObject;

            return $this->values[$dimension->getName()];
        }
    }



    protected function relayEvent(Event $event)
    {
        $event->setVariable('state', $this);
        $this->fireEvent($event);
        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getDimension($name)
    {
        if(array_key_exists($name, $this->dimensions)) {
            return $this->dimensions[$name];
        }

        throw new Exception('Dimension '.$name.' does not exist');
    }


    public function setDimensionValue($dimentionName, $value)
    {

        $valueObject = $this->getDimensionValueObject($dimentionName);
        $valueObject->setValue($value);



        $this->values[$valueObject->getDimension()->getName()] = $valueObject;
        return $this;
    }



}



