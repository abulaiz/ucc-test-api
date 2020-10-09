<?php

class VehicleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_can_store_vehicle(){
        $data = [
            'plat_number' => 'D 2721 MD',
            'name' => 'Supra',
            'engine_displacement' => 700,
            'engine_displacement_unit' => 'centimeters',
            'engine_power' => 800,
            'engine_power_unit' => 'hoursepower',
            'price' => 20000,
            'location' => 'Bandung'
        ];

        $response = $this->post(route('v1.vehicle.store'), $data);
        
        return $response->seeJsonContains(['success' => true]);
    }

    public function test_can_get_list(){
        $response = $this->get(route('v1.vehicle.index'));
        return $response->seeJsonStructure([
            'next_page_url',
            'data'
        ]);       
    }
}
