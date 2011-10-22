<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: settings-help.lang.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Settings help translation strings
$GLOBALS['phpAds_hlp_dbhost'] = "\n        Specifica l'hostname del server ".$phpAds_dbmsname." al quale ".MAX_PRODUCT_NAME." si deve connettere.\n		";

$GLOBALS['phpAds_hlp_dbport'] = "\n        Specifica il numero della porta del server ".$phpAds_dbmsname." al quale ".MAX_PRODUCT_NAME." si deve\n		connettere. La porta di default di ".$phpAds_dbmsname." è la <i>" . ($phpAds_dbmsname == 'MySQL' ? '3306' : '5432')."</i>.";

$GLOBALS['phpAds_hlp_dbuser'] = "\n        Specifica il nome utente che ".MAX_PRODUCT_NAME." deve utilizzare per connettersi al server ".$phpAds_dbmsname.".\n		";

$GLOBALS['phpAds_hlp_dbpassword'] = "\n        Specifica la password che ".MAX_PRODUCT_NAME." deve utilizzare per connettersi al server ".$phpAds_dbmsname.".\n		";

$GLOBALS['phpAds_hlp_dbname'] = "\n        Specifica il nome del database dove ".MAX_PRODUCT_NAME." deve salvare i dati.\n		È importante che il database sia già stato creato sul server. ".MAX_PRODUCT_NAME." <b>non</b> creerà\n		il database se non esiste.\n		";

$GLOBALS['phpAds_hlp_persistent_connections'] = "\n        L'utilizzo delle connessioni persistenti può velocizzare considerevolmente ".MAX_PRODUCT_NAME."\n		e può anche diminuire il carico di lavoro del server. C'è però una controindicazione:\n		nei siti con molti visitatori il carico del server potrebbe aumentare anziché diminuire.\n		La scelta sull'eventuale impiego delle connessioni persistenti deve perciò tenere conto del numero\n		di visitatori e dell'hardware utilizzato. Se ".MAX_PRODUCT_NAME." consume troppe risorse, potrebbe essere\n		necessario modificare questa impostazione.\n		";

$GLOBALS['phpAds_hlp_compatibility_mode'] = "\n		Se si verificano problemi di integrazione di ".MAX_PRODUCT_NAME." con altri prodotti di terze parti\n		potrebbe essere utile attivare la compatibilità. Utilizzando la modalità di invocazione\n		locale e la compatibilità è attiva, ".MAX_PRODUCT_NAME." non dovrebbe alterare lo stato\n		della connessione al database dopo l'uso di ".MAX_PRODUCT_NAME.". Questa opzione produce un (seppur minimo)\n		rallentamento ed è perciò disabilitata di default.\n		";

$GLOBALS['phpAds_hlp_table_prefix'] = "\n		Se il database utilizzato da ".MAX_PRODUCT_NAME." è condiviso con altri prodotti software\n		è consigiabile utilizzare un prefisso per i nomi delle tabelle. Se più installazioni\n		di ".MAX_PRODUCT_NAME." condividono lo stesso database, assicurati che i prefissi siano differenti.\n		";

$GLOBALS['phpAds_hlp_table_type'] = "\n		".$phpAds_dbmsname." supporta differenti tipi di tabelle. Ognuno ha le proprie caratteristiche ed\n		alcuni possono velocizzare considerevolmente ".MAX_PRODUCT_NAME.". MyISAM è il tipo\n		utilizzato di default ed è disponibile in tutte le installazioni di ".$phpAds_dbmsname.".\n		Gli altri potrebbero non essere sempre disponibili.\n		";

$GLOBALS['phpAds_hlp_url_prefix'] = "\n		".MAX_PRODUCT_NAME." necessita di sapere il proprio indirizzo sul server web per poter\n		funzionare correttamente. Devi specificare l'URL della directory dove ".MAX_PRODUCT_NAME."\n		è stato installato, per esempio: <i>http://www.tuo-url.com/".MAX_PRODUCT_NAME."</i>.\n		";

