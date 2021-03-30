<!DOCTYPE html>
<html lang="de">
	<head>
		<title>Junges Münsterschwarzach - Fürbitten-Sammler</title>
		<meta name="author" content="Lucas 'Pad' Kinne">
		<meta charset="utf-8">
		<link rel="icon" href="favicon.png">
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<link rel="stylesheet" href="/css/stylesheet.css">
		<script src="/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="container">
			<?php
				if ($_SERVER["REQUEST_METHOD"] === "POST") {
					try {
						$secretsDir = getenv("JMIC_SECRETS") ? getenv("JMIC_SECRETS") : "/var/data/secrets/jma";
						$conDets = json_decode(file_get_contents($secretsDir."/database.json"), true);
						$mysqli = new mysqli(
							$conDets["server"],
							$conDets["username"],
							$conDets["password"],
							$conDets["database"]
						);
						$mysqli->set_charset($conDets["encoding"]);

						$public = isset($_POST["public"]);
						$stmt = $mysqli->prepare(
							"INSERT INTO intercessions(text, public) VALUES(?, ?)"
						);
						$stmt->bind_param("si", $_POST["text"], $public);
			
						if ($stmt->execute() === false) {
							$stmt->close();
							throw new Exception("Bitte melde bei 'lucas.kinne@pfarrei-meiningen.de'!");
						}
						$stmt->close();

						$alert = array(
							"level" => "Erfolg!",
							"message" => "Deine Fürbitte wurde gespeichert und wird am Karfreitag ins Gebet mit aufgenommen.",
							"type" => "success"
						);
						unset($_POST);
					} catch(Exception $exc) {
						$alert = array(
							"level" => "Fehler!",
							"message" => $exc->getMessage(),
							"type" => "danger"
						);
					} finally {
						unset($mysqli);
					}
				}
			?>
			<?php if (isset($alert)) { ?>
				<div class="alert alert-<?php echo($alert["type"]);?>">
					<span><strong><?php echo($alert["level"]);?></strong> <?php echo($alert["message"]); ?></span>
				</div>
			<?php } ?>
			<div class="jumbotron jmic-background-color mt-4 p-3">
				<div class="d-flex justify-content-between align-items-center">
					<h1 class="d-inline-block m-2 jmic-important">Fürbitten-Sammler</h1>
					<img class="d-inline-block m-2" src="/logo.png" height="100" width="100"/>
				</div>
				<hr>
				<p>Mit diesem Formular kannst du uns deine <strong>Fürbitten für den Karfreitag</strong> zusenden.</p>
				<p>Deine Fürbitten werden (nach manueller Überprüfung) am Karfreitag ab 20.30 Uhr in unserem <a href="https://www.youtube.com/channel/UChTuwpUOsenleoqrjbVTTRQ/live">YouTube-Livestream</a> öffentlich vorgelesen und in unser Gebet mit aufgenommen.</p>
				<p>Wenn du nicht möchtest, dass deine Fürbitten <strong>öffentlich vorgetragen</strong> werden, dann setze unten im Formular <strong>keinen Haken</strong>, sodass wir sie zwar ins Gebet mit aufnehmen, aber <i>nicht</i> öffentlich vorlesen werden.</p>
				<hr>
				<form name="form" method="POST" class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-12" for="text"><strong>Deine Fürbitte:</strong></label>
						<div class="col-12">
							<textarea name="text" class="form-control" placeholder="Trage hier deine Fürbitte ein." 
								rows="15" required><?php if (isset($_POST["text"]) === true) echo($_POST["text"]);?></textarea>
						</div>
					</div>
					<div class="form-check mt-4">
						<input class="form-check-input" type="checkbox" for="public" <?php if (isset($_POST["public"]) === true && $_POST["public"] === 1) echo("checked");?>>
						<label class="form-check-label" for="public">
							Ich bin damit einverstanden, dass meine Fürbitten <strong>öffentlich im Livestream vorgetragen</strong> werden.
						</label>
					</div>
					<hr>
					<div class="form-group mt-4">
						<div class="col-12">
							<button id="submit" type="submit" class="btn btn-success">Absenden</button>
						</div>
					</div>
				</form>	
			</div>
		</div>
	</body>
</html>