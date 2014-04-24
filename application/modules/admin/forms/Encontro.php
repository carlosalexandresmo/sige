<?php

class Admin_Form_Encontro extends Zend_Form {

    protected $modo_edicao = false;

    public function modoEdicao() {
        $this->modo_edicao = true;
    }

    public function init() {
        $this->setName('Encontro');

        $submit = $this->createElement('submit', 'confimar', array('label' => _('Confirm')))->removeDecorator('DtDdWrapper');

        $cancelar = $this->createElement('submit', 'cancelar', array('label' => _('Cancel')))->removeDecorator('DtDdWrapper');
        $cancelar->setAttrib('class', 'submitCancelar');

        $this->addElements(array(
            $this->_nome_encontro(),
            $this->_apelido_encontro(),
            $this->_data_inicio(),
            $this->_data_fim(),
            $this->_periodo_submissao_inicio(),
            $this->_periodo_submissao_fim(),
            $submit,
            $cancelar
        ));
    }

    protected function _id_encontro() {
        $e = new Zend_Form_Element_Hidden('id_encontro');
        $e->addFilter('Int');
        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _nome_encontro() {
        $e = new Zend_Form_Element_Text('nome_encontro');
        $e->setLabel(_('Conference Name') . ":")
                ->setRequired(true)
                ->addValidator('StringLength', false, array(1, 100))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'large')
                ->setAttrib("placeholder", "I Encontro de Software Livre");

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _apelido_encontro() {
        $e = new Zend_Form_Element_Text('apelido_encontro');
        $e->setLabel(_('Codename') . ":")
                ->setRequired(true)
                ->addValidator('StringLength', false, array(1, 10))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'normal')
                ->setAttrib("placeholder", "I ESL");

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _data_inicio() {
        $e = new Zend_Form_Element_Text('data_inicio');
        $e->setLabel(_('Starts in') . ":")
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'date');

        if ($this->modo_edicao) {
            $e->setAttrib("disabled", "disabled");
        }

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _data_fim() {
        $e = new Zend_Form_Element_Text('data_fim');
        $e->setLabel(_('Ends in') . ":")
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'date');

        if ($this->modo_edicao) {
            $e->setAttrib("disabled", "disabled");
        }

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _periodo_submissao_inicio() {
        $e = new Zend_Form_Element_Text('periodo_submissao_inicio');
        $e->setLabel(_('Submission starts in') . ":")
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'date');

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

    protected function _periodo_submissao_fim() {
        $e = new Zend_Form_Element_Text('periodo_submissao_fim');
        $e->setLabel(_('Submission ends in') . ":")
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'date');

        $e->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array('HtmlTag', ''),
            array('Label', ''),
        ));
        return $e;
    }

}