$GLOBALS['phpAds_hlp_my_header'] =
$GLOBALS['phpAds_hlp_my_footer'] = "\n		Qui si possono inserire gli indirizzi dei file (p.es. /home/login/www/header.htm)\n		da utilizzare come intestazione e pié di pagina nelle pagine di amministrazione.\n		Si possono utilizzare sia file di testo che html (questi ultimi non devono contenere tag\n		<body> o <html>).\n		";

$GLOBALS['phpAds_hlp_content_gzip_compression'] = "\n		Abilitando la compressione GZIP dei contenuti si otterrà una grande diminuzione\n		dei dei inviati al browser ogni volta che si utilizza l'interfaccia di amministrazione.\n		Per abilitare questa caratteristica è necessario avere almeno PHP 4.0.5 con\n		l'estensione GZIP installata.\n		";

$GLOBALS['phpAds_hlp_language'] = "\n		Specifica la lingua utilizzata di default da ".MAX_PRODUCT_NAME.". Questa verrà\n		usata nelle interfaccia amministratore e inserzionista. Nota Bene: è comunque possibile\n		specificare una lingua diversa per ogni inserzionista ed anche permettere loro di\n		modificare la propria.\n		";

$GLOBALS['phpAds_hlp_name'] = "\n		Specifica il nome da utilizzare per questa applicazione. Questa stringa sarà\n		visuallizzata in tutte le pagine dell'interfaccia utente. Se non viene specificato (stringa\n		vuota) verrà mostrato il logo di ".MAX_PRODUCT_NAME.".\n		";

$GLOBALS['phpAds_hlp_company_name'] = "Questo nome è usato nelle email spedite da ". MAX_PRODUCT_NAME .".";

$GLOBALS['phpAds_hlp_override_gd_imageformat'] = "\n        ".MAX_PRODUCT_NAME." usually detects if the GD library is installed and which image\n        format is supported by the installed version of GD. However it is possible\n        the detection is not accurate or false, some versions of PHP do not allow\n        the detection of the supported image formats. If ".MAX_PRODUCT_NAME." fails to auto-detect\n        the right image format you can specify the right image format. Possible\n        values are: none, png, jpeg, gif.\n		";

$GLOBALS['phpAds_hlp_p3p_policies'] = "\n		Per utilizzare le funzioni P3P Privacy Policies di ".MAX_PRODUCT_NAME." questa opzione deve\n		essere attivata.\n		";

$GLOBALS['phpAds_hlp_p3p_compact_policy'] = "\n		La policy compatta inviata assieme ai cookie. L'impostazione di default è:\n        'CUR ADM OUR NOR STA NID', che permette ad Internet Explorer 6 di accettare i cookie inviati\n		da ".MAX_PRODUCT_NAME.". Volendo queste impostazioni si possono modificare in base alla\n		propria informativa sulla privacy.\n		";

$GLOBALS['phpAds_hlp_p3p_policy_location'] = "\n		Per utilizzare una policy completa sulla privacy, specificare qui l'indirizzo.\n		";

$GLOBALS['phpAds_hlp_compact_stats'] = "\n		Tradizionalmente ".MAX_PRODUCT_NAME." utilizzava un metodo di registrazione delle visualizzazioni\n		e dei click molto dettagliato, ma anche molto esigente in termini di risorse per il database. Questo\n		poteva essere un problema per siti con molti visitatori. Per superare questo problema\n		".MAX_PRODUCT_NAME." supporta anche un nuovo tipo di statistiche, detto compatto, che è\n		sì meno pesante per il database, ma anche meno dettagliato, in quanto non registra\n		gli host. Se quest'ultima caratteristica è necessaria disabilitare questa optzione.\n		";

$GLOBALS['phpAds_hlp_log_adviews'] = "\n		Normalmente tutte le Visualizzazioni sono registrate; se non vuoi raccogliere statistiche\n		sulle Visualizzazioni disattiva questa opzione.\n		";

