<?php

/**
 * @deprecated usar Sige_Desktop_Menu
 */
class Application_Form_Menu extends Zend_Form
{
	private $control;
	private $menuAtivo = array('inicio'=>'inicio','programacao'=>'','submissao'=>'','caravana'=>'');
	private $acaoAtual;
	private $participanteIndexInicio;
	private $caravana, $submissao, $programacao, $admin;
   private $isAdmin = false;
	
	public function __construct($base, $ativo, $isAdmin = false) {
		$this->control = $base;
		$this->programacao = $this->control->url(array(), 'programacao', true); 
		$this->participanteIndexInicio = $this->control->url(array('controller'=>'participante','action'=>'index'), 'default', true);
		$this->submissao = $this->control->url(array(), 'submissao', true);
      $this->caravana = $this->control->url(array('controller'=>'caravana','action'=>'index'), 'default', true);
		$this->admin = $this->control->url(array(), 'admin', true);
      
      $this->setAtivo($ativo);
      $this->isAdmin = $isAdmin;
	}
	
	public function init(){
		//$menuAtivo=array('inicio'=>'','alterarsenha'=>'','meuseventos'=>'','caravana'=>'');
	}

	public function setAtivo($ativo){
  		if('inicio' == $ativo) {
			$this->menuAtivo['inicio']="active fl_left";
    	} else {
			$this->menuAtivo['inicio']='';
		}

    	if('programacao' == $ativo) {
			$this->menuAtivo['programacao']="active fl_left";
    	}else{
    		$this->menuAtivo['programacao']="";
    	}
    	
    	if('submissao' == $ativo) {
			$this->menuAtivo['submissao']="active fl_left";
    	} else {
			$this->menuAtivo['submissao']="";
		}
		
    	if('caravana' == $ativo) {
			$this->menuAtivo['caravana']="active fl_left";
		} else {
			$this->menuAtivo['caravana']="";
		}
      
      if('admin' == $ativo) {
			$this->menuAtivo['admin']="active fl_left";
		} else {
			$this->menuAtivo['admin']="";
		}
	}
	
	public function getAtivo() {
    	$this->menuAtivo;
	}
	
	public function getCaravana() {
		return $this->caravana;
	}
	
	public function getSubmissao() {
		return $this->submissao;
	}
	
	public function getProgramacao() {
		return $this->programacao;
	}
	
	public function getInicio() {
		return $this->participanteIndexInicio;
	}
	
	public function getAcaoAtual() {
		return $this->acaoAtual;
	}
	
    /**
     * TODO: refazer lógica do menu!
     * @return string
     */
	public function getView() {
        $menu = "<div id=\"menu\" class=\"fl_left\"><a class=\"" . $this->menuAtivo['inicio'];
        $menu.= "\" href=\"" . $this->getInicio() . "\"><i class=\"icon-home icon-large\"></i> &nbsp;" . _("Home") . "</a>";
        $menu.= "<a class=\"" . $this->menuAtivo['programacao'];
        $menu.= "\" href=\"" . $this->getProgramacao() . "\"><i class=\"icon-calendar icon-large\"></i> &nbsp;" . _("Schedule") . "</img></a>";
        $menu.= "<a class=\"" . $this->menuAtivo['caravana'];
        $menu.= "\" href=\"" . $this->getCaravana() . "\"><i class=\"icon-plane icon-large\"></i> &nbsp;" . _("Caravan") . "</a>";
        $menu.= "<a class=\"" . $this->menuAtivo['submissao'];
        $menu.= "\" href=\"" . $this->getSubmissao() . "\"><i class=\"icon-file-text icon-large\"></i> &nbsp;" . _("Paper Submission") . "</a>";
        //$menu.=" <a  class=\"";
        //$menu.=" \" href=\"#\" class=\"fl_right\"><img src=\"".$this->control->baseUrl('imagens/layout/bt_sejavoluntario.png')."\"></img></a>";
        if ($this->isAdmin) {
            $menu.="<a class=\"" . $this->menuAtivo['admin'];
            $menu.="\" href=\"" . $this->admin . "\"><i class=\"icon-legal icon-large\"></i> &nbsp;Admin</a>";
        }
        $menu.= "</div>";
        return $menu;
    }

}
