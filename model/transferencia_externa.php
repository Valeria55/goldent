<?php
class transferencia_externa
{
    private $pdoCentral;

    public $id;
    public $empresa_origen;
    public $quien_transfiere;
    public $monto;
    public $concepto;
    public $fecha_envio;
    public $hora_envio;
    public $comprobante_url;
    public $identificador_origen;
    public $estado;
    public $motivo_resolucion;
    public $fecha_procesado;
    public $procesado_por;

    public function __CONSTRUCT()
    {
     
        try {
            //HOST
            $user = "u832567584_paseodelasonri";
            $pass = "3LmF#J=h";
            $db = "u832567584_paseodelasonri";
            $host = "localhost";
            
            //LOCAL
            // $user = "root";
            // $pass = "";
            // $db = "paseodelasonrisa";
            // $host = "localhost";

            $this->pdoCentral = new PDO('mysql:host=' . $host . ';dbname=' . $db . ';charset=utf8', $user, $pass);
            $this->pdoCentral->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Listar()
    {
        try {
            $stm = $this->pdoCentral->prepare("SELECT * FROM transferencias_externas WHERE empresa_origen = 'Goldent' ORDER BY id DESC");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Registrar($data)
    {
        try {
            $sql = "INSERT INTO transferencias_externas 
                    (empresa_origen, quien_transfiere, monto, concepto, fecha_envio, hora_envio, comprobante_url) 
                    VALUES 
                    (:empresa, :quien, :monto, :concepto, :fecha, :hora, :comprobante)";

            $stmt = $this->pdoCentral->prepare($sql);

            $stmt->execute([
                ':empresa'   => 'Goldent',
                ':quien'     => $data->quien_transfiere,
                ':monto'     => $data->monto,
                ':concepto'  => $data->concepto,
                ':fecha'     => $data->fecha_envio,
                ':hora'      => $data->hora_envio,
                ':comprobante' => $data->comprobante_url
            ]);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