$GLOBALS['phpAds_hlp_block_adviews'] = "\n		Se un visitatore ricarica una pagina, ".MAX_PRODUCT_NAME." registrerà ogni volta\n		una Visualizzazione. Questa funzione è utile per assicurarsi che sia registrata solo\n		una Visualizzazione per ogni banner differente nell'intervallo di secondi specificato. Ad\n		esempio, se questo valore è impostato a 300 secondi, ".MAX_PRODUCT_NAME." registrerà\n		una Visualizzazione solo se un banner non è già stato mostrato allo stesso\n		visitatore negli ultimi 5 minuti. Questa opzione funziona solo se il browser accetta i cookie.\n		";

$GLOBALS['phpAds_hlp_log_adclicks'] = "\n		Normalmente tutti i Click sono registrate; se non vuoi raccogliere statistiche\n		sui Click disattiva questa opzione.\n        Normally all AdClicks are logged, if you don't want to gather statistics\n        about AdClicks you can turn this off.\n		";

$GLOBALS['phpAds_hlp_block_adclicks'] = "\n		Se un visitatore clicca più volte su un banner ".MAX_PRODUCT_NAME." registrerà\n		ogni volta un Click. Questa funzione è utile per assicurarsi che sia registrata solo\n		un Click per ogni banner differente nell'intervallo di secondi specificato. Ad\n		esempio, se questo valore è impostato a 300 secondi, ".MAX_PRODUCT_NAME." registrerà\n		un Click solo se un banner non è già stato cliccato dallo stesso\n		visitatore negli ultimi 5 minuti. Questa opzione funziona solo se il browser accetta i cookie.\n		";

$GLOBALS['phpAds_hlp_geotracking_stats'] = "\n		Se utilizzi un database per il tracking geografico, è possibile memorizzare anche\n		le informazioni geografiche nel database. Così facendo sarà  possibile vedere la\n		nazionalità dei visitatori e le statistiche suddivise per nazione.\n		Questa opzione è disponibile solo utilizzando le statistiche estese.\n		";

$GLOBALS['phpAds_hlp_reverse_lookup'] = "\n		L'hostname è normalmente determinato dal server web, ma in alcuni casi questa\n		funzionalità potrebbe essere disabilitata. Se il server non fornisce questa\n		indicazione, per poter utilizzare l'hostname dei visitatori all'interno di limitazioni\n		di consegna e/o memorizzarlo nelle statistiche, è necessario abilitare questa\n		opzione. Determinare l'hostname dei visitatori occupa un po' di tempo, perciò\n		la consegna dei banner sarà più lenta.\n		";

$GLOBALS['phpAds_hlp_proxy_lookup'] = "\n		Alcuni visitatori utilizzano un server proxy per accedere a Internet. In questo caso\n		".MAX_PRODUCT_NAME." registrerà l'indirizzo IP o il nome dell'host del\n		server proxy invece di quelli del visitatore. Se questa opzione è abilitata\n		".MAX_PRODUCT_NAME." cercherà di risalire all'indirizzo reale\n		del computer del visitatore dietro al proxy. Se questo non sarà possibile, verrà\n		memorizzato l'indirizzo del server proxy. Questa opzione non è\n		abilitata di default poiché rallenta considerevolmente la registrazione\n		delle Visualizzazioni e dei Click.\n		";

$GLOBALS['phpAds_hlp_auto_clean_tables'] =
$GLOBALS['phpAds_hlp_auto_clean_tables_interval'] = "\n		Abilitando questa opzione, le statistiche raccolte saranno automaticamente cancellate una volta\n		trascorso il periodo specificato. Per esempio: impostando il periodo a 5 settimane, verranno\n		cancellate automaticamente le statistiche più vecchie di 5 settimane.\n		";

