<?php

namespace App\Services;

class GpsService
{
    /**
     * Calculate the distance between two GPS coordinates using the Haversine formula.
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in meters
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if a GPS coordinate is within the allowed radius of the office.
     *
     * @param float $lat User latitude
     * @param float $lon User longitude
     * @param float $officeLat Office latitude
     * @param float $officeLon Office longitude
     * @param int $radiusMeters Allowed radius in meters
     * @return bool
     */
    public static function isWithinRadius(float $lat, float $lon, float $officeLat, float $officeLon, int $radiusMeters): bool
    {
        $distance = self::calculateDistance($lat, $lon, $officeLat, $officeLon);
        return $distance <= $radiusMeters;
    }

    /**
     * Get the distance from the office in a human readable format.
     */
    public static function getDistanceFromOffice(float $lat, float $lon, float $officeLat, float $officeLon): string
    {
        $distance = self::calculateDistance($lat, $lon, $officeLat, $officeLon);

        if ($distance < 1000) {
            return round($distance) . ' meter';
        }

        return round($distance / 1000, 1) . ' km';
    }
}
