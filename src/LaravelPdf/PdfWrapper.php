<?php

namespace vzool\LaravelPdf;

/**
 * Laravel PDF: mPDF wrapper for Laravel 5
 *
 * @package laravel-pdf
 * @author Niklas Ravnsborg-Gjertsen
 */
class PdfWrapper {

	protected $mpdf;
	protected $rendered = false;
	protected $options;

	public function __construct($mpdf) {
		$this->mpdf = $mpdf;
		$this->options = array();
	}

	/**
	 * Load a HTML string
	 *
	 * @param string $string
	 * @return static
	 */
	public function loadHTML($string, $mode = 0) {
		$this->mpdf->WriteHTML((string) $string, $mode);
		$this->html = null;
		$this->file = null;
		return $this;
	}

	/**
	 * Load a HTML file
	 *
	 * @param string $file
	 * @return static
	 */
	public function loadFile($file) {
		$this->html = null;
		$this->file = $file;
		return $this;
	}

	/**
	 * Load a View and convert to HTML
	 *
	 * @param string $view
	 * @param array $data
	 * @param array $mergeData
	 * @return static
	 */
	public function loadView($view, $data = array(), $mergeData = array()) {
		$this->html = \View::make($view, $data, $mergeData)->render();
		$this->file = null;
		return $this;
	}

	/**
	 * Output the PDF as a string.
	 *
	 * @return string The rendered PDF as string
	 */
	public function output() {

		if($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output('', 'S');
	}

	/**
	 * Save the PDF to a file
	 *
	 * @param $filename
	 * @return static
	 */
	public function save($filename) {

		if($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'F');
	}

	/**
	 * Make the PDF downloadable by the user
	 *
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function download($filename = 'document.pdf') {

		if ($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif ($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'D');
	}

	/**
	 * Return a response with the PDF to show in the browser
	 *
	 * @param string $filename
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function stream($filename = 'document.pdf' ){
		if ($this->html) {
			$this->mpdf->WriteHTML($this->html);
		} elseif($this->file) {
			$this->mpdf->WriteHTML($this->file);
		}

		return $this->mpdf->Output($filename, 'I');
	}

	/**
	 * Encrypts and sets the PDF document permissions
	 *
	 * @param array $permisson Permissons e.g.: ['copy', 'print']
	 * @param string $userPassword User password
	 * @param string $ownerPassword Owner password
	 *
	 */
	public function setProtection($permisson, $userPassword = '', $ownerPassword = '') {
		if (func_get_args()[2] === NULL) {
			$ownerPassword = bin2hex(openssl_random_pseudo_bytes(8));
		};
		return $this->mpdf->SetProtection($permisson, $userPassword, $ownerPassword);
	}

	/**
	 * Sets the watermark image for the PDF
	 *
	 * @param string $src Image file
	 * @param string $alpha Transparency of the image
	 * @param integer or array $size Defines the size of the watermark.
	 * @param array $position Array of $x and $y defines the position of the watermark.
	 *
	 */
	public function setWatermarkImage($src, $alpha = 0.2, $size = 'D', $position = 'P') {
		$this->mpdf->showWatermarkImage = true;
		return $this->mpdf->SetWatermarkImage($src);
	}

	/**
	 * Sets a watermark text for the PDF
	 *
	 * @param string $text Text for watermark
	 * @param string $alpha Transparency of the text
	 *
	 */
	public function setWatermarkText($text, $alpha = 0.2) {
		$this->mpdf->showWatermarkText = true;
		return $this->mpdf->SetWatermarkText($text);
	}

	public function __call($name, $arguments){
		return call_user_func_array(array($this->mpdf, $name), $arguments);
	}

	/**
	 * Output the PDF as a string.
	 *
	 * @return reference to mPDF instance
	 */
	public function instance(){
		return $this->mpdf;
	}

}