$GLOBALS['phpAds_hlp_auto_clean_userlog'] =
$GLOBALS['phpAds_hlp_auto_clean_userlog_interval'] = "\n		Abilitando questa opzione, verranno cancellate automaticamente le informazioni del registro\n		eventi più vecchie del numero di settimane specificato.\n		";

$GLOBALS['phpAds_hlp_geotracking_type'] = "\n		Il targeting geografico permette a ".MAX_PRODUCT_NAME." di convertire l'indirizzo IP del\n		visitatore in una informazione geografica. Tramite questa informazione sarà possibile\n		impostare limitazioni di consegna; inoltre, memorizzando le informazioni geografiche nelle statistiche\n		sarà possibile vedere quale nazione genera più Visualizzazioni o Click.\n		Per abilitare il tracking geografico è necessario selezionare il dipo di database di cui\n		si è in possesso. Al momento ".MAX_PRODUCT_NAME." supporta i database\n		<a href='http://hop.clickbank.net/?phpadsnew/ip2country' target='_blank'>IP2Country</a>\n		e <a href='http://www.maxmind.com/?rId=phpadsnew' target='_blank'>GeoIP</a>.\n		";

$GLOBALS['phpAds_hlp_geotracking_location'] = "\n		A meno che non venga utilizzato il modulo GeoIP per Apache, è necessario fornire a\n		".MAX_PRODUCT_NAME." il percorso del database geografico. È consigliabile posizionarlo\n		al di fuori della radice dei documenti del server (document root), altrimenti estranei potrebbero\n		essere in grado di scaricare il database.\n		";

$GLOBALS['phpAds_hlp_geotracking_cookie'] = "\n		Convertire l'indirizzo IP in informazione geografica consuma del tempo. Per evitare\n		che ".MAX_PRODUCT_NAME." debba farlo ogni volta che un banner viene consegnato, si\n		può memorizzare il risultato in un cookie. Se il cookie è presente ".MAX_PRODUCT_NAME."\n		utilizzerà questa informazione invece di effettuare ancora la conversione dall'indirizzo IP.\n		";

$GLOBALS['phpAds_hlp_ignore_hosts'] = "\n		Se non vuoi che siano registrati Visualizzazioni e Click per determinati computer,\n		puoi aggiungerli a questa lista. Se è abilitata l'opzione <i>".$GLOBALS['strReverseLookup']."\n		</i> puoi aggiungere nomi di dominio e indirizzi IP, altrimenti puoi utilizzare\n		solo indirizzi IP. È inoltre possibile inserire caratteri jolly (p.es. '*.altavista.com'\n		o '192.168.*').\n		";

$GLOBALS['phpAds_hlp_begin_of_week'] = "\n		Per la maggior parte delle persone una settimana di Lunedì, ma se desideri\n		puoi utilizzare anche la Domenica come primo giorno della settimana.\n		";

$GLOBALS['phpAds_hlp_percentage_decimals'] = "\n		Specifica quante cifre decimali utilizzare nelle pagine delle statistiche.\n		";

$GLOBALS['phpAds_hlp_warn_admin'] = "\n        ".MAX_PRODUCT_NAME." può spedirti una e-mail quando una campagna sta\n		per esaurire i crediti di Visualizzazioni o Click. L'opzione è abilitata di default.\n		";

$GLOBALS['phpAds_hlp_warn_client'] = "". MAX_PRODUCT_NAME ." può spedire un'email all'inserzionista se una delle sue campagne ha solo un";

$GLOBALS['phpAds_hlp_qmail_patch'] = "Alcune versioni di qmail hanno un bug, che fa sì che le instestazioni delle e-mail\ninviate da ". MAX_PRODUCT_NAME ." compaiano invece nel corpo della e-mail. Se questa opzione\nè abilitata ". MAX_PRODUCT_NAME ." invierà le e-mail in un formato compatibile\ncon qmail.";

$GLOBALS['phpAds_hlp_warn_limit'] = "Il limite in cui ". MAX_PRODUCT_NAME ." inizia l'invio di e-mail di avviso è 100";

