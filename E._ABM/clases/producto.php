<?php
class Producto
{
//--------------------------------------------------------------------------------//
//--ATRIBUTOS
	private $codBarra;
 	private $nombre;
  	private $pathFoto;
//--------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------//
//--GETTERS Y SETTERS
	public function GetCodBarra()
	{
		return $this->codBarra;
	}
	public function GetNombre()
	{
		return $this->nombre;
	}
	public function GetPathFoto()
	{
		return $this->pathFoto;
	}

	public function SetCodBarra($valor)
	{
		$this->codBarra = $valor;
	}
	public function SetNombre($valor)
	{
		$this->nombre = $valor;
	}
	public function SetPathFoto($valor)
	{
		$this->pathFoto = $valor;
	}

//--------------------------------------------------------------------------------//
//--CONSTRUCTOR
	public function __construct($codBarra=NULL, $nombre=NULL, $pathFoto=NULL)
	{
		if($codBarra !== NULL && $nombre !== NULL){
			$this->codBarra = $codBarra;
			$this->nombre = $nombre;
			$this->pathFoto = $pathFoto;
		}
	}

//--------------------------------------------------------------------------------//
//--TOSTRING	
  	public function ToString()
	{
	  	return $this->codBarra." - ".$this->nombre." - ".$this->pathFoto."\r\n";
	}
//--------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------//
//--METODOS DE CLASE
	public static function Guardar($obj)
	{
		$resultado = FALSE;
		
		//ABRO EL ARCHIVO
		$ar = fopen("archivos/productos.txt", "a");
		
		//ESCRIBO EN EL ARCHIVO
		$cant = fwrite($ar, $obj->ToString());
		
		if($cant > 0)
		{
			$resultado = TRUE;			
		}
		//CIERRO EL ARCHIVO
		fclose($ar);
		
		return $resultado;
	}
	public static function GuardarEnBase($obj)
	{
		$resultado = true;
		$host = "localhost";
		$user = "root";
		$pass = "";
		$base = "productos";
		$tabla = "productos";
		
		//ABRO LA CONEXION
		//$con = mysqli_connect("localhost" , "root" , "" ,"productos");
		$conexcion = new MySQLi($host , $user , $pass , $base);
		
		//ESCRIBO EN EL ARCHIVO
		$INSERT = "INSERT INTO {$tabla} (codBarra, nombre, pathFoto)
		VALUES({$obj->codBarra}, '{$obj->nombre}', '{$obj->pathFoto}')";

		//mysql_db_query("productos", $sql);
		$conexcion->query($INSERT);
		
		//CIERRO LA CONEXCION
		//mysqli_close($con);
		$conexcion->close();
		
		return $resultado;
	}
	public static function TraerTodosLosProductos()
	{

		$ListaDeProductosLeidos = array();

		//leo todos los productos del archivo
		$archivo=fopen("archivos/productos.txt", "r");
		
		while(!feof($archivo))
		{
			$archAux = fgets($archivo);
			$productos = explode(" - ", $archAux);
			//http://www.w3schools.com/php/func_string_explode.asp
			$productos[0] = trim($productos[0]);
			if($productos[0] != ""){
				$ListaDeProductosLeidos[] = new Producto($productos[0], $productos[1],$productos[2]);
			}
		}
		fclose($archivo);
		
		return $ListaDeProductosLeidos;
		
	}
	public static function TraerDeBase() {

		$resultado = true;
		$host = "localhost";
		$user = "root";
		$pass = "";
		$base = "productos";
		$tabla = "productos";
		$productos = array();

		$conexcion = new MySQLi($host , $user , $pass , $base);
		$SELECT = "SELECT * FROM {$tabla} WHERE 1";
		$elementos = $conexcion->query($SELECT);
		$conexcion->close();

		//var_dump($elementos);
		//var_dump($elementos->fetch_object());

		while($item = $elementos->fetch_object()) {

			array_push($productos , $item);
		}

		return $productos;
	}
	public static function Modificar($obj)
	{
		$resultado = TRUE;
		$contador = 0;
		
		//OBTENGO TODOS LOS PRODUCTOS
		$productos = Producto::TraerTodosLosProductos();
		//RECORRO Y BUSCO LA IMAGEN ANTERIOR. REEMPLAZO POR EL OBJ. MODIFICADO
		foreach($productos as $item) {

			if($item->codBarra == $obj->codBarra) {

				$imagen = $item->pathFoto;
				$productos[$contador] == $obj;
				break;
			}

			$contador++;
		}
		//BORRO LA IMAGEN ANTERIOR
		unlink($imagen);
		
		//ABRO EL ARCHIVO
		$ar = fopen("archivos/productos.txt" , "w");
		//ESCRIBO EN EL ARCHIVO
		foreach($productos as $producto) {

			fwrite($ar , $producto->tostring());
		}
		//CIERRO EL ARCHIVO
		fclose($ar);
		
		return $resultado;
	}
	public static function Eliminar($codBarra)
	{
		$resultado = TRUE;
		
		$contador=0;
				
		//OBTENGO TODOS LOS PRODUCTOS
		$productos = Producto::TraerTodosLosProductos();
		//RECORRO Y BUSCO LA IMAGEN ANTERIOR.
		foreach($productos as $item) {
					
			if($item->codBarra == $codBarra) {
					
				$imagen = $item->pathFoto;
				unset($productos[$contador]);
				break;
			}
					
			$contador++;
		}
		//BORRO LA IMAGEN ANTERIOR

		$ruta = "archivos/".$imagen;

		Archivo::Borrar($ruta);
				
		//ABRO EL ARCHIVO
		$ar = fopen("archivos/productos.txt" , "w");
		//ESCRIBO EN EL ARCHIVO
		foreach($productos as $producto) {
		
			fwrite($ar , $producto->tostring());
		}
		//CIERRO EL ARCHIVO
		fclose($ar);
				
		return $resultado;
	}
	public static function EliminarDeBase($codBarra) {

		$resultado = true;
		$host = "localhost";
		$user = "root";
		$pass = "";
		$base = "productos";
		$tabla = "productos";
		$productos = Producto::TraerDeBase();

		$conexcion = new MySQLi($host , $user , $pass , $base);
		$DELETE = "DELETE FROM {$tabla} WHERE codBarra={$codBarra}";

		foreach($productos as $item) {

			if($item->codBarra = $codBarra) {

				$imagen = $item->pathFoto;
			}
		}

		Archivo::Borrar("archivos/".$imagen);

		$conexcion->query($DELETE);
		$conexcion->close();

		return $resultado;
	}
//--------------------------------------------------------------------------------//
}