<?php

namespace App\Services;
use App\Models\BiodataRef;
use Illuminate\Support\Facades\Validator;

class BiodataRefService extends Service
{
    protected $biodataRefModel;

    public function __construct(BiodataRef $biodataRefModel)
    {
        $this->biodataRefModel = $biodataRefModel;
    }

    public function getDataBiodataRefJson()
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $biodataRefData = $this->biodataRefModel->getBiodataRef();
        if(!$biodataRefData->isEmpty()) {
            foreach ($biodataRefData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.biodata_ref.view.id', ['idBiodataRef' => $value->id]) .'" data-url-update="'. route('data_master.biodata_ref.update', ['idBiodataRef' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.biodata_ref.delete', ['idBiodataRef' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->biodata_ref_name,
                    $status,
                    $btn
                ];
            }
        }

        return $this->responseJson([
            'status' => true,
            'data' => $data,
            'message' => 'status ok',
        ], 200);
    }

    public function getDataBiodataRefJsonByIdBiodataRef($idBiodataRef)
    {
        try {
            $biodataRefData = $this->biodataRefModel->getBiodataRefById($idBiodataRef);
            if(!$biodataRefData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $biodataRefData->id,
                'biodata_ref_name' => $biodataRefData->biodata_ref_name,
                'status' => $biodataRefData->status,
            ];

            return $this->responseJson([
                'status' => true,
                'data' => $data,
                'message' => 'status ok',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function createDataBiodataRef($request)
    {
        try {
            $validator = Validator::make($request, [
                'biodata_ref_name' => ['required', 'regex:/^[^<>]+$/', 'unique:biodata_ref,biodata_ref_name', 'max:255'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh :values.',
                'regex' => ':attribute mengandung karakter terlarang!'

            ]);

            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();

            if($this->biodataRefModel->insertBiodataRef($request)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil menambah data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal menambah data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDataBiodataRef($request, $idBiodataRef)
    {
        try {
            $biodataRefData = $this->biodataRefModel->getBiodataRefById($idBiodataRef);
            // cek dulu apakah ada data yg mau di update
            if(!$biodataRefData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request, [
                'biodata_ref_name' => ['required', 'regex:/^[^<>]+$/', 'unique:biodata_ref,biodata_ref_name,' . $biodataRefData->id, 'max:255'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh :values.',
                'regex' => ':attribute mengandung karakter terlarang!'

            ]);

            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();

            if($this->biodataRefModel->updateBiodataRef($request, $idBiodataRef)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil mengubah data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal mengubah data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDataBiodataRef($idBiodataRef)
    {
        try {
            if($this->biodataRefModel->deleteBiodataRef($idBiodataRef)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil menghapus data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal menghapus data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

}
