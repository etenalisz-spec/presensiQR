<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pertemuan;
use App\Models\Mahasiswa;
use App\Models\Presensi;

use Illuminate\Foundation\Testing\RefreshDatabase;

class PresensiSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Universitas Pamulang');
    }

    public function test_mahasiswa_can_login_and_see_courses()
    {
        $user = User::where('username', '2211001')->first();
        $response = $this->actingAs($user)->get('/mahasiswa/presensi');
        
        $response->assertStatus(200);
        $response->assertSee('BAHASA QUERY TERSTRUKTUR');
        $response->assertSee('05SIFE001');
    }

    public function test_mahasiswa_can_view_14_pertemuan()
    {
        $user = User::where('username', '2211001')->first();
        $mhs = $user->mahasiswa;
        $jadwal = $mhs->kelas->jadwalKuliahs->first();

        $response = $this->actingAs($user)->get("/mahasiswa/presensi/{$jadwal->id}");
        $response->assertStatus(200);
        $response->assertSee('SCAN QR');
        $response->assertSee('Pertemuan Ke - 1');
        $response->assertSee('Pertemuan Ke - 21');
    }

    public function test_dosen_can_login_and_manage_courses()
    {
        $user = User::where('username', 'dosen')->first();
        $response = $this->actingAs($user)->get('/dosen/matakuliah');

        $response->assertStatus(200);
        $response->assertSee('Gusmayeni, S.Kom., M.Kom.');
    }

    public function test_dosen_can_generate_qr_code()
    {
        $user = User::where('username', 'dosen')->first();
        $pertemuan = Pertemuan::where('is_open', true)->first();

        $response = $this->actingAs($user)->post("/dosen/pertemuan/{$pertemuan->id}/generate-qr");
        $response->assertRedirect("/dosen/pertemuan/{$pertemuan->id}/qr");

        $pertemuan->refresh();
        $this->assertNotNull($pertemuan->qr_token);
        $this->assertStringStartsWith('UNPAM-', $pertemuan->qr_token);
    }

    public function test_mahasiswa_can_scan_qr_and_record_attendance()
    {
        $user = User::where('username', '2211001')->first();
        $mhs = $user->mahasiswa;
        $pertemuan = Pertemuan::where('is_open', true)->first();
        $pertemuan->update([
            'qr_token' => 'UNPAM-TEST-TOKEN',
            'qr_expires_at' => now()->addMinutes(20),
        ]);

        $response = $this->actingAs($user)->postJson('/mahasiswa/scan/proses', [
            'pertemuan_id' => $pertemuan->id,
            'qr_token' => 'UNPAM-TEST-TOKEN',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $presensi = Presensi::where('pertemuan_id', $pertemuan->id)->where('mahasiswa_id', $mhs->id)->first();
        $this->assertEquals('Hadir', $presensi->status);
        $this->assertEquals('QR Scan', $presensi->metode);
    }

    public function test_admin_pages_and_import()
    {
        $admin = User::where('username', 'admin')->first();

        // Check pages
        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/akun')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/kelas')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/mahasiswa')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/dosen')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/matakuliah-jadwal')->assertStatus(200);

        // Test drag-and-drop batch import
        $payload = [
            'data' => [
                ['kode_mk' => 'MKTEST99', 'nama_mk' => 'Mata Kuliah Uji Coba', 'sks' => 3, 'semester' => 5]
            ]
        ];
        $resp = $this->actingAs($admin)->postJson('/admin/import/matakuliah', $payload);
        $resp->assertStatus(200);
        $resp->assertJson(['success' => true]);
    }

    public function test_admin_matakuliah_jadwal_hierarchy()
    {
        $admin = User::where('username', 'admin')->first();
        $mk = \App\Models\MataKuliah::first();
        $dosen = \App\Models\Dosen::first();

        // Step 1: List all courses
        $this->actingAs($admin)->get('/admin/matakuliah-jadwal')->assertStatus(200)->assertSee('Pilih Mata Kuliah');

        // Step 2: List lecturers for selected course
        $this->actingAs($admin)->get("/admin/matakuliah-jadwal?mk_id={$mk->id}")->assertStatus(200)->assertSee('Pilih Dosen Pengampu');

        // Step 3: Full detail description for selected course and lecturer
        $this->actingAs($admin)->get("/admin/matakuliah-jadwal?mk_id={$mk->id}&dosen_id={$dosen->id}")->assertStatus(200)->assertSee('DETAIL JADWAL KULIAH');
    }

    public function test_dosen_qr_duration_and_akhiri()
    {
        $user = User::where('username', 'dosen')->first();
        $pertemuan = Pertemuan::where('is_open', true)->first();

        // Generate QR with custom duration 10 mins
        $response = $this->actingAs($user)->post("/dosen/pertemuan/{$pertemuan->id}/generate-qr", [
            'durasi_menit' => 10,
        ]);
        $response->assertRedirect("/dosen/pertemuan/{$pertemuan->id}/qr");

        $pertemuan->refresh();
        $this->assertNotNull($pertemuan->qr_token);
        $this->assertTrue(now()->diffInMinutes($pertemuan->qr_expires_at) <= 10);

        // Akhiri QR langsung
        $respEnd = $this->actingAs($user)->post("/dosen/pertemuan/{$pertemuan->id}/akhiri-qr");
        $respEnd->assertRedirect("/dosen/jadwal/{$pertemuan->jadwal_kuliah_id}/pertemuan");

        $pertemuan->refresh();
        $this->assertTrue(now()->isAfter($pertemuan->qr_expires_at));
    }

    public function test_login_redirects_directly_to_dashboard()
    {
        // Dosen login redirect
        $respDosen = $this->post('/login', [
            'username' => 'dosen',
            'password' => 'dosen123',
        ]);
        $respDosen->assertRedirect('/dosen');

        // Logout
        $this->post('/logout');

        // Mahasiswa login redirect
        $respMhs = $this->post('/login', [
            'username' => '2211001',
            'password' => 'mhs12345',
        ]);
        $respMhs->assertRedirect('/mahasiswa');
    }

    public function test_dosen_rekap_and_student_views()
    {
        $dosen = User::where('username', 'dosen')->first();
        $this->actingAs($dosen)->get('/dosen')->assertStatus(200);
        $this->actingAs($dosen)->get('/dosen/data-mahasiswa')->assertStatus(200);
        $this->actingAs($dosen)->get('/dosen/rekap')->assertStatus(200);
    }

    public function test_mahasiswa_dashboard()
    {
        $user = User::where('username', '2211001')->first();
        $response = $this->actingAs($user)->get('/mahasiswa');
        $response->assertStatus(200);
        $response->assertSee('DASHBOARD MAHASISWA');
        $response->assertSee('Jumlah Kehadiran');
    }
}
