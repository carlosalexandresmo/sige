<?php

class Admin_ConfigController extends Zend_Controller_Action {

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $session = new Zend_Session_Namespace();
            $session->setExpirationSeconds(60 * 60 * 1); // 1 minuto
            $session->url = $_SERVER['REQUEST_URI'];
            $this->_helper->redirector->goToRoute(array(), 'login', true);
            return;
        }

        $sessao = Zend_Auth::getInstance()->getIdentity();
        if (!$sessao ["administrador"]) {
            $this->_helper->redirector->goToRoute(array(
                'controller' => 'participante',
                'action' => 'index'), 'default', true);
        }

        $this->_helper->layout->setLayout('twbs3-admin/layout');
        $this->view->menu = new Sige_Desktop_AdminSidebarLeftMenu($this->view, 'config');
    }

    public function indexAction() {
    }

    public function permissaoUsuariosAction() {
    }

    public function ajaxBuscarUsuariosAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $sessao = Zend_Auth::getInstance()->getIdentity();
        $cache = Zend_Registry::get('cache_common');
        $ps = $cache->load('prefsis');
        $idEncontro = (int) $ps->encontro["id_encontro"];
        $json = new stdClass;

        $model = new Application_Model_Pessoa();
        try {
            $data = $model->buscarPermissaoUsuarios(
                    $idEncontro, $this->_request->getParam("termo", ""), $this->_request->getParam("buscar_por", "nome"), $this->_request->getParam("tipo_usuario", 0)
            );
        } catch (Exception $e) {
            $json->erro = $e->getMessage();
        }

        $json->size = count($data);
        $json->aaData = array();

        foreach ($data as $value) {
            if ($value['administrador']) {
                $admin = "<i class='icon-unlock'></i> Admin";
            } else {
                $admin = "<i class='icon-lock'></i> Usuário";
            }
            $acao = "<a href=\"/admin/config/editar-permissao/id/{$value['id_pessoa']}\"
                class=\"btn btn-default\"><i class=\"fa fa-edit\"></i> "
                . _("Edit permission") . "</a>";

            $json->aaData[] = array(
                "{$value ['nome']}",
                "{$value ['apelido']}",
                "{$value ['email']}",
                "{$admin}<br/>{$value['descricao_tipo_usuario']}",
                $acao
            );
        }

        header("Pragma: no-cache");
        header("Cache: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-type: text/json");
        echo json_encode($json);
    }

    public function editarPermissaoAction() {
        $cache = Zend_Registry::get('cache_common');
        $ps = $cache->load('prefsis');
        $idEncontro = (int) $ps->encontro["id_encontro"];

        $model = new Application_Model_Pessoa();
        $form = new Admin_Form_Permissao();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $cancelar = $this->getRequest()->getPost('cancelar');
                if (isset($cancelar)) {
                    return $this->_helper->redirector->goToRoute(array(
                                'module' => 'admin',
                                'controller' => 'config',
                                'action' => 'permissao-usuarios'), 'default', true);
                }

                $id = $this->getRequest()->getParam('id', 0);
                $admin = ((bool) $form->getValue('admin') ? 't' : 'f');
                $id_tipo_usuario = $form->getValue('id_tipo_usuario');

                $adapter = $model->getAdapter();
                try {
                    $adapter->beginTransaction();
                    $model->update(array('administrador' => $admin), 'id_pessoa = ' . $id);

                    $adapter->update("encontro_participante", array('id_tipo_usuario' => $id_tipo_usuario), "id_encontro = {$idEncontro} AND id_pessoa = {$id}");
                    $adapter->commit();
                    return $this->_helper->redirector->goToRoute(array(
                                'module' => 'admin',
                                'controller' => 'config',
                                'action' => 'permissao-usuarios'), 'default', true);
                } catch (Exception $e) {
                    $this->_helper->flashMessenger->addMessage(
                            array('danger' => 'Ocorreu um erro inesperado.<br/>Detalhes: '
                                . $e->getMessage()));
                    $adapter->rollBack();
                }
            } else {
                $form->populate($formData);
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $rs = $model->buscarPermissaoUsuarios($idEncontro, $id, "id_pessoa");
                $data = $rs[0];
                $form->populate(array(
                    'admin' => $data['administrador'],
                    'id_tipo_usuario' => $data['id_tipo_usuario']
                ));
                $this->view->usuario = $data['nome'];
            }
        }
    }

    public function limparCacheAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        try {
            $cache = Zend_Registry::get('cache_common');
            $cache->clean(Zend_Cache::CLEANING_MODE_ALL); // limpa todos os caches
//            $cache->remove('cache_common'); // limpa somente cache espeficico

            $this->_helper->flashMessenger->addMessage(
                    array('success' => 'Cache foi limpo com sucesso.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage(
                    array('error' => 'Ocorreu um erro inesperado.<br/>Detalhes: '
                        . $e->getMessage()));
        }
        return $this->_helper->redirector->goToRoute(array(
                    'module' => 'admin',
                    'controller' => 'config',
                    'action' => 'index'), 'default', true);
    }

    public function infoSistemaAction() {
        $this->view->title = _('System Info');

        $sistema = new Admin_Model_Sistema();
        $this->view->postgres = $sistema->infoPostgres();
    }
}
