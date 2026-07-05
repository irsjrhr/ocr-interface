<?php

namespace App\Http\Controllers;

use App\Services\MinioS3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MinioControllerAPI extends Controller
{
    protected MinioS3Service $minioService;

    public function __construct(MinioS3Service $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * Menampilkan list semua bucket yang ada di MinIO.
     * URL API: GET /api/minio
     * Parameter: (none)
     * Contoh Response:
     * {
     *   "success": true,
     *   "data": ["bucket1", "bucket2"]
     * }
     *
     * @return JsonResponse
     */
    public function listBuckets(): JsonResponse
    {
        try {
            $buckets = $this->minioService->listBuckets();
            
            return response()->json([
                'success' => true,
                'data' => $buckets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil list bucket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan list directory browser untuk semua directory business unit pada suatu bucket.
     * URL API: GET /api/minio/{bucket}
     * Parameter Query:
     *   - ?search (opsional): kata kunci pencarian
     *   - ?startDate (opsional): filter format YYYY-MM-DD
     *   - ?endDate (opsional): filter format YYYY-MM-DD
     * Contoh Response:
     * {
     *   "success": true,
     *   "data": ["BU-1", "BU-2"]
     * }
     *
     * @param Request $request
     * @param string $bucket
     * @return JsonResponse
     */
    public function listBusinessUnits(Request $request, string $bucket): JsonResponse
    {
        try {
            $search = $request->query('search');
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');
            
            $directories = $this->minioService->listDirectories_byBucket('/', $bucket, $search, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $directories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mengambil list Business Unit: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan list directory browser untuk semua directory document pada suatu directory business unit.
     * URL API: GET /api/minio/{bucket}/{directoryBusinessUnit}
     * Parameter Query:
     *   - ?search (opsional): kata kunci pencarian
     *   - ?startDate (opsional): filter format YYYY-MM-DD
     *   - ?endDate (opsional): filter format YYYY-MM-DD
     * Contoh Response:
     * {
     *   "success": true,
     *   "data": ["Doc-1", "Doc-2"]
     * }
     *
     * @param Request $request
     * @param string $bucket
     * @param string $directoryBusinessUnit
     * @return JsonResponse
     */
    public function listDocuments(Request $request, string $bucket, string $directoryBusinessUnit): JsonResponse
    {
        try {
            $search = $request->query('search');
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');
            
            $directories = $this->minioService->listDirectories_byBucket($directoryBusinessUnit . '/', $bucket, $search, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $directories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mengambil list Dokumen: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan list directory browser untuk semua file document pada suatu directory document.
     * URL API: GET /api/minio/{bucket}/{directoryBusinessUnit}/{directoryDocument}
     * Parameter Query:
     *   - ?search (opsional): kata kunci pencarian file
     *   - ?startDate (opsional): filter tanggal awal YYYY-MM-DD
     *   - ?endDate (opsional): filter tanggal akhir YYYY-MM-DD
     *   - ?download_file (opsional): nama file (jika diisi, akan mengembalikan/mendownload file fisik)
     * Contoh Response (List):
     * {
     *   "success": true,
     *   "data": [
     *      {"name": "file1.json", "size": 1024, "lastModified": "2026-07-03"}
     *   ]
     * }
     *
     * @param Request $request
     * @param string $bucket
     * @param string $directoryBusinessUnit
     * @param string $directoryDocument
     * @return mixed
     */
    public function listFiles(Request $request, string $bucket, string $directoryBusinessUnit, string $directoryDocument)
    {
        try {
            $path = $directoryBusinessUnit . '/' . $directoryDocument . '/';
            
            // Jika ada parameter ?download_file=namafile.pdf maka akan mendownload file tersebut
            $downloadFile = $request->query('download_file');
            if ($downloadFile) {
                return $this->downloadFile($bucket, $path, $downloadFile);
            }
            
            // Preparing Logic Parameter URL
            $search = $request->query('search');
            $startDate = $request->query('startDate');
            $endDate = $request->query('endDate');
            // Get List Files
            $files = $this->minioService->listFiles_byDirectories($path, $bucket, $search, $startDate, $endDate);
            return response()->json([
                'success' => true,
                'data' => $files
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mengambil list file: " . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Mengunggah file document dari web hook interface nanonet
     * URL API: POST /api/minio/{bucket}/uploadInterface
     * Parameter Form Data (Body):
     *   - directoryBusinessUnit: (String) Wajib diisi
     *   - directoryDocument: (String) Wajib diisi
     *   - file: (File biner) Wajib diisi
     * Contoh Response:
     * {
     *   "success": true,
     *   "message": "File berhasil diupload.",
     * }
     *
     * @param Request $request
     * @param string $bucket
     * @return JsonResponse
     */
    public function uploadFileInterface(Request $request, string $bucket): JsonResponse
    {
        $request->validate([
            'directoryBusinessUnit' => 'required|string',
            'directoryDocument' => 'required|string',
            'file' => 'required|file'
        ]);

        try {
            $directoryBusinessUnit = $request->input('directoryBusinessUnit');
            $directoryDocument = $request->input('directoryDocument');
            $file = $request->file('file');
            
            $url = $this->minioService->uploadDocumentInterface($file, $directoryBusinessUnit, $directoryDocument, $bucket);
            
            if ($url) {
                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diupload.',
                    'url' => $url
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal upload file.'], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error upload file: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengunduh file dari MinIO menggunakan kombinasi parameter query.
     * URL API: GET /api/minio/{bucket}/download
     * Parameter Query:
     *   - directoryBusinessUnit: (String) Opsional
     *   - directoryDocument: (String) Opsional
     *   - filedocument: (String) Wajib
     *
     * @param Request $request
     * @param string $bucket
     * @return mixed
     */
    public function download(Request $request, string $bucket)
    {
        $directoryBusinessUnit = $request->query('directoryBusinessUnit');
        $directoryDocument = $request->query('directoryDocument');
        $filedocument = $request->query('filedocument');

        $path = '';
        if ($directoryBusinessUnit) {
            $path .= $directoryBusinessUnit . '/';
        }
        if ($directoryDocument) {
            $path .= $directoryDocument . '/';
        }

        // 1. Jika parameter filedocument diisi, download satu file tersebut
        if ($filedocument) {
            return $this->downloadFile($bucket, $path, $filedocument);
        }

        // 2. Jika hanya direktori yang diberikan, download semua isi direktori tersebut sebagai ZIP
        if ($path !== '') {
            return $this->downloadDirectoryAsZip($bucket, $path);
        }

        return response()->json([
            'success' => false,
            'message' => 'Harap tentukan setidaknya directoryBusinessUnit, directoryDocument, atau filedocument.'
        ], 400);
    }

    /**
     * Membuat arsip ZIP dari sebuah direktori di MinIO dan mendownloadnya.
     *
     * @param string $bucket
     * @param string $path
     * @return mixed
     */
    private function downloadDirectoryAsZip(string $bucket, string $path)
    {
        try {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
            $files = \Illuminate\Support\Facades\Storage::disk('minios3')->allFiles($path);

            if (empty($files)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Direktori kosong atau tidak ditemukan.'
                ], 404);
            }

            $zip = new \ZipArchive();
            // Membersihkan karakter / di akhir path untuk penamaan zip
            $cleanPath = rtrim(str_replace('/', '_', $path), '_');
            $zipFileName = ($cleanPath ?: $bucket) . '_' . time() . '.zip';
            
            // Pastikan folder temp exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
                foreach ($files as $file) {
                    $content = \Illuminate\Support\Facades\Storage::disk('minios3')->get($file);
                    // Hapus path depan (prefix) jika ingin di zip langsung ke akar file, atau biarkan agar berstruktur
                    $zip->addFromString(basename($file), $content);
                }
                $zip->close();
            } else {
                throw new \Exception('Gagal membuat file ZIP.');
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload direktori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengunduh file fisik dari MinIO berdasarkan parameter download_file.
     * URL API: Dipanggil melalui GET /api/minio/{bucket}/{directoryBusinessUnit}/{directoryDocument}?download_file={filename}
     * Response: (Stream File / Attachment HTTP) atau JSON error jika tidak ditemukan.
     *
     * @param string $bucket
     * @param string $directory
     * @param string $filename
     * @return mixed
     */
    private function downloadFile(string $bucket, string $directory, string $filename)
    {
        try {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
            $path = rtrim($directory, '/') . '/' . $filename;
            
            if (!Storage::disk('minios3')->exists($path)) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
            }

            return Storage::disk('minios3')->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mendownload file: " . $e->getMessage()
            ], 500);
        }
    }
}
