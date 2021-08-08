<?php
class QueryBuilder
{
    private $conn = null;
    private $config = [];
    private $query = "";
    private $countWhere = 0;
    private $countselect = 0;
    private $countjoin = 0;
    private $qeueu = array();
    private $new = true;
    private $functions = array("select", "from", "join", "set", "update", "where", "orwhere", "like", "group_by", 'order_by', "subquery");
    private $bind_scirpt = array();
    private $hasil = null;
    function __construct()
    {
        $config = (new josegonzalez\Dotenv\Loader('./.env'))->parse()->toArray();
        $conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASSWORD'], $config['DB_NAME']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $this->config = $config;
        $this->conn = $conn;
    }

    function connect()
    {
        $conn = new mysqli($this->config['DB_HOST'], $this->config['DB_USER'], $this->config['DB_PASSWORD'], $this->config['DB_NAME']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $this->conn = $conn;
    }

    function disconnect()
    {
        $this->conn->close();
    }

    function subquery()
    {

        if ($this->new) {
            $this->qeueu['subquery'][] = array(
                'temp' => array()
            );
        } else {
            return $this->query;
        }
        return $this;
    }
    function group_by($kolom)
    {
        if ($this->new) {
            $this->qeueu['group_by'][] = array(
                'temp' => array(
                    'kolom' => $kolom,
                )
            );
        } else {
            $this->query .= ' GROUP BY ' . $kolom;
        }
        return $this;
    }

    /**
     * @param $tipe enum['ASC'|'DESC']
     */
    function order_by($kolom, $tipe = 'ASC')
    {
        if ($this->new) {
            $this->qeueu['order_by'][] = array(
                'temp' => array(
                    'kolom' => $kolom,
                    'tipe' => $tipe
                )
            );
        } else {
            $this->query .= ' ORDER BY ' . $kolom . ' ' . $tipe;
        }
        return $this;
    }

    function get_query()
    {
        $this->new = false;
        if (count($this->qeueu) > 0)
            $this->execute();


        if (count($this->qeueu) == 0) {
            $this->query .= "";
            $this->db->query($this->query);
            foreach ($this->bind_scirpt as $bind) {
                $this->db->bind($bind['key'], $bind['value']);
            }
            $query = $this->query;
            $this->reset();

            return $query;
        }
    }
    function join($tabel, $on, $tipe = "INNER")
    {

        if ($this->new) {
            $this->qeueu['join'][] = array(
                'temp' => array(
                    'tabel' => $tabel,
                    'on' => $on,
                    'tipe' => $tipe,
                )
            );
        } else {
            // if($this->countjoin >0)
            $this->query .= ' ' . $tipe . " JOIN " . $tabel . " ON " . $on;
        }
        return $this;
    }

    function select($selection)
    {
        if ($this->new) {
            $this->qeueu['select'][] = array(
                'temp' => $selection
            );
        } else {
            if ($this->countselect > 0)
                $this->query .=  ", " . $selection;
            else
                $this->query .=  " SELECT " . $selection;

            $this->countselect++;
        }
        return $this;
    }
    function insert($input, $table)
    {
        $query = 'INSERT INTO ' . $table . '(';
        $jml = count($input);
        $bts = $jml - 1;
        $i = 0;
        foreach ($input as $k => $v) {
            if ($i != $bts)
                $query .= '`' . $k . '`, ';
            elseif ($i == $bts)
                $query .= '`' . $k . '`) VALUES (';

            $i++;
        }

        $i = 0;
        foreach ($input as $k => $v) {
            if ($i != $bts)
                $query .= "'" . $v . "', ";
            elseif ($i == $bts)
                $query .= "'" . $v .  "')" ;

            $i++;
        }
        return $this->conn->query($query) === TRUE ? true : false;
    }

    function insert_batch($inputs, $table)
    {
        $query = 'INSERT INTO ' . $table . '(';
        $juml_batch = count($inputs);
        $bts_batch = $juml_batch - 1;
        $jml = count($inputs[0]);
        $bts = $jml - 1;
        $j = 0;
        $i = 0;
        foreach ($inputs[0] as $k => $v) {
            if ($i != $bts)
                $query .= '`' . $k . '`, ';
            elseif ($i == $bts)
                $query .= '`' . $k . '`) VALUES (';

            $i++;
        }

        foreach ($inputs as $input) {
            $i = 0;
            foreach ($input as $k => $v) {
                if ($i != $bts)
                    $query .= '"' . $v . '", ';
                elseif ($i == $bts)
                    $query .= '"' . $v . '")';

                $i++;
            }

            $query .= $j != $bts_batch ? ', (' : null;
            $j++;
        }
        return $this->conn->query($this->query) === TRUE ? true : false;
    }
    function from($table)
    {
        if ($this->new) {
            $this->qeueu['from'][] = array(
                'temp' => $table
            );
        } else {
            $this->query .= " FROM " . $table;
        }
        return $this;
    }
    function where($kolom, $nilai, $operator = "=")
    {
        if ($this->new) {
            $this->qeueu['where'][] = array(
                'temp' => array(
                    'kolom' => $kolom,
                    'nilai' => $nilai,
                    'operator' => $operator
                )
            );
        } else {
            $key_binding = 'VAR' . random(1) . random(1, 'int');
            if (is_string($nilai))
                $nilai = "$nilai";

            if (stristr($this->query, "where"))
                $this->query .= " and " . $kolom . " " . $operator . "'$nilai'";
            else
                $this->query .= " where " . $kolom . " " . $operator . "'$nilai'";

            $this->bind_scirpt[] = array(
                "key" => $key_binding,
                "value" => "$nilai",
            );
            $this->countWhere++;
        }
        return $this;
    }
    function set($tabel, $new_value)
    {
        if ($this->new) {
            $this->qeueu['set'][] = array(
                'temp' => array(
                    'tabel' => $tabel,
                    'new_value' => $new_value,
                )
            );
        } else {
            $query = 'UPDATE ' . $tabel . ' SET ';
            $jml = count($new_value);
            $bts = $jml - 1;
            $i = 0;
            foreach ($new_value as $k => $v) {
                $key_binding = 'VAR' . random(1) . random(1, 'int');

                if ($i != $bts)
                    $query .= '`' . $k . "` = '$v', ";
                elseif ($i == $bts)
                    $query .= '`' . $k . "` = '$v'";

                $this->bind_scirpt[] = array(
                    "key" => $key_binding,
                    "value" => $v,
                );
                $i++;
            }
            $this->query .= $query;
        }
        // var_dump($query);die;
        return $this;
    }
    function update()
    {

        $this->new = false;
        if (count($this->qeueu) > 0)
            $this->execute();

        if (count($this->qeueu) == 0) {
            $res = $this->conn->query($this->query);
            $this->reset();
            if($res)
                return $res;
            else
                return false;
        }
    }
    function or_where($kolom, $nilai, $operator = "=")
    {
        if ($this->new) {
            $this->qeueu['orwhere'][] = array(
                'temp' => array(
                    'kolom' => $kolom,
                    'nilai' => $nilai,
                    'operator' => $operator
                )
            );
        } else {
            $key_binding = 'VAR' . random(1) . random(1, 'int');
            $this->query .= " OR " . $kolom . " " . $operator . "'$nilai'";
            $this->bind_scirpt[] = array(
                "key" => $key_binding,
                "value" => $nilai,
            );
            $this->countWhere++;
        }
        return $this;
    }
    function like($kolom, $nilai, $tipe = "doble")
    {
        if ($this->new) {
            $this->qeueu['like'][] = array(
                'temp' => array(
                    'kolom' => $kolom,
                    'nilai' => $nilai,
                    'tipe' => $tipe
                )
            );
        } else {
            $key_binding = 'VAR' . random(1) . random(1, 'int');
            if (!in_array($tipe, ['doble', 'kiri', 'kanan']))
                throw new Exception("Tipe 'Like' invalid", 1);

            if (is_string($nilai))
                $nilai = $tipe == 'doble' ? '%' . $nilai . '%' : ($tipe == 'kiri' ? '%' . $nilai : $nilai . '%');

            if (stristr($this->query, "where"))
                $this->query .= " and " . $kolom . " LIKE  '$nilai'";
            else
                $this->query .= " where " . $kolom . " LIKE '$nilai'";

            // $this->bind_scirpt[] = array(
            //     "key" => $key_binding,
            //     "value" => (),
            // );
            $this->countWhere++;
        }
        return $this;
    }
    function row($type = 'array')
    {
        $this->new = false;
        if(!in_array($type, ['array', 'object']))
            throw new Exception("Tipe data invalid", 500);
            
        $row = [];
        if (count($this->qeueu) > 0)
            $this->execute();

        if (count($this->qeueu) == 0) {
            $this->query .= " LIMIT 1";
            $res = $this->conn->query($this->query);
            if($res->num_rows > 0){
                while($r = $res->fetch_assoc())
                    $row = $r;       
            }else
                $row = null;

            $this->reset();
            return $type == 'object' ? (object) $row : $row;
        }
    }
    function results()
    {

        $this->new = false;
        $rows = [];
        if (count($this->qeueu) > 0)
            $this->execute();

        if (count($this->qeueu) == 0) {
            $this->query .= " LIMIT 1";
            $res = $this->conn->query($this->query);
            if($res->num_rows > 0){
                while($r = $res->fetch_assoc())
                    $rows[] = $r;       
            }else
                $rows = null;

            $this->reset();
            return $rows;
        }
    }
    function result_object()
    {
        $this->new = false;
        $rows = [];
        if (count($this->qeueu) > 0)
            $this->execute();

        if (count($this->qeueu) == 0) {
            $this->query .= " LIMIT 1";
            $res = $this->conn->query($this->query);
            if($res->num_rows > 0){
                while($r = $res->fetch_object())
                    $rows[] = $r;       
            }else
                $rows = null;

            $this->reset();
            return $rows;
        }
    }
    function call_function($f, $t, $index)
    {
        if ($f == "select")
            $this->select($t);

        if ($f == 'from')
            $this->from($t);

        if ($f == 'set')
            $this->set($t['tabel'], $t['new_value']);

        if ($f == 'join')
            $this->join($t['tabel'], $t['on'], $t['tipe']);

        if ($f == "where")
            $this->where($t['kolom'], $t['nilai'], $t['operator']);
        if ($f == 'update')
            $this->update();

        if ($f == "orwhere")
            $this->or_where($t['kolom'], $t['nilai'], $t['operator']);

        if ($f == "like")
            $this->like($t['kolom'], $t['nilai'], $t['tipe']);

        if ($f == "group_by")
            $this->group_by($t['kolom']);

        if ($f == "order_by")
            $this->order_by($t['kolom'], $t['tipe']);

        if ($f == 'subquery')
            $this->subquery();

        unset($this->qeueu[$f][$index]);
    }
    function execute()
    {
        foreach ($this->functions as $f) {
            foreach ($this->qeueu as $k => $v) {
                if ($k == $f) {
                    foreach ($v as $key => $value)
                        $this->call_function($f, $value['temp'], $key);
                }
                if (empty($this->qeueu[$f]))
                    unset($this->qeueu[$f]);
            }
        }

        if (count($this->qeueu) > 0)
            $this->execute();
    }
    function get()
    {
        $this->reset();
        return $this;
    }
    function reset()
    {
        $this->query = "";
        $this->countWhere = 0;
        $this->countselect = 0;
        $this->qeueu = array();
        $this->new = true;
        $this->bind_scirpt = array();
        $this->hasil = null;
    }
}
