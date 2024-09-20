<?php
/*
Versi Esign Digunakan : 2.0
Pengembang : Nurul Hudin
Contact : 082315192789
*/
class EsignService
{
    public $config;
    public function __construct()
    {
        $this->config = $this->config();
    }

    public function config()
    {
        try {
            $this->loadEnv(__DIR__ . '/.env');
            return [
                'ip' => getenv('IP'),
                'username' => getenv('USERNAME'),
                'password' => getenv('PASSWORD'),
            ];
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }


    public function verifyUser($nik)
    {
        $data = [
            "nik" => $nik,
        ];
        // URL API
        $url = $this->config['ip'] . '/api/v2/user/check/status';
        // Inisialisasi cURL
        $ch = curl_init($url);
        // Username dan password untuk Basic Auth

        // Mengatur opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->config['username'] . ':' . $this->config['password'])
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // Eksekusi permintaan dan ambil respons
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Tutup cURL
        file_put_contents('api_response.log', "HTTP Status: $httpStatus\nResponse: $response\n");
        // Cek jika terjadi error
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } elseif ($httpStatus >= 400) {
            echo 'API Error: ' . $response;
        } else {
            // Tampilkan respons
            header('Content-Type: application/json');
            return $response;
        }
        // Tutup cURL
        curl_close($ch);
    }

    function signPdf($arg)
    {
        // Data yang akan dikirim dalam format JSON
        $data = [
            "nik" => $arg['user']['nik'],
            "passphrase" => $arg['user']['passphrase'],
            "signatureProperties" => [
                [
                    "tampilan" => "VISIBLE",
                    "imageBase64" => $this->fileToBase64($arg['gambar_tte']),
                    "page" => $arg['halaman'] ?? 1,
                    "originX" => $arg['originX'],
                    "originY" => $arg['originY'],
                    "width" => $arg['width'],
                    "height" => $arg['height']
                ]
            ],
            "file" => [$this->fileToBase64($arg['file_pdf'])]
        ];
        // URL API
        $url = $this->config['ip'] . '/api/v2/sign/pdf';
        // Inisialisasi cURL
        $ch = curl_init($url);
        // Username dan password untuk Basic Auth

        // Mengatur opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->config['username'] . ':' . $this->config['password'])
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // Eksekusi permintaan dan ambil respons
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Tutup cURL
        file_put_contents('api_response.log', "HTTP Status: $httpStatus\nResponse: $response\n");
        // Cek jika terjadi error
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } elseif ($httpStatus >= 400) {
            echo 'API Error: ' . $response;
        } else {
            // Tampilkan respons
            $js = json_decode($response, true);
            if (isset($js['file'][0])) {
                $filedecode = base64_decode($js['file'][0]);
                file_put_contents($arg['savetodir'] . '/' . $arg['savetofile'], $filedecode);
                if (file_exists($arg['savetodir'] . '/' . $arg['savetofile'])) {
                    ///lakuakan sesuatu disini
                    //respon json file pdf sudah tte
                    $result = json_encode(['status' => 'success', 'file' => $arg['savetodir'] . '/' . $arg['savetofile']]);
                } else {
                    //pesan gagal disimpan di disk
                    $result = json_encode(['status' => 'failed', 'msg' => 'File Failed to store on your disk']);
                }
            } else {
                $result = json_encode(['status' => 'failed', 'msg' => 'Failed Sign Document']);
            }
            header('Content-Type: application/json');
            return $result;
        }
        // Tutup cURL
        curl_close($ch);
    }
    function verifyPdf($arg)
    {
        // Data yang akan dikirim dalam format JSON
        $data = [
            "file" => $this->fileToBase64($arg['file'])
        ];
        // URL API
        $url = $this->config['ip'] . '/api/v2/verify/pdf';
        // Inisialisasi cURL
        $ch = curl_init($url);
        // Username dan password untuk Basic Auth

        // Mengatur opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->config['username'] . ':' . $this->config['password'])
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // Eksekusi permintaan dan ambil respons
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Tutup cURL
        file_put_contents('api_response.log', "HTTP Status: $httpStatus\nResponse: $response\n");
        // Cek jika terjadi error
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } elseif ($httpStatus >= 400) {
            echo 'API Error: ' . $response;
        } else {
            error_log($response);
            header('Content-Type: application/json');
            return $response;
        }
        // Tutup cURL
        curl_close($ch);
    }

    function fileToBase64($filepath)
    {
        $content = file_get_contents($filepath);
        return base64_encode($content);
    }
    function loadEnv($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("File .env tidak ditemukan di $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                // Abaikan baris komentar
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Hilangkan tanda kutip di sekitar nilai
            $value = trim($value, '"');

            // Set environment variable
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}
