<?php

$dbh = require('includes/db.php');

$sql = "
    SELECT 
        name_id,
        (total - 
        (
            SELECT count(*) 
            FROM paypal_transactions
            WHERE website_sport = name_id 
        )) as remainingSlots
    FROM sport_limits 
    INNER JOIN sports ON fk_sport_id = sports.id
";
$stmt = $dbh->prepare($sql);

$stmt->execute();

$rawRemainingSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);
$remainingSlots = array();

foreach ($rawRemainingSlots as $key => $sport) {
    $remainingSlots[$sport['name_id']] = max(0, ((int) $sport['remainingSlots']));
}

function formatRemainingPlacesText($nPlaces) {
    if ($nPlaces === 0) {
        return 'Aucune place restante';
    } else if ($nPlaces === 1) {
        return '1 place restante';
    } else {
        return $nPlaces.' places restantes';
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="home">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Pratique ton sport</title>
        
        <link rel="stylesheet" href="styles/compiled/styles.css" />

        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    </head>
    <body>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/fr_CA/sdk.js#xfbml=1&version=v2.8&appId=110238456300991";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        
        <div id="wrapper">
            <header>
                <div class="centered-wrapper">
                    <div class="logo">
                        <img src="images/logo.svg" />
                    </div>
                    <div class="menu">
                        <a href="#ligues" class="item">Ligues</a>
                        <a href="#horaire" class="item">Horaire</a>
                        <a href="#contact" class="item">Contact</a>
                        <a href="#inscrire" class="item subscribe">S'inscrire</a>
                    </div>
                </div>
            </header>
            <div class="zone-intro">
                <div class="side-image"></div>
                <div class="content">
                    <div class="vertical-centered">
                        <div class="centered-wrapper">
                            <h2>Vous aimez bouger?</h2>
                            <h1>Pratique ton sport</h1>
                        </div>
                    </div>
                    <div class="bottom-infos">
                        <div class="centered-wrapper">
                            <div class="contact-infos">
                                <div class="phone">579 888-8844</div>
                                <div class="email"><a href="#"><!-- dynamic --></a></div>
                            </div>
                            <div class="social">
                                <a href="https://www.facebook.com/Pratiquetonsport/" target="_blank">
                                    Facebook <img src="images/facebook.png"/>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="zone-goal">
                <div class="background-gradient"></div>
                <div class="centered-wrapper">
                    <h2>Notre but</h2>
                    <p class="goal-description">
                        Vous aimez bouger et faire du sport? Alors, Pratique ton sport c’est pour vous! 
                        Nous vous offrons la possibilité de pratiquer <em class="yellow">votre sport</em> dans des conditions amicales et avec un entraîneur qui sera à l’écoute pour vous aider à vous surpasser comme vous le souhaitez.
                    </p>
                    <div class="list clearfix" id="ligues">
                        <div class="sport">
                            <img src="images/sport-volleyball.jpg" />
                            <div class="tabs">
                                <div class="tab black">Débutant</div>
                                <div class="tab">2h/sem</div>
                            </div>
                            <div class="infos">
                                <h3>Volleyball</h3>
                                <p class="price">135$/personne</h3>
                                <p class="desc">
                                    Apprenez les techniques de bases du volleyball tel que manchette, touche, service, réception et autres, ainsi que les rotations de jeu, afin de bien les appliquer lors de vos nombreuses parties.
                                </p>
                                <div class="school">École secondaire Cap Jeunesse</div>
                            </div>
                        </div>
                        <div class="sport">
                            <img src="images/sport-volleyball-2.jpg" />
                            <div class="tabs">
                                <div class="tab black">Intermédiaire</div>
                                <div class="tab">2h/sem</div>
                            </div>
                            <div class="infos">
                                <h3>Volleyball</h3>
                                <p class="price">135$/personne</h3>
                                <p class="desc">
                                    Cette ligue est pour perfectionner vos compétences du volleyball tel que les «spikes», position spécifique, attaque de précision et autres, afin de bien les appliquer lors de vos nombreuses parties.
                                </p>
                                <div class="school">Polyvalente Saint-Jérôme</div>
                            </div>
                        </div>
                        <div class="sport">
                            <img src="images/sport-frisbee.jpg" />
                            <div class="tabs">
                                <div class="tab black">Pour tous</div>
                                <div class="tab">1h30/sem</div>
                            </div>
                            <div class="infos">
                                <h3>Ultimate Frisbee</h3>
                                <p class="price">110$/personne</h3>
                                <p class="desc">
                                    Apprenez les techniques de base du l’ultimate frisbee tel que le lancer, la réception, les positionnements et autres, ainsi que les règles, afin de bien les appliquer lors de vos nombreuses parties.
                                </p>
                                <div class="school">École primaire Sans Frontières</div>
                            </div>
                        </div>
                        <div class="sport">
                            <img src="images/sport-hockey.jpg" />
                            <div class="tabs">
                                <div class="tab black">Pour tous</div>
                                <div class="tab">1h/sem</div>
                            </div>
                            <div class="infos">
                                <h3>Hockey cosom</h3>
                                <p class="price">120$/personne ou 900$/équipe</h3>
                                <p class="desc">
                                    Jouez des parties de hockey cosum de façon compétitive mais amicale, avec la présence d’un conseiller/arbitre pour vous aider à vous perfectionner si vous en sentez le besoin. Cette ligue comprend des séries qui permettra de remporter le trophée de la saison. L'équipement n'est pas fournis sauf pour les balles. Prendre note que les palettes en bois ainsi que le ruban de bâton d'hockey sont interdits.
                                </p>
                                <div class="school">École secondaire Hauts Sommets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="zone-leagues">
                <div class="background-gradient"></div>
                <div class="wrapper clearfix">
                    <div class="left">
                        <div class="text-container">
                            <p class="price">10$</p>
                            <p class="description">pour un essaie ou quand vous êtes disponible *</p>
                        </div>
                    </div>
                    <div class="right">
                        <div class="content">
                            <h2>Chaque ligue</h2>
                            <p class="description">
                                Vous jouez dans une ligue pour une durée de 15 semaines et la plupart des ligues sont mixtes. Un entraîneur et conseiller est sur place pour vous aider à progresser dans la pratique de votre sport.
                            </p>
                            <p class="current-season">
                                <span>Saison automne 2017</span><br/>
                                11 septembre au 22 décembre (15 semaines)
                            </p>
                            <p class="other-infos">
                                Les tarifs peuvent changer sans préavis.
                            </p>
                            <p class="other-infos">
                                * Possibilité de payer 10$ pour un essaie d’une soirée ou simplement les soirées que vous êtes disponible (paiement sur place).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="zone-schedule" id="horaire">
                <div class="centered-wrapper">
                    <div class="left">
                        <h2>Horaire des ligues</h2>
                    </div>
                    <div class="right">
                        <p class="desc">Semaine du 11 septembre jusqu’à la semaine du 18 décembre (inclusivement)</p>
                        <div class="list">
                            <div class="item clearfix">
                                <div class="day">Mercredi</div>
                                <div class="time">21h00 à 22h30</div>
                                <div class="sport">Ultimate Frisbee</div>
                            </div>
                            <div class="item clearfix">
                                <div class="day">Jeudi</div>
                                <div class="time">20h30 à 22h30</div>
                                <div class="sport">Volleyball (débutant)</div>
                            </div>
                            <div class="item clearfix">
                                <div class="day">Vendredi</div>
                                <div class="time">19h00 à 21h00</div>
                                <div class="sport">Volleyball (intermédiare)</div>
                            </div>
                            <div class="item clearfix">
                                <div class="day">Vendredi</div>
                                <div class="time">19h00 à 21h00</div>
                                <div class="sport">Ligue d'hockey cosum</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="zone-form" id="inscrire">
                <div class="centered-wrapper">
                    <div class="left">
                        <h2>Inscription</h2>
                    </div>
                    <div class="right">
                        <div class="form">
                            <div class="form-fields">
                                <div class="clearfix">
                                    <input class="firstname" type="text" placeholder="Prénom">
                                    <input class="lastname" type="text" placeholder="Nom">
                                </div>
                                <input class="email" type="text" placeholder="Adresse courriel">
                            </div>
                            <div class="sport-list clearfix">
                                <div class="item <?php echo (($remainingSlots['1_volleyball_debutant']) ? '' : 'full') ?>" data-sport-id="1_volleyball_debutant">
                                    <div class="name">Volleyball débutant</div>
                                    <div class="price">135$<sup>*</sup>/personne</div>
                                    <div class="places"><?php echo formatRemainingPlacesText($remainingSlots['1_volleyball_debutant']) ?></div>
                                </div>
                                <div class="item <?php echo (($remainingSlots['2_volleyball_intermediaire']) ? '' : 'full') ?>" data-sport-id="2_volleyball_intermediaire">
                                    <div class="name">Volleyball intermédiaire</div>
                                    <div class="price">135$<sup>*</sup>/personne</div>
                                    <div class="places"><?php echo formatRemainingPlacesText($remainingSlots['2_volleyball_intermediaire']) ?></div>
                                </div>
                                <div class="item <?php echo (($remainingSlots['3_ultimate_frisbee']) ? '' : 'full') ?>" data-sport-id="3_ultimate_frisbee">
                                    <div class="name">Ultimate frisbee</div>
                                    <div class="price">110$<sup>*</sup>/personne</div>
                                    <div class="places"><?php echo formatRemainingPlacesText($remainingSlots['3_ultimate_frisbee']) ?></div>
                                </div>
                                <div class="item <?php echo (($remainingSlots['4_hockey_cosum']) ? '' : 'full') ?>" data-sport-id="4_hockey_cosum">
                                    <div class="name">Hockey cosum</div>
                                    <div class="price">120$<sup>*</sup>/personne</div>
                                    <div class="places"><?php echo formatRemainingPlacesText($remainingSlots['4_hockey_cosum']) ?></div>
                                </div>
                                <div class="item <?php echo (($remainingSlots['5_hockey_cosum_team']) ? '' : 'full') ?>" data-sport-id="5_hockey_cosum_team">
                                    <div class="name">Hockey cosum</div>
                                    <div class="price">900$<sup>*</sup>/équipe</div>
                                    <div class="places"><?php echo formatRemainingPlacesText($remainingSlots['5_hockey_cosum_team']) ?></div>
                                </div>
                            </div>
                            <div class="paypal-link" href="">
                                <div>Finaliser l'inscription</div>
                                <div class="paypal-bt"></div>
                            </div>
                            <div class="star-infos">
                            *: Taxes en sus
                            </div>
                        </div>
                        <div class="form-success">
                            <p class="title">Félicitation, vous êtes maintenant inscrit.</h2>
                        </div>
                        <div class="form-error">
                            <p class="title">Une erreur est survenu lors de l'enregistrement. <br/>Veuillez <a href="?refresh#zone-form">réinitialiser</a> la page et essayez de nouveau.</p>
                            <p class="subtitle">Aucun paiment n'a été effectué.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="zone-comments" id="contact">
                <div class="fb-wrapper">
                    <div class="fb-comments" data-href="http://pratiquetonsport.com" data-width="1140" data-numposts="10" data-order-by="reverse_time"></div>
                </div>
            </div>
            <footer>
                <div class="centered-wrapper">
                    <div class="left">
                        <div class="phone">579 888-8844</div>
                        <div class="email"><a href="#"><!-- dynamic --></a></div>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="js/payment.js"></script>
        <script src="js/menu.js"></script>
        <script type="text/javascript">
            jQuery('footer .email a, .contact-infos .email a').text('pratiquetonsport' + '@' + 'gmail.com')
            jQuery('footer .email a, .contact-infos .email a').attr('href', 'mailto:pratiquetonsport' + '@' + 'gmail.com')
        </script>

        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-104392993-1', 'auto');
          ga('send', 'pageview');

        </script>
    </body>
</html>