$GLOBALS['phpAds_hlp_allow_invocation_plain'] =
$GLOBALS['phpAds_hlp_allow_invocation_js'] =
$GLOBALS['phpAds_hlp_allow_invocation_frame'] =
$GLOBALS['phpAds_hlp_allow_invocation_xmlrpc'] =
$GLOBALS['phpAds_hlp_allow_invocation_local'] =
$GLOBALS['phpAds_hlp_allow_invocation_interstitial'] =
$GLOBALS['phpAds_hlp_allow_invocation_popup'] = "\n		Queste impostazioni controllano i tipi di invocazione consentiti.\n		Se un tipo di invocazione non è abilitata, essa non sarà\n		disponibile nel generatore di codici di invocazione.<br /><b>N.B.</b> i tipi di\n		invocazione disabilitati continueranno a funzionare, benché non sia\n		possibile generarne il codice.\n		";

$GLOBALS['phpAds_hlp_con_key'] = "\n		".MAX_PRODUCT_NAME." include un potente sistema di fornitura dei banner\n		tramite selezione diretta. Per maggiori informazioni leggere il manuale utente.\n		Con questa opzione sarà possibile utilizzare parole chiave condizionali.\n		L'opzione è abilitata di default.\n		";

$GLOBALS['phpAds_hlp_mult_key'] = "\n		Utilizzando la selezione diretta per invocare i banner, è possibile\n		specificare una o più parole chiave per ogni banner. Quaesta opzione\n		deve essere attivata per utilizzare più di una parola chiave per banner.\n		L'opzione è abilitata di default.\n		";

$GLOBALS['phpAds_hlp_acl'] = "\n		Se non utilizzi le limitazioni di consegna puoi disabilitare questa opzione per\n		ottenere un lieve incremento di velocit&agrave.\n		";

$GLOBALS['phpAds_hlp_default_banner_url'] =
$GLOBALS['phpAds_hlp_default_banner_target'] = "\n		Se ".MAX_PRODUCT_NAME." non riesce a connettersi al database, o non riesce a trovare\n		banner corrispondenti alla richiesta, per esempio se il database si è rovinato\n		o è stato cancellato, non mostra nulla. Alcune utenti preferiscono che\n		in questi casi venga mostrato un banner di default. Il banner specificato qui non\n		verrà ignorato ai fini della registrazione di Visualizzazioni e Click e non\n		sarà usato se ci saranno ancora banner attivi nel database. L'opzione è\n		disabilitata di default.\n		";

$GLOBALS['phpAds_hlp_delivery_caching'] = "\n		Per diminuire i tempi di consegna, ".MAX_PRODUCT_NAME." utilizza una cache per\n		memorizzare le informazioni necessarie alla consegna dei banner ai visitatori. La cache\n		di consegna è memorizzata di default nel database, ma per aumentare ancora la\n		velocità di risposta, è possibile memorizzare la cache in un file o nella\n		memoria condivisa. La memoria condivisa è la più veloce. I file sono quasi\n		altrettanto veloci. Non è consigliabile disabilitare la cache, in quanto il decadimento\n		delle prestazioni sarebbe considerevole.\n		";

$GLOBALS['phpAds_hlp_type_sql_allow'] =
$GLOBALS['phpAds_hlp_type_web_allow'] =
$GLOBALS['phpAds_hlp_type_url_allow'] =
$GLOBALS['phpAds_hlp_type_html_allow'] =
$GLOBALS['phpAds_hlp_type_txt_allow'] = "\n        ".MAX_PRODUCT_NAME." può utilizzare diversi tipi di banner e memorizzarli in maniera\n		differente. Le prime due opzioni regolano la memorizzazione locale dei banner; è\n		infatti possibile usare l'interfaccia di amministrazione per eseguire l'upload dei banner\n		e ".MAX_PRODUCT_NAME."  li salverà nel database o su un server web. Si possono inoltre\n		utilizzare banner memorizzati su server esterni, oppure usare HTML o un testo semplice\n		per generare un banner.\n		";

