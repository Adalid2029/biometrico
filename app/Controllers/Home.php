<?php


namespace App\Controllers;

use App\Models\Hora;
use App\Models\Querys;

class Home extends BaseController
{
    public $db = null;
    protected $q = null;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->q = new Querys();
    }
    public function index()
    {
        // $this->q->selectTable('ad_persona_marcado', '*', [], 'id_persona_marcado desc')->getResultArray()
        // return view('welcome_message', ['personMarking' => $this->db->table('ad_persona_marcado apm')->join('persona p', 'p.ci = apm.ci')->get()->getResultArray()]);
        return view('welcome_message', ['personMarking' => $this->q->selectTable('ad_persona_marcado', '*', [], 'id_persona_marcado desc')->getResultArray()]);
    }
    public function search($searchType)
    {
        // return var_dump($_REQUEST);
        if ($this->request->isAJAX()) {

            switch ($searchType) {
                default:
                    // return
                    // 	var_dump($_REQUEST);
                    $person = $this->q->searchGlobal('persona', [
                        ['dato' => mb_convert_case($this->request->getVar('query'), MB_CASE_UPPER), 'columns' => ['nombre', 'paterno', 'materno', 'ci'], 'divide' => true, 'expression' => 'groupStart', 'match' => 'both', 'escape' => true, 'join' => '%'],
                    ], "concat(nombre, ' ', paterno, ' ', materno, ' ', ci ) as nombre, ci");


                    $array_aux = [];
                    foreach ($person as $key => $dat) {
                        $array_aux[] = ['value' => $dat['nombre'], 'data' =>  $dat['ci']];
                    }
                    $return_array = ['query' => 'Unit', 'suggestions' => $array_aux];
                    return $this->response->setJSON($return_array);
                    break;
            }
        }
    }
    function savePeerMaking()
    {
        $data = $this->request->getVar();
        // return var_dump(date('Y-m-d H:i:s', strtotime("{$data['date-marking']} {$data['hour']}")));
        $insertedPersonMaking = $this->q->insertTable('ad_persona_marcado', [
            'ci' => $data['ci'],
            'marcado' => date('Y-m-d H:i:s', strtotime("{$data['date-marking']} {$data['hour']}")),
        ]);
        if ($insertedPersonMaking)
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Se ha guardado correctamente']);
        else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar']);
        }
    }
    public function in()
    {

        $hora = new Hora();
        $datos = [];
        set_time_limit(50000);
        $this->db->query('SET GLOBAL group_concat_max_len=1002400;');
        $this->db->query('SET SESSION group_concat_max_len=1002400;');
        // foreach ($this->q->selectTable('persona_marcado', "ci, GROUP_CONCAT(concat(marcado,'|',id_biometrico)) marcado", ['trim(ci)' => '9874182', 'marcado >=' => '2022-05-01 00:00:00', 'marcado <=' => '2022-06-01 07:30:00'], '', 'ci')->getResultArray() as $key => $value) {

        foreach ($this->q->selectTable('persona_marcado', "ci, GROUP_CONCAT(concat(marcado,'|',id_biometrico)) marcado", ['trim(ci)' => '8362136'], '', 'ci')->getResultArray() as $key => $value) {
            $datos[$value['ci']] = $value['marcado'];
            // foreach ($this->q->selectTable('persona_marcado', 'cast(marcado as date) as marcado', ['ci' => $value['ci']], '', 'cast(marcado as date)')->getResultArray() as $k => $v) {
            //     $fechas[$v['marcado']] = $this->q->selectTable('persona_marcado', 'group_concat(cast(marcado as time)) marcado', ['ci' => $value['ci'], 'cast(marcado as date)' => $v['marcado']])->getRowArray();
            // }
            // $this->q->insertTable('persona_fecha_marcado', ['ci' => $value['ci'], 'fecha' => json_encode($fechas)]);
            // var_dump($fechas);
        }
        var_dump($datos);
        die;
        $fechasPorMarcar = $this->q->selectTable('fecha_marcado', '*', ['id_fecha_marcado >=' => '2022-05-01', 'id_fecha_marcado <=' => '2022-06-01'])->getResultArray();

        foreach ($datos as $key => $value) {
            $fechas = [];
            foreach (explode(',', $value) as $k => $v) {
                $fecha = strtotime(explode('|', $v)[0]);
                $id_biometrico = explode('|', $v)[1];
                $fechas[date('Y-m-d', $fecha)][] = [
                    'id_biometrico' => $id_biometrico,
                    'hora' => date('H:i:s', $fecha),
                ];
            }
            foreach ($fechasPorMarcar as $t => $te) {
                $id_persona_fecha_marcado = $this->q->insertTable('persona_fecha_marcado', ['ci' => $key, 'id_fecha_marcado' => $te['id_fecha_marcado']]);
                // $id_persona_fecha_marcado = 1;
                if (array_key_exists($te['id_fecha_marcado'], $fechas)) {
                    $hEntradaM = [];
                    $hSalidaT = [];
                    $hMediodia = [];

                    foreach ($fechas[$te['id_fecha_marcado']] as $w => $we) {
                        $hora = ['hora' => $we['hora'], 'id_biometrico' => $we['id_biometrico'], 'id_persona_fecha_marcado' => $id_persona_fecha_marcado];
                        if ($we['hora'] <= '11:59:00') {
                            $hEntradaM = empty($hEntradaM) ? array_merge($hora, ['tipo' => 'EM']) : ($we['hora'] < $hEntradaM ? array_merge($hora, ['tipo' => 'EM']) : $hEntradaM);
                        } else if ($we['hora'] >= '15:00:00') {
                            $hSalidaT = empty($hSalidaT) ? array_merge($hora, ['tipo' => 'ST']) : ($we['hora'] > $hSalidaT ? array_merge($hora, ['tipo' => 'ST']) : $hSalidaT);
                        } else if ($we['hora'] >= '12:00:00' && $we['hora'] <= '14:59:00') {
                            $hMediodia[] = $hora;
                        }

                        // $this->q->insertTable(
                        //     'hora_marcado',
                        //     ['id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'id_biometrico' => $we['id_biometrico'], 'hora' => $we['hora']]
                        // );
                    }
                    if (empty($hEntradaM)) {
                        $hEntradaM = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'EM'];
                    }
                    if (empty($hSalidaT)) {
                        $hSalidaT = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'ST'];
                    }
                    $hSalidaM = [];
                    $hEntradaT = [];
                    if (count($hMediodia) == 1) {
                        if ($hMediodia[0]['hora'] <= '12:30:00') {
                            $hSalidaM = array_merge($hMediodia[0], ['tipo' => 'SM']);
                            $hEntradaT = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'ET'];
                        } else if ($hMediodia[0]['hora'] > '12:30:00') {
                            $hSalidaM = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'SM'];
                            $hEntradaT = array_merge($hMediodia[0], ['tipo' => 'ET']);
                        }
                    } else if (count($hMediodia) == 0) {
                        $hSalidaM = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'SM'];
                        $hEntradaT = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'ET'];
                    } else if (count($hMediodia) >= 2) {

                        if (max($hMediodia) == min($hMediodia)) {
                            if (max($hMediodia)['hora'] <= '12:30:00') {
                                $hSalidaM = array_merge(max($hMediodia), ['tipo' => 'SM']);
                                $hEntradaT = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'ET'];
                            } else if (max($hMediodia)['hora'] > '12:30:00') {
                                $hSalidaM = ['hora' => null, 'id_biometrico' => null, 'id_persona_fecha_marcado' => $id_persona_fecha_marcado, 'tipo' => 'SM'];
                                $hEntradaT = array_merge(max($hMediodia), ['tipo' => 'ET']);
                            }
                        } else {
                            $hSalidaM = array_merge(min($hMediodia), ['tipo' => 'SM']);
                            $hEntradaT = array_merge(max($hMediodia), ['tipo' => 'ET']);
                        }
                    }

                    // var_dump($te['id_fecha_marcado'], $hEntradaM, $hSalidaM, $hEntradaT, $hSalidaT);
                    $this->q->insertTable('hora_marcado', $hEntradaM);
                    $this->q->insertTable('hora_marcado', $hSalidaM);
                    $this->q->insertTable('hora_marcado', $hEntradaT);
                    $this->q->insertTable('hora_marcado', $hSalidaT);
                    // var_dump($hEntradaM, $hSalidaT, $hMediodia);
                }
            }
            // try {
            //     $this->q->insertTable('persona_fecha_marcado', ['ci' => $key, 'fecha' => json_encode($fechas)]);
            // } catch (\Exception $e) {
            //     var_dump($e->getMessage(), $key);
            // }
        }


        // $file = $this->request->getFile('documento');
        // $file->move(WRITEPATH . 'uploads');

        // $data = [
        //     'file_name' =>  $file->getName(),
        //     'file_type'  => $file->getClientMimeType(),
        //     'file_size'  => $file->getSize(),
        // ];

        // $file = IOFactory::load(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $file->getName());
        // for ($sheetIndex = 0; $sheetIndex < $file->getSheetCount(); $sheetIndex++) {
        //     $currentSheet = $file->getSheet($sheetIndex);

        //     // $i = 1;
        //     // $cn = 0;
        //     // while (!empty(($currentSheet->getCell('A' . $i))->getValue())) {
        //     //     $cn++;
        //     //     $i++;
        //     // }
        //     for ($x = 1; $x < $currentSheet->getHighestRow(); $x++) {
        //         $value = trim($currentSheet->getCell('A' . $x)->getValue());
        //         // if (is_null($this->q->selectTable('administrativo', '*', ['ci' => $value])->getRowArray()))
        //         $this->q->insertTable('administrativo', ['ci' => $value]);
        //     }
        // }

    }
}
