<?php

class Application_Model_EventoDemanda extends Zend_Db_Table_Abstract {

   protected $_name = 'evento_demanda';
   protected $_sequence = false;
   protected $_primary = array('evento', 'id_pessoa');
   protected $_dependentTables = array('pessoa', 'evento_realizacao');

   public function remover($data) {
      $this->delete($data);
   }

	/**
	 * @param array $data id_encontro, id_pessoa
	 */
   public function listar($data) {
      $select = "SELECT er.evento, nome_tipo_evento, nome_evento,
         TO_CHAR(data, 'DD/MM/YYYY') as data, TO_CHAR(hora_inicio, 'HH24:MM') AS hora_inicio,
         TO_CHAR(hora_fim, 'HH24:MM') AS hora_fim, validada
         FROM evento_demanda ed INNER JOIN evento_realizacao er ON (ed.evento = er.evento)
         INNER JOIN evento e ON (er.id_evento = e.id_evento)
         INNER JOIN tipo_evento te ON (e.id_tipo_evento = te.id_tipo_evento)
         WHERE e.id_encontro = ? AND ed.id_pessoa = ? ORDER BY data ASC, hora_inicio ASC ";

      return $this->getAdapter()->fetchAll($select, $data);
   }

   /**
    *
    * @param array $data id_encontro, id_pessoa, id_evento
    */
   public function lerEvento($data) {
      $select = "SELECT ed.evento, nome_tipo_evento, nome_evento,
         TO_CHAR(data, 'DD/MM/YYYY') as data, TO_CHAR(hora_inicio, 'HH24:MM') AS hora_inicio,
         TO_CHAR(hora_fim, 'HH24:MM') AS hora_fim
         FROM evento_demanda ed INNER JOIN evento_realizacao er ON (ed.evento = er.evento)
         INNER JOIN evento e ON (er.id_evento = e.id_evento)
         INNER JOIN tipo_evento te ON (e.id_tipo_evento = te.id_tipo_evento)
         WHERE e.id_encontro = ? AND ed.id_pessoa = ? AND ed.evento = ? ";

      $row = $this->getAdapter()->fetchAll($select, $data);
      if (!$row[0]) {
         throw new Exception("Evento não encontrado.");
         return;
      }
      return $row[0];
   }
}
