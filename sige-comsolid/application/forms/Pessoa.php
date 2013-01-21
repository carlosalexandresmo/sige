<?php

class Url_Validator extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalidUrl';
 
    protected $_messageTemplates = array(
        self::INVALID_URL   => "'%value%' is not a valid URL.",
    );
 
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
 
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        return true;
    }
}

class Application_Form_Pessoa extends Zend_Form {

	public function init() {
		
		$nome = $this->createElement('text', 'nome',array('label' => '* Nome: '));
		$nome->setRequired(true)
         ->addValidator('regex', false, array('/^[ a-zA-ZáéíóúàìòùãẽĩõũâêîôûäëïöüçÁÉÍÓÚ]*$/'))
         ->addValidator('stringLength', false, array(6, 100))
         ->addErrorMessage("Nome deve ter no mínimo 6 caracteres, contém caracteres inválidos");
                         
		$email = $this->createElement('text', 'email',array('label' => '* E-mail: '));
		$email->setRequired(true)
          ->addValidator('EmailAddress')
          ->addErrorMessage("E-mail inválido");
   
		$apelido = $this->createElement('text', 'apelido',array('label' => '* Apelido: '));
		$apelido->setRequired(true)
          ->addValidator('stringLength', false, array(1, 20))
          ->addErrorMessage("Apelido deve ter no nímimo 1 caracteres");

      $modelSexo = new Application_Model_Sexo();
		$rs = $modelSexo->fetchAll(null, 'id_sexo ASC'); 
		$sexo = $this->createElement('radio', 'id_sexo',array('label' => 'Sexo: '));
		$sexo->setRequired(true)
         ->setValue('0')
         ->setSeparator('');
      foreach($rs as $row) {
			$sexo->addMultiOption($row->id_sexo, $row->descricao_sexo);
		}
         
		$twitter = $this->createElement('text', 'twitter',array('label' => 'Twitter: @'));
		$twitter->addValidator('regex', false, array('/^[A-Za-z0-9_]*$/'))
           ->addErrorMessage("Twitter inválido");
               
		$facebook = $this->createElement('text', 'facebook',array('label' => 'Facebook (E-mail): '));
		$facebook->addValidator('EmailAddress')
            ->addErrorMessage("Facebook inválido");
               
		$site = $this->createElement('text', 'endereco_internet',array('label' => 'Site: '));
		$site->addValidator(new Url_Validator)
        ->addErrorMessage("Site inválido");
           


		$cidade = new Application_Model_Municipio();
		$listaCiddades  = $cidade->fetchAll(null, 'nome_municipio');
			  
		$municipio = $this->createElement('select', 'municipio',array('label' => 'Município: '));
		$municipio->setAttrib("class", "select2");
		foreach($listaCiddades as $item)
		{
			 $municipio->addMultiOptions(array($item->id_municipio => $item->nome_municipio));   
		}
	  

		$ins = new Application_Model_Instituicao();
		$listaIns  = $ins->fetchAll(null,'nome_instituicao');
															  
		$instituicao = $this->createElement('select', 'instituicao',array('label' => 'Instituição: '));
		$instituicao->setAttrib("class", "select2");
		foreach($listaIns as $item)
		{
			$instituicao->addMultiOptions(array($item->id_instituicao => $item->nome_instituicao));   
		}

		// TODO: usar máscara para ano nascimento, usar numeric(4) para guardar
		$anoNascimento = $this->createElement('select', 'nascimento',array('label' => 'Ano Nascimento: '));
		$anoNascimento->setAttrib("class", "select2");
		$date = new Zend_Date();
      $ano = (int) $date->toString('YYYY');
      for($i = $ano; $i > 1899; $i--) {
			 $anoNascimento->addMultiOptions(array("1/1/$i" => "$i"));
		}

		$this->addElement($nome)
						->addElement($email)
						->addElement($apelido)
						->addElement($sexo)
						->addElement($twitter)
						->addElement($facebook)
						->addElement($site)
						->addElement($municipio)
						->addElement($instituicao)
						->addElement($anoNascimento);
					  
		$botao = $this->createElement('submit', 'confimar')->removeDecorator('DtDdWrapper');
		$this->addElement($botao);
		$botao = $this->createElement('reset', 'cancelar')->removeDecorator('DtDdWrapper');
		$this->addElement($botao);
	}
}