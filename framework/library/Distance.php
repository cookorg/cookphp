<?php

namespace library;

use Exception;

/**
 * GPS距离
 */
class Distance {

    /**
     * The formula used for distance calculation.
     *
     * @var string
     */
    private $formula = 'vincenty';

    /**
     * The unit used to return the distance in. The supported units are shown in the conversion table below.
     *
     * @var string
     */
    private $unit = 'km';

    /**
     * An array holding the conversion table from meters to several other units.
     *
     * @var array
     */
    private $conversion = [
        'cm' => '100', // centimeters
        'in' => '39.3700787', // inches
        'ft' => '3.2808399', // feet
        'm' => '1', // meters
        'km' => '0.001', // kilometers
        'mi' => '0.000621371192', // miles
    ];

    /**
     * Method that returns the distance between two GPS locations in the preferred unit according to the set formula.
     *
     * @param float $latitudeA The latitude for point A.
     * @param float $longitudeA The longitude for point A.
     * @param float $latitudeB The latitude for point B.
     * @param float $longitudeB The longitude for point B.
     *
     * @uses betweenVincenty
     * @uses betweenHaversine
     * @throws Exception
     * @return float
     */
    public function between($latitudeA, $longitudeA, $latitudeB, $longitudeB) {
        if (!floatval($latitudeA) || !floatval($longitudeA) || !floatval($latitudeB) || !floatval($longitudeB)) {
            throw new Exception('One or more of the parsed variables are not valid floats.');
        }
        $distanceInMeters = call_user_func_array([$this, 'between' . ucfirst($this->getFormula())], [$latitudeA, $longitudeA, $latitudeB, $longitudeB]);
        return $this->convert($distanceInMeters);
    }

    /**
     * Method that returns the distance between two GPS locations in meters according to the Vincenty formula.
     *
     * @param float $latitudeA The latitude for point A.
     * @param float $longitudeA The longitude for point A.
     * @param float $latitudeB The latitude for point B.
     * @param float $longitudeB The longitude for point B.
     *
     * @return float
     */
    private function betweenVincenty($latitudeA, $longitudeA, $latitudeB, $longitudeB) {
        $latitudeA = deg2rad($latitudeA);
        $longitudeA = deg2rad($longitudeA);
        $latitudeB = deg2rad($latitudeB);
        $longitudeB = deg2rad($longitudeB);
        $longDelta = $longitudeB - $longitudeA;
        $a = pow(cos($latitudeB) * sin($longDelta), 2) + pow(cos($latitudeA) * sin($latitudeB) - sin($latitudeA) * cos($latitudeB) * cos($longDelta), 2);
        $b = sin($latitudeA) * sin($latitudeB) + cos($latitudeA) * cos($latitudeB) * cos($longDelta);
        $angle = atan2(sqrt($a), $b);
        return floor($angle * 6371000);
    }

    /**
     * Method that returns the distance between two GPS locations in meters according to the Haversine formula.
     *
     * @param float $latitudeA The latitude for point A.
     * @param float $longitudeA The longitude for point A.
     * @param float $latitudeB The latitude for point B.
     * @param float $longitudeB The longitude for point B.
     *
     * @return float
     */
    private function betweenHaversine($latitudeA, $longitudeA, $latitudeB, $longitudeB) {
        $latitudeA = deg2rad($latitudeA);
        $longitudeA = deg2rad($longitudeA);
        $latitudeB = deg2rad($latitudeB);
        $longitudeB = deg2rad($longitudeB);
        $latDelta = $latitudeB - $latitudeA;
        $longDelta = $longitudeB - $longitudeA;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latitudeA) * cos($latitudeB) * pow(sin($longDelta / 2), 2)));
        return floor($angle * 6371000);
    }

    /**
     * @param $distance
     * @return mixed
     */
    private function convert($distance) {
        return $distance * $this->getConversion()[$this->getUnit()];
    }

    /**
     * Returns the currently set formula used for distance calculation.
     *
     * @return string
     */
    public function getFormula() {
        return $this->formula;
    }

    /**
     * Sets the formula to be used for distance calculation.
     *
     * @param string $formula
     *
     * @throws Exception
     */
    public function setFormula($formula) {
        if (!in_array($formula, ['vincenty', 'haversine'])) {
            throw new Exception('You have tried to set an invalid distance formula.');
        }
        $this->formula = $formula;
    }

    /**
     * Returns the currently set unit used when returning the distance between two coordinates.
     *
     * @return string
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * Sets the unit to be used when returning the distance between two coordinates.
     *
     * @param string $unit
     *
     * @throws Exception
     */
    public function setUnit($unit) {
        if (!in_array($unit, array_keys($this->getConversion()))) {
            throw new Exception('You have tried to set an invalid distance unit.');
        }
        $this->unit = $unit;
        return $this;
    }

    /**
     * Gets the conversion table between different units.
     *
     * @return array
     */
    public function getConversion() {
        return $this->conversion;
    }

}
