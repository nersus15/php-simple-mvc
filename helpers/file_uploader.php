<?php
class FileUploader
{
    private $core = null;
    function __construct()
    {
        $this->core = new Core();   
    }
    function upload_image($gambar, $tujuan, $conf = null)
    {
        $config = array(
            'default' => null,
            'asal' => null,
            'maks' => 6000000,
            'isEnkripsi' => true,
        );
        if (!empty($conf))
            foreach ($conf as $k => $v)
                $config[$k] = $v;


        $nama_file = $gambar['name'];
        $ukuran_file = $gambar['size'];
        $error = $gambar['error'];
        $tmp = $gambar['tmp_name'];
        $format_sesuai = ['jpg', 'jpeg', 'png'];
        $format_file = explode('.', $nama_file);
        $format_file = strtolower(end($format_file));
        if (!in_array($format_file, $format_sesuai)) {
            response(['message' => 'Gagal, Pilih file yang Valid jpg/jpeg/png'], 403);
        } elseif ($ukuran_file > $config['maks']) {
            response(['message' => 'Gagal, size file yang dipilih terlalu besar'], 403);
        } else {
            if ($config['isEnkripsi']) {
                $nama_image = random(10);
                $nama_image .= ".";
                $nama_image .= $format_file;
            } else
                $nama_image =  $config['name'] . '.' . $format_file;

            try {
                move_uploaded_file($tmp, $tujuan . '/' . $nama_image);
            } catch (\Exception $err) {
                response(['message' => 'Gagal upload file', 'err' => $err->getMessage()], 500);
            }
            if (isset($config['sebelum']) && !empty($config['sebelum'])) {
                unlink($tujuan . '/' . $config['sebelum']);
            }
            return $nama_image;
        }
    }
    function readExcel($files, $mapping = []){
        // var_dump($files);
        require_once $this->core->get_path('thirdParty/PhpSpreadsheet/vendor/autoload.php');
        require_once $this->core->get_path('thirdParty/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $path = $files['tmp_name'];
        $object = $reader->load($path);
        $sheetCount = $object->getSheetCount();
        $data = [];
        for($i=0; $i < $sheetCount; $i++){
            $sheets = [];
            $sheetData = $object->getSheet($i)->toArray();
            foreach($sheetData as $row){
                foreach($mapping as $k => $v){
                    $temp[$v] = $row[$k];
                }
                $sheets[] = $temp;
            }
            $data[] = $sheets;
        }

        return $data;
    }
}
