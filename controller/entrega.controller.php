<?php
require_once 'model/entrega.php';
require_once 'model/usuario.php';
require_once 'model/cierre.php';
require_once 'model/caja.php';
require_once 'model/metodo.php';
class entregaController
{
    private $model;
    private $usuario;
    private $cierre;
    private $caja;
    private $metodo;

    public function __CONSTRUCT()
    {
        $this->model = new entrega();
        $this->usuario = new usuario();
        $this->cierre = new cierre();
        $this->caja = new caja();
        $this->metodo = new metodo();
    }

    public function Index()
    {
        if (!isset($_SESSION)) session_start();
        $userId = $_SESSION['user_id'];
        $nivel = $_SESSION['nivel'];

        // Si es vendedor (nivel 3) o entregador (nivel 11), por defecto ve sus pendientes asignados
        $solo_asignados = ($nivel == 3 || $nivel == 11) ? $userId : null;

        $pendientes = $this->model->ListarPendientes($solo_asignados);
        $entregados = $this->model->ListarEntregados();
        $usuarios = $this->usuario->ListarUsuarios();
        $metodos_pago = $this->metodo->ListarTodos();

        require_once 'view/header.php';
        require_once 'view/entrega/entrega.php';
        require_once 'view/footer.php';
    }

    public function ConfirmarEntrega()
    {
        if (!isset($_SESSION)) session_start();

        $id_entrega = isset($_POST['id_entrega']) ? intval($_POST['id_entrega']) : 0;
        $importe_percibido = isset($_POST['importe_percibido']) ? floatval(str_replace('.', '', $_POST['importe_percibido'])) : 0;
        $metodo_pago = isset($_POST['metodo_pago']) ? trim($_POST['metodo_pago']) : 'Efectivo';
        $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
        $usuario_entrega_id = $_SESSION['user_id'];

        if ($id_entrega > 0) {
            $this->model->ConfirmarEntrega($id_entrega, $usuario_entrega_id, $importe_percibido, $metodo_pago, $observaciones);

            // Manejo de carga de archivos / comprobantes adjuntos (Cámara y Galería)
            $inputsArchivos = array('comprobantes', 'foto_camara');
            $uploadDir = 'uploads/comprobantes_entrega/';

            foreach ($inputsArchivos as $inputKey) {
                if (!empty($_FILES[$inputKey]['name'][0])) {
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $totalFiles = count($_FILES[$inputKey]['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        $tmpName = $_FILES[$inputKey]['tmp_name'][$i];
                        $originalName = $_FILES[$inputKey]['name'][$i];
                        $fileType = $_FILES[$inputKey]['type'][$i];
                        $fileError = $_FILES[$inputKey]['error'][$i];

                        if ($fileError === UPLOAD_ERR_OK && !empty($tmpName)) {
                            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                            if (empty($ext)) $ext = 'jpg';
                            $newFileName = 'entrega_' . $id_entrega . '_' . time() . '_' . $inputKey . '_' . $i . '.' . $ext;
                            $destination = $uploadDir . $newFileName;

                            if (move_uploaded_file($tmpName, $destination)) {
                                $this->model->GuardarComprobante($id_entrega, $destination, $originalName, $fileType);
                            }
                        }
                    }
                }
            }

            // Manejo de fotos capturadas en vivo mediante WebRTC base64
            if (!empty($_POST['fotos_capturadas_base64']) && is_array($_POST['fotos_capturadas_base64'])) {
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                foreach ($_POST['fotos_capturadas_base64'] as $idx => $base64Data) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                        $imgData = substr($base64Data, strpos($base64Data, ',') + 1);
                        $ext = strtolower($type[1]);
                        $decodedData = base64_decode($imgData);
                        if ($decodedData !== false) {
                            $newFileName = 'entrega_' . $id_entrega . '_' . time() . '_cam_' . $idx . '.' . $ext;
                            $destination = $uploadDir . $newFileName;
                            file_put_contents($destination, $decodedData);
                            $this->model->GuardarComprobante($id_entrega, $destination, 'Foto_Camara_' . ($idx + 1) . '.' . $ext, 'image/' . $ext);
                        }
                    }
                }
            }

            header("Location: ?c=entrega&success=Entrega+confirmada+exitosamente");
            exit();
        } else {
            header("Location: ?c=entrega");
            exit();
        }
    }

    public function VerDetalle()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
        if ($id > 0) {
            $entrega = $this->model->Obtener($id);
            $comprobantes = $this->model->ObtenerComprobantes($id);

            echo json_encode(array(
                'status' => 'success',
                'entrega' => $entrega,
                'comprobantes' => $comprobantes
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'ID no válido'));
        }
        exit();
    }
}