$GLOBALS['phpAds_hlp_type_web_mode'] = "\n		Per utilizzare i banner memorizzati sul server web, è necessario configurare\n		questa opzione. Per memorizzare i banner in una directory locale selezionare\n		<i>".$GLOBALS['strTypeWebModeLocal']."</i>; per utilizzare un server FTP esterno\n		<i>".$GLOBALS['strTypeWebModeFtp']."</i>. Su alcuni server web si può anche\n		utilizzare FTP anche per il server locale.\n		";

$GLOBALS['phpAds_hlp_type_web_dir'] = "\n		Inserire la directory dove ".MAX_PRODUCT_NAME."  deve copiare i banner di cui si è\n		eseguito l'upload. L'interprete PHP deve essere in grado di potervi scrivere, e questo\n		significa che potrebbe essere necessario modificarne i permessi UNIX (chmod). La directory\n		qui specificata deve essere nella document root del server web, poiché il server\n		deve essere in grado di inviarla autonomamente. Non inserire la barra finale (/). È\n		necessario configurare questa opzione solo se il metodo di memorizzazione selezionato è\n		<i>".$GLOBALS['strTypeWebModeLocal']."</i>.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_host'] = "\n		Se il metodo di memorizzazione selezionato è <i>".$GLOBALS['strTypeWebModeLocal']."</i>\n		è necessario specificare l'indirizzio IP o il nome di dominio del server FTP dove\n		".MAX_PRODUCT_NAME." deve copiare i banner.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_path'] = "\n		Se il metodo di memorizzazione selezionato è <i>".$GLOBALS['strTypeWebModeLocal']."</i>\n		è necessario specificare la directory sul server FTP dove\n		".MAX_PRODUCT_NAME." deve copiare i banner.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_user'] = "\n		Se il metodo di memorizzazione selezionato è <i>".$GLOBALS['strTypeWebModeLocal']."</i>\n		è necessario specificare il nome utente per connettersi al server FTP dove\n		".MAX_PRODUCT_NAME." deve copiare i banner.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_password'] = "\n		Se il metodo di memorizzazione selezionato è <i>".$GLOBALS['strTypeWebModeLocal']."</i>\n		è necessario specificare la password per connettersi al server FTP dove\n		";

$GLOBALS['phpAds_hlp_type_web_url'] = "\n		Se si memorizzano banner su un server web, ".MAX_PRODUCT_NAME." deve conoscere\n		l'URL pubblico a cui corrisponde la directory specificata qui sotto. Non inserire\n		la barra alla fine (/).\n		";

$GLOBALS['phpAds_hlp_type_html_auto'] = "\n		Se questa opzione è attiva ".MAX_PRODUCT_NAME." modificherà automaticamente\n		i banner HTML per rendere possibile la registrazione dei click. Se attiva, sarà\n		comunque possibile disabilitarla per i banner per i quali lo si desidera.\n		";

$GLOBALS['phpAds_hlp_type_html_php'] = "\n		È possibile far eseguire a ".MAX_PRODUCT_NAME." codice PHP contenuto all'interno\n		dei banner HTML. L'opzione è disabilitata di default.\n		";

$GLOBALS['phpAds_hlp_admin'] = "\n        Inserire lo username dell'amministratore. Utilizzando questo nome sarà possibile\n		entrare nell'interfaccia di amministrazione.\n		";

$GLOBALS['phpAds_hlp_admin_pw'] =
$GLOBALS['phpAds_hlp_admin_pw2'] = "\n		Inserire la password per entrare nell'interfaccia di amministrazione.\n		È necessario scrivere la nuova password due volte, per evitare errori di battitura.\n		";

