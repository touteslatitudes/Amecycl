<?php
/**
 * Base class for template engine
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/public
 */
 
class ACY_Template
{
	private $attributs = array();
	private $properties = array();

	public function __construct( $file )
	{
		$this->attributs['file'] = $file; // enregistrement du nom du fichier
	}

	public function assign( $property, $value )
	{
		$this->properties[ $property ] = $value; // enregistrement des proprietes
	}
	public function parse()
	{
		extract( $this->properties ); // creer les variables a utiliser

		ob_start();

		require $this->attributs['file']; // lecture et inclusion du fichier template

		$result = ob_get_contents();

		ob_end_clean();

		return $result;
	}
}