<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\FuelRecord;
use Illuminate\Http\Request;

class FuelRecordController extends Controller
{
    public function index(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        return $car->fuelRecords()->orderByDesc('date')->orderByDesc('id')->get();
    }

    public function store(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        $data = $this->validated($request);
        $record = $car->fuelRecords()->create($data);
        $this->syncMileage($car, $data['mileage']);

        return response()->json($record, 201);
    }

    public function update(Request $request, FuelRecord $fuelRecord)
    {
        $this->authorizeRecord($request, $fuelRecord);

        $data = $this->validated($request);
        $fuelRecord->update($data);
        $this->syncMileage($fuelRecord->car, $data['mileage']);

        return $fuelRecord->fresh();
    }

    public function destroy(Request $request, FuelRecord $fuelRecord)
    {
        $this->authorizeRecord($request, $fuelRecord);
        $fuelRecord->delete();

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'mileage' => ['required', 'integer', 'min:0', 'max:5000000'],
            'liters' => ['required', 'numeric', 'min:0.01', 'max:2000'],
            'cost' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'date.required' => '請選擇加油日期',
            'mileage.required' => '請輸入加油時里程',
            'mileage.integer' => '里程必須是數字',
            'liters.required' => '請輸入加油量',
            'liters.numeric' => '加油量必須是數字',
            'cost.required' => '請輸入花費金額',
            'cost.numeric' => '花費金額必須是數字',
        ]);
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

    private function authorizeRecord(Request $request, FuelRecord $record): void
    {
        abort_unless($record->car->user_id === $request->user()->id, 403, '無權存取此紀錄');
    }
}
