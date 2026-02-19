<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Diactoros\Response\JsonResponse;

class IndexController extends AbstractActionController
{
    private AdapterInterface $db;
    
    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function carruselAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $file = $this->params()->fromFiles('imagen');

            if ($file && $file['error'] === UPLOAD_ERR_OK) {

                $uploadDir = 'public/uploads/carrusel/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $nombre = uniqid('img_') . '.' . $ext;

                move_uploaded_file(
                    $file['tmp_name'],
                    $uploadDir . $nombre
                );

                // 🔥 CLAVE: redirigir después del POST
                return $this->redirect()->toRoute(
                    'application',
                    ['action' => 'carrusel']
                );
            }
        }

        // GET → cargar imágenes
        $imagenes = [];
        $dir = 'public/uploads/carrusel';

        if (is_dir($dir)) {
            $imagenes = array_values(
                array_diff(scandir($dir), ['.', '..'])
            );
        }

        return new ViewModel([
            'imagenes' => $imagenes,
        ]);
    }

    public function baseDatosAction()
    {
        $sql = new Sql($this->db);

        $select = $sql->select('productos');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        return new ViewModel([
            'productos' => $result,
        ]);
    }

    public function capchaAction()
    {
        $request = $this->getRequest();
        $viewModel = new ViewModel();

        // Solo procesamos cuando se envía el formulario (POST)
        if ($request->isPost()) {
            $postData = $request->getPost()->toArray();
            $token = $postData['h-captcha-response'] ?? '';

            $message = 'Captcha no recibido';
            $success = false;

            if ($token !== '') {
                $secret = 'ES_2207507e4662426fba1598f67036d8b0';

                // Validación simple con file_get_contents
                $url = 'https://hcaptcha.com/siteverify';
                $data = [
                    'secret'   => $secret,
                    'response' => $token,
                ];

                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ],
                ];

                $context  = stream_context_create($options);
                $result   = file_get_contents($url, false, $context);
                $response = json_decode($result, true);

                if ($response && !empty($response['success'])) {
                    $success = true;
                    $message = '¡Captcha válido! Puedes continuar con el formulario.';
                    // Aquí podrías guardar datos, enviar email, etc.
                } else {
                    $message = 'Captcha inválido o expirado.';
                    // Opcional: ver errores específicos
                    // if (!empty($response['error-codes'])) {
                    //     $message .= ' (' . implode(', ', $response['error-codes']) . ')';
                    // }
                }
            }

            // Pasamos los resultados a la vista para mostrar el mensaje
            $viewModel->setVariables([
                'success' => $success,
                'message' => $message,
            ]);
        }

        // Siempre mostramos la vista (con o sin mensaje)
        return $viewModel;
    }

    public function insertarEjemploAction()
    {
        $sql = new Sql($this->db);

        $insert = $sql->insert('productos');
        $insert->values([
            'nombre' => 'Producto ejemplo',
            'precio' => 100,
            'categoria_id' => 1,
        ]);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        $stmt->execute();

        return $this->redirect()->toRoute('application', [
            'action' => 'base-datos'
        ]);
    }

    public function borrarTodoAction()
    {
        $sql = new Sql($this->db);

        $delete = $sql->delete('productos');
        $stmt = $sql->prepareStatementForSqlObject($delete);
        $stmt->execute();

        return $this->redirect()->toRoute('application', [
            'action' => 'base-datos'
        ]);
    }

    public function eliminarImagenesAction()
    {
        $request = $this->getRequest();

        if (! $request->isPost()) {
            $this->getResponse()->setStatusCode(405);
            $this->getResponse()->setContent(json_encode([
                'success' => false,
                'message' => 'Método no permitido. Usa POST.'
            ]));
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            return $this->getResponse();
        }
        $uploadsPath = realpath(__DIR__ . '/../../../../public/uploads/carrusel');

        if ($uploadsPath === false || !is_dir($uploadsPath)) {
            $this->getResponse()->setStatusCode(500);
            $this->getResponse()->setContent(json_encode([
                'success' => false,
                'message' => 'La carpeta de uploads/carrusel no existe o no se puede acceder.'
            ]));
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            return $this->getResponse();
        }

        $files = glob($uploadsPath . '/*');
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $deletedCount++;
            }
        }

        $this->getResponse()->setStatusCode(200);
        $this->getResponse()->setContent(json_encode([
            'success' => true,
            'message' => $deletedCount > 0 
                ? "Se eliminaron $deletedCount imágenes correctamente." 
                : "No había imágenes para eliminar."
        ]));
        $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        return $this->getResponse();
    }

    public function crudSegundaEvaluacionAction(){
        $sql = new Sql($this->db);

        $select = $sql->select('usuarios');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        return new ViewModel([
            'usuarios' => $result,
        ]);
    }

public function addUsuarioBdAction()
{
    $request = $this->getRequest();
    if (!$request->isPost()) {
        return new \Laminas\View\Model\JsonModel([
            'success' => false,
            'message' => 'Método no permitido',
            'code' => 405
        ]);
    }

    $content = $request->getContent();
    $usuario = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return new \Laminas\View\Model\JsonModel([
            'success' => false,
            'message' => 'JSON inválido: ' . json_last_error_msg(),
            'code' => 400
        ]);
    }

    // Validar que todos los campos existan
    $requiredFields = ['nombre', 'apellido_pat', 'apellido_mat', 'telefono', 'correo', 'nacimiento'];
    foreach ($requiredFields as $field) {
        if (!isset($usuario[$field])) {
            return new \Laminas\View\Model\JsonModel([
                'success' => false,
                'message' => "Falta el campo: $field",
                'code' => 400
            ]);
        }
    }

    try {
        $sql = new Sql($this->db);
        $insert = $sql->insert('usuarios');
        $insert->values([
            'nombre' => $usuario['nombre'],
            'apellido_pat' => $usuario['apellido_pat'],
            'apellido_mat' => $usuario['apellido_mat'],
            'correo' => $usuario['correo'],
            'nacimiento' => $usuario['nacimiento'],
            'telefono' => $usuario['telefono']
        ]);
        $stmt = $sql->prepareStatementForSqlObject($insert);
        $stmt->execute();

        return new \Laminas\View\Model\JsonModel([
            'success' => true,
            'message' => 'Usuario agregado exitosamente',
            'code' => 200
        ]);
    } catch (\Exception $e) {
        return new \Laminas\View\Model\JsonModel([
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage(),
            'code' => 500
        ]);
    }
}

    public function addUsuarioAction(){
        return new ViewModel();
    }
}
