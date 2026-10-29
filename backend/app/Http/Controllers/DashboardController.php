<?php

namespace App\Http\Controllers;

use App\Support\CarMetrics;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cars = $user->cars()
            ->with(['maintenanceRecords', 'fuelRecords'])
            ->orderBy('id')
            ->get();

        $maintenance = $cars->flatMap(fn ($car) => $car->maintenanceRecords);
        $fuel = $cars->flatMap(fn ($car) => $car->fuelRecords);

        $totalMaintenance = (float) $maintenance->sum('cost');
        $totalFuel = (float) $fuel->sum('cost');
        $now = Carbon::now();

        $monthCost = $maintenance->filter(fn ($r) => $r->date->isSameMonth($now))->sum('cost')
            + $fuel->filter(fn ($r) => $r->date->isSameMonth($now))->sum('cost');

        $consumptionTotals = $cars
            ->map(fn ($car) => CarMetrics::consumptionTotals($car->fuelRecords))
            ->reduce(
                fn (array $total, array $carTotals) => [
                    'distance' => $total['distance'] + $carTotals['distance'],
                    'liters' => $total['liters'] + $carTotals['liters'],
                ],
                ['distance' => 0, 'liters' => 0.0],
            );

        $monthly = collect(range(5, 0))->map(function (int $offset) use ($now, $maintenance, $fuel) {
            $month = $now->copy()->subMonths($offset);
            $key = $month->format('Y-m');

            return [
                'month' => $key,
                'label' => $month->format('n').'月',
                'maintenance' => round((float) $maintenance->filter(fn ($r) => $r->date->format('Y-m') === $key)->sum('cost'), 2),
                'fuel' => round((float) $fuel->filter(fn ($r) => $r->date->format('Y-m') === $key)->sum('cost'), 2),
            ];
        })->values();

        $reminders = $cars
            ->flatMap(fn ($car) => CarMetrics::reminders($car))
            ->sortBy(fn ($r) => [$r['overdue'] ? 0 : 1, $r['days_left'] ?? 99999, $r['km_left'] ?? 999999])
            ->values()
            ->all();

        return response()->json([
            'cars_count' => $cars->count(),
            'total_cost' => round($totalMaintenance + $totalFuel, 2),
            'month_cost' => round((float) $monthCost, 2),
            'total_maintenance_cost' => round($totalMaintenance, 2),
            'total_fuel_cost' => round($totalFuel, 2),
            'avg_consumption' => $consumptionTotals['liters'] > 0
                ? round($consumptionTotals['distance'] / $consumptionTotals['liters'], 2)
                : null,
            'monthly' => $monthly,
            'reminders' => $reminders,
            'cars' => $cars->map(function ($car) {
                $summary = CarMetrics::summary($car);

                return [
                    'id' => $car->id,
                    'name' => $car->name,
                    'make' => $car->make,
                    'model' => $car->model,
                    'year' => $car->year,
                    'plate' => $car->plate,
                    'current_mileage' => $car->current_mileage,
                    'fuel_type' => $car->fuel_type,
                    'photo_url' => $car->photo_url,
                    'total_cost' => $summary['total_cost'],
                    'avg_consumption' => $summary['avg_consumption'],
                    'reminder_count' => count($summary['reminders']),
                ];
            })->values(),
        ]);
    }
}