$GLOBALS['phpAds_hlp_pwold'] =
$GLOBALS['phpAds_hlp_pw'] =
$GLOBALS['phpAds_hlp_pw2'] = "\n		Per modificare la password dell'amministratore è necessario inserire la\n		vecchia password in alto. È inoltre necessario scrivere la nuova password\n		due volte, per evitare errori di battitura.\n		";

$GLOBALS['phpAds_hlp_admin_fullname'] = "\n		Inserire il nome completo dell'amministratore. Questo campo viene utilizzato nella\n		spedizione delle statistiche via email.\n		";

$GLOBALS['phpAds_hlp_admin_email'] = "L'indirizzo email dell'amministratore. E' utilizzato come indirizzo del mittente quando";

$GLOBALS['phpAds_hlp_admin_email_headers'] = "\n		Qui si possono inserire header supplementari per le email inviate da ".MAX_PRODUCT_NAME.".\n		";

$GLOBALS['phpAds_hlp_admin_novice'] = "Se vuoi ricevere un messaggio di avviso prima della cancellazione di inserzionisti, campagne, banner, editori e zone, imposta questa opzione a Vero.";

$GLOBALS['phpAds_hlp_client_welcome'] = "\n		Se questa opzione è attiva, verrà visualizzato un messaggio di benvenuto\n		dopo il login dell'inserzionista. E' possibile personalizzare il messaggio modificando\n		il file welcome.html nella directory admin/templates. Nella maggior parte dei casi\n		può essere utile includere il nome della propria società, il logo,\n		informazioni sui contatti, un link alla pagina del listino, ecc...\n		";

$GLOBALS['phpAds_hlp_client_welcome_msg'] = "\n		Invece di modificare il file welcome.html, è possibile inserire un breve testo qui;\n		il file welcome.html sarà così ignorato. E' possibile usare codice HTML.\n		";

$GLOBALS['phpAds_hlp_updates_frequency'] = "\n		Attivare questa funzione per ricercare automaticamente versioni aggiornate di ".MAX_PRODUCT_NAME.".\n		E' possibile specificare l'intervallo con cui ".MAX_PRODUCT_NAME." effettuerà la\n		connessione al server degli aggiornamenti. Se viene trovata una versione aggiornata apparirà\n		una finestra con le informazioni necessarie.\n		";

$GLOBALS['phpAds_hlp_userlog_email'] = "\n		Attivando questa opzione sarà possibile salvare una copia di tutte le e-mail inviate da\n		".MAX_PRODUCT_NAME.". I messaggi saranno memorizzati nel ".$GLOBALS['strUserLog'].".\n		";

$GLOBALS['phpAds_hlp_userlog_priority'] = "\n		Per assicurarsi che il calcolo delle priorità sia andato a buon fine, è\n		possibile salvare un resoconto dei calcoli effettuati ogni ora. Il rapporto contiene\n		il profilo previsto e la proritè assegnata a tutti i banner. Questa informazione\n		è utile nel caso si voglia segnalare un bug nel calcolo delle priorità.\n		I rapporti sono memorizzati nel ".$GLOBALS['strUserLog'].".\n		";

$GLOBALS['phpAds_hlp_userlog_autoclean'] = "\n		Per assicurarsi che il database venga ripulito correttamente, è\n		possibile salvare un resoconto di quanto è effettivavente accaduto durante\n		la pulizia. I rapporti sono memorizzati nel ".$GLOBALS['strUserLog'].".\n		";

$GLOBALS['phpAds_hlp_default_banner_weight'] = "\n		Inserire il peso proposto durante la creazione dei banner.\n		Il valore di default è 1.\n		";

$GLOBALS['phpAds_hlp_default_campaign_weight'] = "\n		Inserire il peso proposto durante la creazione delle campagne.\n		Il valore di default è 1.\n		";

$GLOBALS['phpAds_hlp_gui_show_campaign_info'] = "Se questa opzione è abilitata, saranno mostrate informazioni extra a riguardo di ogni campagna nella pagina <em>Campagne</em>. Le informazioni extra includono il numero di visualizzazioni, il numero di click, il numero di conversioni rimanenti, la data di attivazione, quella di scadenza e le impostazioni di priorità.";

