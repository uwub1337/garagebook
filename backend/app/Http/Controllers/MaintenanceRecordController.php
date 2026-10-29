<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        return $car->maintenanceRecords()->orderByDesc('date')->orderByDesc('id')->get();
    }

    public function store(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        $data = $this->validated($request);
        $record = $car->maintenanceRecords()->create($data);
        $this->syncMileage($car, $data['mileage']);

        return response()->json($record, 201);
    }

    public function update(Request $request, MaintenanceRecord $maintenanceRecord)
    {
        $this->authorizeRecord($request, $maintenanceRecord);

        $data = $this->validated($request);
        $maintenanceRecord->update($data);
        $this->syncMileage($maintenanceRecord->car, $data['mileage']);

        return $maintenanceRecord->fresh();
    }

    public function destroy(Request $request, MaintenanceRecord $maintenanceRecord)
    {
        $this->authorizeRecord($request, $maintenanceRecord);
        $maintenanceRecord->delete();

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'item' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'mileage' => ['required', 'integer', 'min:0', 'max:5000000'],
            'cost' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'next_due_date' => ['nullable', 'date'],
            'next_due_mileage' => ['nullable', 'integer', 'min:0', 'max:5000000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'item.required' => '請輸入保養項目',
            'date.required' => '請選擇保養日期',
            'mileage.required' => '請輸入保養時里程',
            'mileage.integer' => '里程必須是數字',
            'cost.required' => '請輸入花費金額',
            'cost.numeric' => '花費金額必須是數字',
        ]);

        $data['next_due_date'] = $data['next_due_date'] ?? null;
        $data['next_due_mileage'] = $data['next_due_mileage'] ?? null;

        return $data;
    }

    private function syncMileage(Car $car, int $mileage): void
    {
        if ($mileage > $car->current_mileage) {
            $car->update(['current_mileage' => $mileage]);
        }
    }

    private function authorizeCar(Request $request, Car $car): void
    {
        abort_unless($car->user_id === $request->user()->id, 403, '無權存取此車輛');
    }

    private function authorizeRecord(Request $request, MaintenanceRecord $record): void
    {
        abort_unless($record->car->user_id === $request->user()->id, 403, '無權存取此紀錄');
    }
}
