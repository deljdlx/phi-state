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


        $valueObject = new DimensionValue(
            $dimension,
            null
        );


        $self = $this;
        $valueObject->addEventListener(
            DimensionValue::EVENT_CHANGE,
            function ($event) use ($self) {
                $self->relayChangeDimensionValueEvent($event);
            }
        );

        $this->values[$dimension->getName()] = $valueObject;


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

        if (array_key_exists($name, $this->values)) {
            return $this->values[$dimension->getName()];
        }
        else {

        }
    }


    /**
     * @param Event $event
     * @return $this
     */
    protected function relayChangeDimensionValueEvent(Event $event)
    {

        $event->setVariable('state', $this);
        $this->fireEvent($event);
        return $this;
    }

    /**
     * @return DimensionValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $name
     * @return Dimension
     */
    public function getDimension($name)
    {
        if (array_key_exists($name, $this->dimensions)) {
            return $this->dimensions[$name];
        }

        throw new Exception('Dimension ' . $name . ' does not exist');
    }


    public function setDimensionValue($dimentionName, $value)
    {

        $valueObject = $this->getDimensionValueObject($dimentionName);
        $valueObject->setValue($value);


        $this->values[$valueObject->getDimension()->getName()] = $valueObject;
        return $this;
    }


}



