<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GarageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_a_distance_weighted_fuel_consumption_average(): void
    {
        $user = User::factory()->create();
        $firstCar = $this->createCar($user, '第一台車');
        $secondCar = $this->createCar($user, '第二台車');

        $firstCar->fuelRecords()->create(['date' => '2026-01-01', 'mileage' => 1000, 'liters' => 10, 'cost' => 300]);
        $firstCar->fuelRecords()->create(['date' => '2026-01-02', 'mileage' => 1100, 'liters' => 10, 'cost' => 300]);
        $secondCar->fuelRecords()->create(['date' => '2026-01-01', 'mileage' => 1000, 'liters' => 10, 'cost' => 300]);
        $secondCar->fuelRecords()->create(['date' => '2026-01-02', 'mileage' => 2000, 'liters' => 50, 'cost' => 1500]);

        Sanctum::actingAs($user);

        $this->getJson('/api/stats')
            ->assertOk()
            ->assertJsonPath('avg_consumption', 18.33);
    }

    public function test_users_cannot_access_another_users_car(): void
    {
        $owner = User::factory()->create();
        $car = $this->createCar($owner, '私人車輛');
        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $this->getJson("/api/cars/{$car->id}")
            ->assertForbidden();
    }

    public function test_car_details_and_a_photo_are_saved_in_one_request(): void
    {
        Storage::fake('uploads');
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post('/api/cars', [
            'name' => '旅行車',
            'make' => 'Toyota',
            'model' => 'Yaris',
            'current_mileage' => 1234,
            'fuel_type' => 'gasoline',
            'photo' => UploadedFile::fake()->image('car.jpg'),
        ])->assertCreated();

        $car = Car::query()->sole();

        $this->assertSame(1234, $car->current_mileage);
        $this->assertNotNull($car->photo_path);
        Storage::disk('uploads')->assertExists($car->photo_path);
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $payload = ['email' => 'unknown@example.com', 'password' => 'not-the-password'];

        foreach (range(1, 5) as $attempt) {
            $this->postJson('/api/auth/login', $payload)->assertUnprocessable();
        }

        $this->postJson('/api/auth/login', $payload)->assertTooManyRequests();
    }

    private function createCar(User $user, string $name): Car
    {
        return $user->cars()->create([
            'name' => $name,
            'make' => 'Toyota',
            'model' => 'Yaris',
            'current_mileage' => 0,
            'fuel_type' => 'gasoline',
        ]);
    }
}
