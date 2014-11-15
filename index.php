<?php
#Define constantes de configuração
define("PATH", "/var/www/html/");
define("URL", "localhost");
define("HOST", "localhost");
define("DB", "mysql");
define("USER", "root");
define("PASS", "011224");

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
							  echo "<span class='linha-diretorio'><img src='img/pasta.png' height='14' width='16'><a href='http://".URL."/{$arquivo}'>{$arquivo}</a></span><br/>";
							endif;
						endforeach
		    			?>
	    			</div>
	    			<div id="box-diretorio">
	    			<h2>Arquivos</h2>
		    			<?php
		    			foreach($array_file as $arquivo):
		    				if ($arquivo != '.' && $arquivo != '..' && !is_dir($arquivo)):
							  echo "<span class='linha-diretorio'><img src='img/arquivo.png' height='18' width='16'><a href='http://".URL."/{$arquivo}'>{$arquivo}</a></span><br/>";
							endif;
						endforeach
		    			?>
	    			</div>
	    		</div>
	    		<hr>
	    		<div id="rodape">
	    			Desenvolvido por William F. Leite<br/><br/>
	    			Blog: <a href="http://devwilliam.blogspot.com.br">devwilliam.com.br</a>
	    		</div>
	    	</div>
	    </div>
    </body>
</html>