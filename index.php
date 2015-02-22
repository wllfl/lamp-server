<?php
#Define constantes de configuração
define("PATH", "/var/www/html/");
define("URL", "localhost");
define("HOST", "localhost");
define("DB", "mysql");
define("USER", "root");
define("PASS", "011224");

# Captura submissão para exclusão de pastas ou arquivos
$file = (isset($_POST['file'])) ? $_POST['file'] : '' ;

if($file != ""):
	if(is_dir($file)):
		ExcluiDir($file);
	else:
		unlink(PATH.$file);
	endif;
endif;
unset($file);

# Função para exlcluir diretórios, sub-diretórios e arquivos
function ExcluiDir($pasta){
    
    if ($dd = opendir($pasta)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$pasta/$Arq";
                if(is_dir($Path)){
                    ExcluiDir($Path);
                }elseif(is_file($Path)){
                    unlink($Path);
                }
            }
        }
        closedir($dd);
    }
    rmdir($pasta);
}

# Captura versão do servidor MySQL instalado
try{
	$pdo = new PDO('mysql:host='.HOST.'; dbname='.DB.';', USER, PASS);
	$versao = "Indisponível";
	if ($pdo):
		$sql = "SELECT version() AS versao";
		$stm = $pdo->prepare($sql);
		$stm->execute();
		$dados = $stm->fetch(PDO::FETCH_OBJ);
		$versao = $dados->versao;
	endif;
}catch (PDOException $erro){
	echo "Erro ao verificar versão do servidor MySQL: " . $erro->getmessage();
	$versao = "Indisponível";
}

# Função para capturar permissões de arquivos
function getPermissao($file){
	$perms = fileperms($file);

	if (($perms & 0xC000) == 0xC000) {
	    // Socket
	    $info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
	    // Link simbólico
	    $info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
	    // Regular
	    $info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
	    // Bloco especial
	    $info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
	    // Diretório
	    $info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
	    // Caractere especial
	    $info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
	    // FIFO pipe
	    $info = 'p';
	} else {
	    // Desconhecido
	    $info = 'u';
	}

	// Proprietário
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
	            (($perms & 0x0800) ? 'S' : '-'));

	// Grupo
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
	            (($perms & 0x0400) ? 'S' : '-'));

	// Outros
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

	return $info;
}

# Captura pastas e arquivos dentro do diretório /var/www/
$diretorio = dir(PATH);
$array_dir = array();
while($arquivo = $diretorio->read()):
		$array_dir[strtolower($arquivo)] = $arquivo;
endwhile;
ksort($array_dir);

$arquivos = dir(PATH);
$array_file = array();
while($arquivo = $arquivos->read()):
		if (is_file($arquivo)):
			$array_file[strtolower($arquivo)] = $arquivo;
		endif;
endwhile;
ksort($array_file);

?>
<html>
    <head>
        <title>LAMP - Linux</title>
        <meta http-equiv="Content-Type" content="text/html; utf-8"/>
        <meta http-equiv="content-language" content="pt-br" />
        <link rel='stylesheet' type='text/css' href='css/reset.css'/>
        <link rel='stylesheet' type='text/css' href='css/estilo.css'/>
    </head>
    <body>
	    <div id="tudo">
	    	<div id="conteudo">
	    		<div id="topo" class="clearfix">
	    			<img src="img/linux.png" class="esquerda" height="140" width="160">
	    			<img src="img/lamp.png" class="direita" height="140" width="160">
	    			<div id="box-informacao">
	    				<h2>Informações</h2>
		    			<span class="label">Versão do PHP: </span><?= phpversion();?><br/><br/>
		    			<span class="label">Versão do Servidor Apache: </span><?= apache_get_version();?><br/><br/>
		    			<span class="label">Versão do Servidor MySQL: </span><?= $versao;?><br/><br/>
		    			<span class="label">Detalhes PHP: </span><a target="_blank" href="phpinfo.php">phpinfo()</a><br/>			
	    			</div>
	    		</div>

	    		<div id="extensoes" class="clearfix">
	    			<hr>
	    			<h2>Extensões Habilitadas</h2>
	    			<?php
    				$extensoes = get_loaded_extensions();
    				$contador  = 0;
    				$limite    = 10;

    				foreach($extensoes as $name):
    					if ($contador < $limite) echo "<span class='box-extensao' id='box-1'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
		    			if ($contador >= $limite && $contador < ($limite * 2)) echo "<span class='box-extensao' id='box-2'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
		    			if ($contador >= ($limite * 2) && $contador < ($limite * 3)) echo "<span class='box-extensao' id='box-3'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
		    			if ($contador >= ($limite * 3) && $contador < ($limite * 4))echo "<span class='box-extensao' id='box-4'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
		    			if ($contador >= ($limite * 4) && $contador < ($limite * 5))echo "<span class='box-extensao' id='box-5'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
		    			if ($contador >= ($limite * 5) && $contador < ($limite * 6))echo "<span class='box-extensao' id='box-5'><img src='img/ok.png' height='14' width='16'>{$name}</span>";
	    				$contador++; 
	    			endforeach;
	    			?>
	    		</div>

	    		<div id="projetos" class="clearfix">
	    			<hr>
	    			<h2>Diretórios e Arquivos em <?=PATH?></h2>
	    			<div id="box-diretorio">
	    			<h2>Diretórios</h2>
		    			<?php
		    			foreach($array_dir as $arquivo):
		    				if ($arquivo != '.' && $arquivo != '..' && is_dir($arquivo)):
		    				    $permissao = getPermissao($arquivo);
							    echo "<span class='linha-diretorio' title='Permissões {$permissao}'>
							    		<form id='form_{$arquivo}' method='POST'>
							    		<input type='hidden' name='file' value='{$arquivo}'>
							    		<img src='img/excluir.png' height='15' width='16' title='Excluir Pasta' onclick='valida(\"{$arquivo}\");'>
								    	<img src='img/pasta.png' height='14' width='16'><a target='_blank' href='http://".URL."/{$arquivo}'>{$arquivo}</a>
								    	</form>
								    </span>";
							endif;
						endforeach
		    			?>
	    			</div>
	    			<div id="box-diretorio">
	    			<h2>Arquivos</h2>
		    			<?php
		    			foreach($array_file as $arquivo):
		    				if ($arquivo != '.' && $arquivo != '..' && !is_dir($arquivo)):
							    echo "<span class='linha-diretorio' title='Permissões {$permissao}'>
										<form id='form_{$arquivo}' method='POST'>
										<input type='hidden' name='file' value='{$arquivo}'>
										<img src='img/excluir.png' height='16' width='16' title='Excluir Arquivo' onclick='valida(\"{$arquivo}\");'>
										<img src='img/arquivo.png' height='18' width='16'><a target='_blank' href='http://".URL."/{$arquivo}'>{$arquivo}</a>
										</form>
									 </span>";
							endif;
						endforeach
		    			?>
	    			</div>
	    		</div>
	    		<hr>
	    		<div id="rodape">
	    			Desenvolvido por William F. Leite<br/><br/>
	    			Blog: <a href="http://devwilliam.blogspot.com.br">devwilliam.com.br</a><br/><br/>
	    			GitHub: <a href="https://github.com/wllfl/lamp-server">https://github.com/wllfl/lamp-server</a>
	    		</div>
	    	</div>
	    </div>
	    <script type="text/javascript">
	    function valida(file){
	    	var retorno = confirm("Deseja excluir esse arquivo?");
	    	if(retorno){
	    		document.forms['form_'+file].submit();
	    	}
	    }
	    </script>
    </body>
</html>