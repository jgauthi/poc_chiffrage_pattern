<?php
use Jgauthi\Tools\Chiffrage\Pattern as ChiffragePattern;

// In this example, the vendor folder is located in root poc
require_once __DIR__.'/../vendor/autoload.php';

$total = 0;
$debug = false;

// Set Default examples
if (!isset($_POST['var'])) {
    $example = [
        'Liste des taches:',
        '* Task1 Menu Article LorumIpsu £ma2',
        '* Task2 Feature Service DolorColor £fs3',
        '* Task3 Template Page Lorem £tp',
        '* Task4 Block Article Ipsu £ba4',
    ];
    $_POST['var'] = implode("\n", $example);
}

if (!empty($_POST['var'])) {
    $chiffrage = new ChiffragePattern;

    // Détecter les pattern dans les taches
    $regexp = ChiffragePattern::REGEXP;
    $taches = explode("\n", $_POST['var']);

    // Calculer les chiffrages
    foreach ($taches as $id => $tache) {
        if (!preg_match("#({$regexp})(=[0-9\.]+h)?#i", $tache, $extract)) {
            continue;
        }

        $pattern = $extract[1];
        $calcul = $chiffrage->calcul($pattern);

        if ($debug) {
            var_dump("Calcul du pattern {$pattern}, extract:", $extract);
        }

        if (empty($calcul)) {
            continue;
        }

        // Retirer un calcul précédent renseigné dans la liste des taches
        if (!empty($extract[5])) {
            $tache = str_replace($pattern . $extract[5], $pattern, $tache);
        }

        if (isset($_POST['time_only'])) {
            $taches[$id] = str_replace($pattern, $calcul . 'h', $tache);
        } else {
            $taches[$id] = str_replace($pattern, $pattern . '=' . $calcul . 'h', $tache);
        }

        $total += $calcul;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Chiffrage Pattern</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<main role="main" class="container">
    <div class="row">
    <h1>Calcul chiffrage à partir de pattern</h1>

        <form action="<?=basename($_SERVER['PHP_SELF']); ?>" method="post" class="col-sm-6">
        <fieldset>
            <label for="var">Liste des taches (1 pattern chiffrage optionnel par ligne):</label><br />
            <textarea name="var" id="var" cols="50" rows="20" class="form-control"><?php if (isset($_POST['var'])) {
        echo htmlentities(trim($_POST['var']), ENT_QUOTES, 'UTF-8');
    } ?></textarea>

            <br />
            <div style="float: right"><input type="submit" value="Envoyer"  /></div>
            <label>
                <input type="checkbox" name="time_only" value="1"<?=((isset($_POST['time_only'])) ? ' checked="checked"' : null); ?>>
                Retirer les patterns
            </label>

        </fieldset>
        </form>

        <?php if (!empty($total) && !empty($taches)) : ?>
        <fieldset class="col-sm-6">
            <h3>
                Résultat du script
                <em style="font-size: 0.5em;">(<strong>Total:</strong> <?=number_format($total, 2, ',', ' '); ?>h)</em>
            </h3>
            <textarea name="var" cols="50" rows="20" class="form-control" style="padding: 5px; color: blue;" readonly><?=implode("\n", $taches); ?></textarea>
        <fieldset>
        <?php endif ?>

    </div>
</main>
</body>
</html>