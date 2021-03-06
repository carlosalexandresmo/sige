<?php

define("MPDF_PATH", APPLICATION_PATH . '/../vendor/mpdf/mpdf/mpdf.php');
define("INSCRICAO_ENCONTRO", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/inscricao_encontro.html');
define("ARTIGOS_LISTA", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/artigos_lista.html');
define("TICKET_INSCRICAO", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/ticket_inscricao.html');
define("FOLHA_PRESENCA", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/folha_presenca.html');
define("FOLHA_PRESENCA_ITEM", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/folha_presenca_item.html');
define("PROGRAMACAO", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/programacao.html');
define("PROGRAMACAO_ITEM", APPLICATION_PATH . '/../library/Sige/Pdf/Relatorio/Template/programacao_item.html');

/**
 *
 */
class Sige_Pdf_Relatorio_Parser {

    protected $tipo;
    protected $dados_array;
    protected $template_filepath;
    protected $doc_titulo;
    protected $doc_autor;
    protected $doc_criador;
    protected $doc_logo;
    protected $doc_subtitulo;

    /**
     *
     * @param string $tipo - (constante) qual dos modelos a carregar
     * @param array $dados_array - dados que serão preenchidos no relatório
     */
    public function __construct($tipo, $dados_array = array()) {
        $this->tipo = $tipo;
        $this->dados_array = $dados_array;

        $cache = Zend_Registry::get('cache_common');
        $ps = $cache->load('prefsis');
        $apelido_encontro = $ps->encontro["apelido_encontro"];

        $this->doc_titulo = $apelido_encontro . " - " . date("Y");
        $this->doc_autor = $this->doc_criador = "SiGE https://github.com/comsolid/sige";
        $this->doc_logo = APPLICATION_PATH . '/../public/img/logo_ifce_ceara.png';
        $this->doc_subtitulo = $dados_array["nome_relatorio"];
        $this->template_filepath = $this->parseTipo($tipo);
    }

    protected function parseTipo($tipo) {
        return constant($tipo);
    }

    protected function parseDados($template, $dados_array) {
        $keys = array_keys($dados_array);
        foreach ($keys as $key) {
            $template = preg_replace("/%%{$key}%%/", trim($dados_array[$key]), $template);
        }
        return $template;
    }

    /**
     * Carrega o template do arquivo.
     */
    protected function loadTemplate($template_filepath) {
        if (!file_exists($template_filepath) || !is_readable($template_filepath)) {
            return false;
        }
        $handle = fopen($template_filepath, 'rb');
        return fread($handle, filesize($template_filepath));
    }

    public function gerarPdf() {
        $format = $this->_build();
        $this->_download($format[0], $format[1]);
    }

    public function obterPdf() {
        $format = $this->_build();
        return $this->_getPDFBinaryString($format[0], $format[1]);
    }

    private function _build() {
        $css = null;
        $cabecalho = '
            <div style="text-align: center">
                <div class="row" style="text-align: center;">
                    <img src="' . $this->doc_logo . '" style="width: 4cm" />
                </div>
                <div class="row" style="text-align: center; margin-top: 10px;">
                    ' . $this->doc_titulo . '
                </div>
                <div class="row" style="
                    text-align: center;
                    margin-top: 2px;
                    font-size: 16px;
                    font-weight: bold;">
                    ' . $this->doc_subtitulo . '
                </div>
            </div>
        ';
        $rodape = '<p style="font-size: 9pt; text-align: center">';
        $rodape .= 'Página {PAGENO} de {nbpg} - Gerado pelo SiGE em '.date("d/m/Y H:i");
        $rodape .= '</p>';
        $html = '
<html>
<head>
</head>
<body>

<!--mpdf
<htmlpageheader name="myheader">
' . $cabecalho . '
</htmlpageheader>

<htmlpagefooter name="myfooter">
' . $rodape . '
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

' . $this->parseDados($this->loadTemplate($this->template_filepath), $this->dados_array) . '

</body>
</html>
';
        return array($css, $html);
    }

    /**
     * Retorna o PDF em forma de string binária
     * O objetivo é permitir envio do PDF através de anexo de e-mail
     */
    protected function _getPDFBinaryString($css, $html) {
        // Foi difícil, mas achei a solução: http://stackoverflow.com/a/9178296/846419
        try {
            ob_start(); // This is very important to start output buffering and to catch out any possible notices
            require_once MPDF_PATH;
            $mpdf = new mPDF('utf-8', 'A4', 10, 'Arial', 5, 5, 48, 25, 10, 10);
            $mpdf->debug = true;
            $mpdf->allow_output_buffering = true;
            $mpdf->useOnlyCoreFonts = true;    // false is default
            $mpdf->SetTitle($this->doc_titulo);
            $mpdf->SetAuthor($this->doc_autor);
            $mpdf->SetCreator($this->doc_criador);
            $mpdf->SetWatermarkText("SiGE");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->SetDisplayMode('fullpage');
//            $mpdf->SetAutoFont(0); // deprecated
            $mpdf->autoScriptToLang = FALSE;

            $mpdf->WriteHTML($css, 1);
            $mpdf->WriteHTML($html);
            $pdf_binary = $mpdf->Output('', 'S'); // With the binary PDF data in $pdf we can do whatever we want - attach it to email, save to filesystem, push to browser's PDF plugin or offer it to user for download
//        ob_get_contents(); // Here we catch out previous output from buffer (and can log it, email it, or throw it away as I do :-) )
            ob_end_clean(); // Finaly we clean output buffering and turn it off
            return $pdf_binary;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _download($css, $html) {
        $filename_output = "relatorio_sige_" . strtolower($this->tipo) . "_" . date("Y-m-d-His") . ".pdf";
        try {
            $pdf_binary = $this->_getPDFBinaryString($css, $html);

            if (headers_sent()) {
                throw new Exception('Some data has already been output to browser, '
                    . 'can\'t send PDF file.');
            }

            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: Tue, 01 Nov 1988 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Content-Type: application/force-download');
            header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
            header('Content-Type: application/pdf', false);
            if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) ||
                    empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                header('Content-Length: ' . strlen($pdf_binary));
            }

            header('Content-disposition: attachment; filename="' . $filename_output . '"');
            echo $pdf_binary; // With the headers set PDF file is ready for download after we call echo
            exit;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
}
