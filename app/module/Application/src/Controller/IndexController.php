<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\AdapterInterface;

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
            return new JsonResponse([
                'success' => false,
                'message' => 'Método no permitido'
            ], 405);
        }

        $uploadsPath = realpath(__DIR__ . '/../../../public/uploads/carrusel');

        if ($uploadsPath === false || !is_dir($uploadsPath)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'La carpeta uploads no existe'
            ], 500);
        }

        $files = glob($uploadsPath . '/*');
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'message' => "Se eliminaron $deletedCount imágenes correctamente"
        ], 200);
    }

}