$GLOBALS['phpAds_hlp_gui_show_banner_info'] = "Se questa opzione è abilitata, saranno mostrate informazioni extra relative ad ogni banner nella pagina <em>Banner</em>. Le informazioni extra includono la URL di destinazione, le parole chiave, le dimensioni ed il peso del banner.";

$GLOBALS['phpAds_hlp_gui_show_campaign_preview'] = "Se questa opzione è abilitata, sarà mostrata una anteprima di ogni banner nella pagina <em>Banner</em>. Se l'opzione viene disabilitata sara comunque possibile vedere un'anteprima di ogni banner cliccando il triangolo adiacente.";

$GLOBALS['phpAds_hlp_gui_show_banner_html'] = "\n		Se questa opzione è attiva verrà mostrato il banner HTML invece del codice. Questa\n		opzione è disbailitata di default, poiché il codice di un banner HTML può\n		creare conflitti con l'interfaccia utente. Se l'opzione è disabilitata sarà comunque\n		possibile visualizzare il banner cliccando sul pulsante <i>".$GLOBALS['strShowBanner']."</i>\n		a fianco del codice HTML del banner.\n		";

$GLOBALS['phpAds_hlp_gui_show_banner_preview'] = "\n		Se questa opzione è attiva verrà mostrata un'anteprima nella parte superiore delle pagine\n		<i>".$GLOBALS['strBannerProperties']."</i>, <i>".$GLOBALS['strModifyBannerAcl']."</i> and\n		<i>".$GLOBALS['strLinkedZones']."</i>. Se l'opzione è disabilitata sarà comunque\n		possibile visualizzare il banner cliccando sul pulsante <i>".$GLOBALS['strShowBanner']."</i> in alto.\n		";

$GLOBALS['phpAds_hlp_gui_hide_inactive'] = "Se questa opzione è abiliata, tutti i banner, le campagne, gli inserzionisti inattivi saranno nascosti dalle pagine <em>Inserzionisti e campagne</em> e <em>Campagne</em>. È possibile visualizzare gli oggetti nascosti cliccando <em>Mostra tutti</em> in fondo alla pagina.";

$GLOBALS['phpAds_hlp_gui_show_matching'] = "\n		Se questa opzione è attiva, i banner corrispondenti verranno mostrati nella pagina\n		<i>".$GLOBALS['strIncludedBanners']."</i>, se è selezionato il metodo\n		<i>". $GLOBALS['strCampaignSelection']."</i>. Questo permetterà di vedere\n		quali banner sono presi in considerazione per la consegna se la campagna è collegata.\n		Sarà inoltre possibile vedere un'anteprima dei banner corrispondenti.\n		";

$GLOBALS['phpAds_hlp_gui_show_parents'] = "\n		Se questa opzione è attiva, verranno mostrate le campagne che contengono i banner nella pagina\n		<i>".$GLOBALS['strIncludedBanners']."</i>, se è selezionato il metodo\n		<i>". $GLOBALS['strBannerSelection']."</i>. Questo permetterà di vedere a quali campagne\n		appartengono i banner prima di collegarli alla zona. I banner saranno perciò raggruppati\n		per campagne e non più ordinati alfabeticamente.\n		";

$GLOBALS['phpAds_hlp_gui_link_compact_limit'] = "\n		Per defualt tutti banner o le campagne disponibili sono visualizzati nella pagina <i>".$GLOBALS['strIncludedBanners']."</i>.\n		Di conseguenza, se l'inventario contiene molti banner, la pagina può diventare molto lunga.\n		Questa opzione permette di impostare il numero massimo di oggetti da visualizzare nella pagina.\n		Se il numero è maggiore verrà mostrato il metodo di collegamento che richiede meno spazio.\n		";

?>