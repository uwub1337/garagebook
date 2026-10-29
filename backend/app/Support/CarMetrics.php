<?php

namespace App\Support;

use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CarMetrics
{
    /**
     * 平均油耗（km/L）：以相鄰兩次加油的里程差除以本次加油量。
     */
    public static function consumption(Car $car): ?float
    {
        return self::consumptionFromRecords(self::fuelRecords($car));
    }

    /**
     * @param  Collection<int, mixed>  $records
     * @return array{distance: int, liters: float}
     */
    public static function consumptionTotals(Collection $records): array
    {
        $records = $records->sortBy('mileage')->values();

        $distance = 0;
        $liters = 0.0;

        for ($i = 1; $i < $records->count(); $i++) {
            $delta = $records[$i]->mileage - $records[$i - 1]->mileage;
            if ($delta > 0) {
                $distance += $delta;
                $liters += (float) $records[$i]->liters;
            }
        }

        return [
            'distance' => $distance,
            'liters' => $liters,
        ];
    }

    /**
     * @param  Collection<int, mixed>  $records
     */
    public static function consumptionFromRecords(Collection $records): ?float
    {
        $totals = self::consumptionTotals($records);

        return $totals['liters'] > 0
            ? round($totals['distance'] / $totals['liters'], 2)
            : null;
    }

    /**
     * 即將到期（30 天內或 500 公里內）的保養提醒。
     */
    public static function reminders(Car $car): array
    {
        $today = Carbon::today();

        return self::maintenanceRecords($car)
            ->map(function ($record) use ($car, $today) {
                if (! $record->next_due_date && ! $record->next_due_mileage) {
                    return null;
                }

                $dueDate = $record->next_due_date?->copy()->startOfDay();
                $daysLeft = $dueDate
                    ? (int) floor(($dueDate->timestamp - $today->timestamp) / 86400)
                    : null;
                $kmLeft = $record->next_due_mileage !== null
                    ? (int) $record->next_due_mileage - (int) $car->current_mileage
                    : null;

                $isDue = ($daysLeft !== null && $daysLeft <= 30)
                    || ($kmLeft !== null && $kmLeft <= 500);

                if (! $isDue) {
                    return null;
                }

                return [
                    'id' => $record->id,
                    'car_id' => $car->id,
                    'car_name' => $car->name,
                    'item' => $record->item,
                    'due_date' => $record->next_due_date?->format('Y-m-d'),
                    'due_mileage' => $record->next_due_mileage,
                    'days_left' => $daysLeft,
                    'km_left' => $kmLeft,
                    'overdue' => ($daysLeft !== null && $daysLeft < 0)
                        || ($kmLeft !== null && $kmLeft < 0),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function summary(Car $car): array
    {
        $maintenanceCost = (float) self::maintenanceRecords($car)->sum('cost');
        $fuelCost = (float) self::fuelRecords($car)->sum('cost');

        return [
            'maintenance_cost' => round($maintenanceCost, 2),
            'fuel_cost' => round($fuelCost, 2),
            'total_cost' => round($maintenanceCost + $fuelCost, 2),
            'avg_consumption' => self::consumption($car),
            'reminders' => self::reminders($car),
        ];
    }

    /**
     * @return Collection<int, mixed>
     */
    private static function maintenanceRecords(Car $car): Collection
    {
        return $car->relationLoaded('maintenanceRecords')
            ? $car->maintenanceRecords
            : $car->maintenanceRecords()->get();
    }

    /**
     * @return Collection<int, mixed>
     */
    private static function fuelRecords(Car $car): Collection
    {
        return $car->relationLoaded('fuelRecords')
            ? $car->fuelRecords
            : $car->fuelRecords()->get();
    }
}
