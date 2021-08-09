<?php
class Home extends Core
{
    public function index()
    {
        $this->load_view('pages/upload_atk');
    }
    function upload(){
        $uploader = $this->load_class('helpers/file_uploader', 'FileUploader', true);
        /**
         * @var FileUploader $uploader
         */

        $data = $uploader->readExcel($_FILES['excel'], ['kategori', 'nama', 'harga', 'stok']);
        $input = [];
        foreach($data[0] as $v){
            $v['id'] = random(8);
            $input[] = $v;
        }
        
        /**
         * @var QueryBuilder $this->db
         */

         $this->db->insert_batch($input, 'atk');
        
    }

    function contoh(){
        $q = $this->db->select('*')
            ->from('atk')->join('transaksi', 'transaksi.barang = atk.id', 'LEFT')
            ->result_object();
        response($q);
    }
}
