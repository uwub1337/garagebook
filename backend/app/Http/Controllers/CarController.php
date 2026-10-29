<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Support\CarMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = $request->user()->cars()
            ->with(['maintenanceRecords', 'fuelRecords'])
            ->orderByDesc('created_at')
            ->get();

        return $cars->map(fn (Car $car) => array_merge(
            $car->withoutRelations()->toArray(),
            CarMetrics::summary($car),
        ))->values();
    }

    public function store(Request $request)
    {
        $this->validatePhoto($request);
        $car = $request->user()->cars()->create($this->validated($request));

        $this->replacePhoto($request, $car);

        return response()->json($car, 201);
    }

    public function show(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        $car->load(['maintenanceRecords', 'fuelRecords']);

        return response()->json([
            'car' => $car->withoutRelations(),
            'maintenance_records' => $car->maintenanceRecords->sortByDesc(['date', 'id'])->values(),
            'fuel_records' => $car->fuelRecords->sortByDesc(['date', 'id'])->values(),
            'stats' => CarMetrics::summary($car),
        ]);
    }

    public function update(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);
        $this->validatePhoto($request);
        $car->update($this->validated($request));
        $this->replacePhoto($request, $car);

        return $car->fresh();
    }

    public function destroy(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        if ($car->photo_path) {
            Storage::disk('uploads')->delete($car->photo_path);
        }

        $car->delete();

        return response()->noContent();
    }

    public function uploadPhoto(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        $this->validatePhoto($request, true);
        $this->replacePhoto($request, $car);

        return $car->fresh();
    }

    public function deletePhoto(Request $request, Car $car)
    {
        $this->authorizeCar($request, $car);

        if ($car->photo_path) {
            Storage::disk('uploads')->delete($car->photo_path);
            $car->update(['photo_path' => null]);
        }

        return $car->fresh();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'make' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'plate' => ['nullable', 'string', 'max:20'],
            'current_mileage' => ['required', 'integer', 'min:0', 'max:5000000'],
            'fuel_type' => ['required', 'in:gasoline,diesel,hybrid,electric'],
        ], [
            'name.required' => '請輸入車輛暱稱',
            'make.required' => '請輸入廠牌',
            'model.required' => '請輸入車型',
            'current_mileage.required' => '請輸入目前里程',
            'current_mileage.integer' => '里程必須是數字',
            'fuel_type.required' => '請選擇燃料種類',
            'fuel_type.in' => '燃料種類不正確',
        ]);
    }

    private function authorizeCar(Request $request, Car $car): void
    {
        abort_unless($car->user_id === $request->user()->id, 403, '無權存取此車輛');
    }

    private function replacePhoto(Request $request, Car $car): void
    {
        if (! $request->hasFile('photo')) {
            return;
        }

        $newPhotoPath = $request->file('photo')->store('cars', 'uploads');
        $oldPhotoPath = $car->photo_path;

        $car->update(['photo_path' => $newPhotoPath]);

        if ($oldPhotoPath) {
            Storage::disk('uploads')->delete($oldPhotoPath);
        }
    }

    private function validatePhoto(Request $request, bool $required = false): void
    {
        $request->validate([
            'photo' => [
                $required ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'photo.required' => '請選擇照片',
            'photo.image' => '檔案必須是圖片',
            'photo.mimes' => '圖片格式只支援 jpg、png、webp',
            'photo.max' => '圖片大小不能超過 5MB',
        ]);
    }
}
