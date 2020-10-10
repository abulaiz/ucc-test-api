<?php

namespace App\Models;

class Vehicle {

    public $accepted_engine_displacement = ['liters', 'centimeters', 'inches'];

    public $accepted_engine_power = ['hoursepower', 'kilowatts'];

    public $engine_displacement_alias = [
        'liters' => 'L', 'centimeters' => 'CC', 'inches' => 'CID'
    ];

    public $engine_power_alias = [
        'hoursepower' => 'HP', 'kilowatts' => 'kW'
    ];

    // 1 inches = 16.3871 centimeters = 0.0163871 liters
    public $displacementAggregation = [
        'inches' => 1, 'centimeters' => 16.3871, 'liters'=> 0.0163871
    ];

    // 1 hoursepower = 0.7457 kilowatts
    public $powerAggregation = [
        'hoursepower' => 1, 'kilowatts' => 0.7457
    ];

    public function calculateConvertion($value, $from, $to, $aggregation){
        if($aggregation[$from] != $aggregation[$to]){
            return round(($value/$aggregation[$from])*$aggregation[$to], 3);
        } else {
            return $value;
        }
    }

    public function displacementValue($initialValue, $initialUnit, $toUnit){
        return $this->calculateConvertion(
            $initialValue, $initialUnit, $toUnit, $this->displacementAggregation
        );
    }

    public function powerValue($initialValue, $initialUnit, $toUnit){
        return $this->calculateConvertion(
            $initialValue, $initialUnit, $toUnit, $this->powerAggregation
        );
    }

    public function mockUp($options, $data){
        $result = [];

        $engine_displacement = 'default';
        if(!empty($options->get('engine_displacement'))){
            if(in_array($options->get('engine_displacement'), $this->accepted_engine_displacement))
                $engine_displacement = $options->get('engine_displacement');
        }

        $engine_power = 'default';
        if(!empty($options->get('engine_power'))){
            if(in_array($options->get('engine_power'), $this->accepted_engine_power))
                $engine_power = $options->get('engine_power');        
        }

        foreach ($data as $index => $item) {
            $result[] = (array)$item;

            // Get valid engine_displacement and enginer_power measurement unit
            $displacement_unit = $engine_displacement;
            $power_unit = $engine_power;
            if($displacement_unit == 'default')
                 $displacement_unit = $item->engine_displacement_unit;
            if($power_unit == 'default')
                $power_unit = $item->engine_power_unit;

            // Set value for engine_displacement and engine_power
            $result[$index]['engine_displacement'] = $this->displacementValue(
                $item->engine_displacement, $item->engine_displacement_unit, $displacement_unit
            );
            $result[$index]['engine_power'] = $this->powerValue(
                $item->engine_power, $item->engine_power_unit, $power_unit
            );

            // Set aliase for measurement unit
            $result[$index]['engine_displacement_unit'] = $this->engine_displacement_alias[$displacement_unit];
            $result[$index]['engine_power_unit'] = $this->engine_power_alias[$power_unit];            
        }

        return $result;
    }
}

