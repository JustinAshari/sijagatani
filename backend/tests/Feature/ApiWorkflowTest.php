<?php

namespace Tests\Feature;

use App\Models\Penggilingan;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(string $role, array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => $overrides['name'] ?? ucfirst($role) . ' User',
            'username' => $overrides['username'] ?? $role . '_' . uniqid(),
            'password' => $overrides['password'] ?? 'password123',
            'role' => $role,
            'nama_penggilingan' => $overrides['nama_penggilingan'] ?? ($role === 'penggilingan' ? 'Penggilingan Utama' : null),
            'parent_id' => $overrides['parent_id'] ?? null,
        ], $overrides));
    }

    protected function petaniPayload(array $overrides = []): array
    {
        return array_merge([
            'tanggal' => now()->toDateString(),
            'nik' => '1234567890123456',
            'nama' => 'Petani Uji',
            'alamat' => 'Alamat Uji',
            'no_telepon' => '08123456789',
            'luas_lahan' => 1.25,
            'alamat_lahan' => 'Sawah Uji',
            'potensi_panen' => 500,
            'komoditi' => 'Gabah',
            'bank' => 'BRI',
            'no_rekening' => '1234567890',
        ], $overrides);
    }

    public function test_guest_cannot_access_protected_api_route(): void
    {
        $this->getJson('/api/petani')
            ->assertStatus(401);
    }

    public function test_admin_can_crud_petani_with_upload_and_verification(): void
    {
        Storage::fake('public');

        $admin = $this->makeUser('admin');
        Sanctum::actingAs($admin);

        $createResponse = $this->post('/api/petani', array_merge($this->petaniPayload([
            'nik' => '1111222233334444',
            'nama' => 'Petani Baru',
        ]), [
            'foto_ktp' => UploadedFile::fake()->image('ktp.jpg', 1200, 800),
        ]));

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Petani Baru');

        $petaniId = $createResponse->json('data.id');
        $fotoKtpPath = $createResponse->json('data.foto_ktp');

        $this->assertNotNull($fotoKtpPath);
        Storage::disk('public')->assertExists($fotoKtpPath);
        $this->assertDatabaseHas('petani', [
            'id' => $petaniId,
            'nik' => '1111222233334444',
            'nama' => 'Petani Baru',
        ]);

        $updateResponse = $this->putJson('/api/petani/' . $petaniId, $this->petaniPayload([
            'nik' => '1111222233334444',
            'nama' => 'Petani Diupdate',
            'luas_lahan' => 2.5,
            'potensi_panen' => 750,
        ]));

        $updateResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Petani Diupdate');

        $this->assertDatabaseHas('petani', [
            'id' => $petaniId,
            'nama' => 'Petani Diupdate',
        ]);

        $verifyResponse = $this->postJson('/api/petani/' . $petaniId . '/verifikasi', [
            'status_verifikasi' => 'disetujui',
            'catatan_verifikasi' => 'Data lengkap',
        ]);

        $verifyResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status_verifikasi', 'disetujui');

        $this->assertDatabaseHas('petani', [
            'id' => $petaniId,
            'status_verifikasi' => 'disetujui',
            'verified_by' => $admin->id,
        ]);

        $deleteResponse = $this->deleteJson('/api/petani/' . $petaniId);

        $deleteResponse->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('petani', [
            'id' => $petaniId,
        ]);
    }

    public function test_admin_can_create_and_verify_penggilingan_with_transports(): void
    {
        $admin = $this->makeUser('admin');
        Sanctum::actingAs($admin);

        $petani = Petani::create([
            'tanggal' => now()->toDateString(),
            'nik' => '5555666677778888',
            'nama' => 'Petani Dasar',
            'alamat' => 'Alamat Dasar',
            'luas_lahan' => 1.00,
            'alamat_lahan' => 'Lahan Dasar',
            'potensi_panen' => 400,
            'komoditi' => 'Gabah',
        ]);

        $createResponse = $this->postJson('/api/penggilingan', [
            'tanggal_pengajuan' => now()->toDateString(),
            'nama_penggilingan' => 'Penggilingan Uji',
            'lokasi_makloon' => 'Gudang Uji',
            'transports' => [
                [
                    'nama_pengemudi' => 'Driver Uji',
                    'nopol' => 'AB1234CD',
                    'kuantum' => 1.5,
                ],
            ],
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_penggilingan', 'Penggilingan Uji');

        $penggilinganId = $createResponse->json('data.id');

        $this->assertDatabaseHas('penggilingan', [
            'id' => $penggilinganId,
            'nama_penggilingan' => 'Penggilingan Uji',
            'lokasi_makloon' => 'Gudang Uji',
        ]);

        $this->assertDatabaseHas('penggilingan_transport', [
            'penggilingan_id' => $penggilinganId,
            'urutan' => 1,
            'nama_pengemudi' => 'Driver Uji',
            'nopol' => 'AB1234CD',
        ]);

        $verifyResponse = $this->postJson('/api/penggilingan/' . $penggilinganId . '/verifikasi', [
            'status_verifikasi' => 'disetujui',
            'catatan_verifikasi' => 'Siap diproses',
        ]);

        $verifyResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status_verifikasi', 'disetujui');

        $this->assertDatabaseHas('penggilingan', [
            'id' => $penggilinganId,
            'status_verifikasi' => 'disetujui',
            'verified_by' => $admin->id,
        ]);
    }

    public function test_role_access_limits_and_scopes_penggilingan_data(): void
    {
        $petani = Petani::create([
            'tanggal' => now()->toDateString(),
            'nik' => '9999000011112222',
            'nama' => 'Petani Scope',
            'alamat' => 'Alamat Scope',
            'luas_lahan' => 1.00,
            'alamat_lahan' => 'Lahan Scope',
            'potensi_panen' => 300,
            'komoditi' => 'Gabah',
        ]);

        Penggilingan::create([
            'tanggal_pengajuan' => now()->toDateString(),
            'nama_penggilingan' => 'Scope A',
            'lokasi_makloon' => 'Lokasi A',
            'total_tonase' => 1.25,
            'jumlah_angkutan' => 1,
        ]);

        Penggilingan::create([
            'tanggal_pengajuan' => now()->toDateString(),            
            'nama_penggilingan' => 'Scope B',
            'lokasi_makloon' => 'Lokasi B',
            'total_tonase' => 2.50,
            'jumlah_angkutan' => 1,
        ]);

        $penggilinganUser = $this->makeUser('penggilingan', [
            'nama_penggilingan' => 'Scope A',
        ]);
        Sanctum::actingAs($penggilinganUser);

        $this->getJson('/api/penggilingan')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/petani')
            ->assertForbidden();

        $this->getJson('/api/users')
            ->assertForbidden();
    }

    public function test_parent_penggilingan_can_manage_own_sub_admins(): void
    {
        $parent = $this->makeUser('penggilingan', [
            'name' => 'Parent Penggilingan',
            'username' => 'parent_penggilingan',
            'nama_penggilingan' => 'Penggilingan Induk',
            'parent_id' => null,
        ]);

        Sanctum::actingAs($parent);

        $createResponse = $this->postJson('/api/my-sub-admins', [
            'name' => 'Sub Admin Satu',
            'username' => 'subadmin_satu',
            'password' => 'password123',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Sub Admin Satu')
            ->assertJsonPath('data.parent_id', $parent->id)
            ->assertJsonPath('data.nama_penggilingan', 'Penggilingan Induk');

        $subAdminId = $createResponse->json('data.id');

        $this->assertDatabaseHas('users', [
            'id' => $subAdminId,
            'parent_id' => $parent->id,
            'nama_penggilingan' => 'Penggilingan Induk',
            'role' => 'penggilingan',
        ]);

        $indexResponse = $this->getJson('/api/my-sub-admins');

        $indexResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $subAdminId);

        $updateResponse = $this->putJson('/api/my-sub-admins/' . $subAdminId, [
            'name' => 'Sub Admin Diupdate',
            'username' => 'subadmin_dua',
            'password' => 'password456',
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Sub Admin Diupdate')
            ->assertJsonPath('data.username', 'subadmin_dua');

        $this->assertDatabaseHas('users', [
            'id' => $subAdminId,
            'name' => 'Sub Admin Diupdate',
            'username' => 'subadmin_dua',
            'parent_id' => $parent->id,
        ]);

        $deleteResponse = $this->deleteJson('/api/my-sub-admins/' . $subAdminId);

        $deleteResponse->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', [
            'id' => $subAdminId,
        ]);
    }

    public function test_sub_admin_cannot_access_sub_admin_management_routes(): void
    {
        $parent = $this->makeUser('penggilingan', [
            'name' => 'Parent Penggilingan',
            'username' => 'parent_penggilingan_b',
            'nama_penggilingan' => 'Penggilingan Scope',
            'parent_id' => null,
        ]);

        $subAdmin = $this->makeUser('penggilingan', [
            'name' => 'Sub Admin',
            'username' => 'subadmin_scope',
            'nama_penggilingan' => 'Penggilingan Scope',
            'parent_id' => $parent->id,
        ]);

        Sanctum::actingAs($subAdmin);

        $this->getJson('/api/my-sub-admins')
            ->assertForbidden();

        $this->postJson('/api/my-sub-admins', [
            'name' => 'Tidak Boleh',
            'username' => 'tidak_boleh',
            'password' => 'password123',
        ])->assertForbidden();

        $this->putJson('/api/my-sub-admins/999999', [
            'name' => 'Tidak Boleh',
            'username' => 'tidak_boleh',
            'password' => 'password123',
        ])->assertForbidden();

        $this->deleteJson('/api/my-sub-admins/999999')
            ->assertForbidden();
    }
}