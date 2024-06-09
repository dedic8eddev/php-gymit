<htmlpageheader name="contractHeader">
<div class="clearfix">
    <div class="float-left w-40">
        <img width="180px" src="<?php echo config_item('app')['img_folder'].'pdf/contract/logo_gymit.png'; ?>" />
    </div>
    <div class="float-right w-60 text-right">
        <p class="my-1">
            <img width="10px" src="<?php echo config_item('app')['img_folder'].'pdf/contract/phone.png'; ?>" />
            <?php echo $general_info->phone; ?>
        </p>
        <p class="my-1">
            <img width="11px" src="<?php echo config_item('app')['img_folder'].'pdf/contract/envelope.png'; ?>" />
            <?php echo $general_info->email; ?>
        </p>
        <p class="my-1">
            <img width="10px" src="<?php echo config_item('app')['img_folder'].'pdf/contract/location.png'; ?>" />
            <?php echo $general_info->street; ?>, <?php echo $general_info->zip; ?> <?php echo $general_info->city; ?>
        </p>
    </div>
</div>
<hr />
</htmlpageheader>
<htmlpagefooter name="contractFooter">
    <div class="text-center">{PAGENO}</div>
</htmlpagefooter>
<div class="text-center">
    <b>ČLENSKÁ SMLOUVA/MEMBERSHIP CONTRACT<br />Č./NR. <?php echo $contractNumber; ?></b>
</div>
<p class="mt-5">
    <?php echo $subject_info->name; ?>, společnost zapsaná v obchodním rejstříku vedeného u Městského soudu v Praze pod sp. zn. C 258894, IČ <?php echo $subject_info->company_id; ?>, se sídlem <?php echo $subject_info->street; ?>, <?php echo $subject_info->zip; ?> <?php echo $subject_info->town; ?>, jako provozovatel klubu GymIt!/<i>Company registered in the Companies‘ Register administered by the City Court in Prague, under ref. No.C 258894, having its registered office at <?php echo $subject_info->street; ?>, <?php echo $subject_info->zip; ?> <?php echo $subject_info->town; ?> as operator of GymIt! Clubs,</i>
</p>
<p>
    dále jen „provozovatel“/<i>hereinafter referred to as „the Operator“</i>
</p>
<p class="mb-4">
    a/<i>and</i>
</p>
<div class="clearfix">
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo @$user_data->first_name; ?></td></tr>
            <tr><td class="bt-dotted">jméno/<i>name</i></td></tr>
        </table>
    </div>
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo @$user_data->last_name; ?></td></tr>
            <tr><td class="bt-dotted">příjmení/<i>surname</i></td></tr>
        </table>
    </div>
</div>
<div class="clearfix mt-4">
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo isset($user_data->birth_date) ? date('j. n. Y', strtotime($user_data->birth_date)) : ''; ?>&nbsp;</td></tr>
            <tr><td class="bt-dotted">datum narození/<i>birth date</i></td></tr>
        </table>
    </div>
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo @$user_data->personal_identification_number; ?>&nbsp;</td></tr>
            <tr><td class="bt-dotted">rodné číslo/<i>birth number</i></td></tr>
        </table>
    </div>
</div>
<table class="form w-100 mt-3">
    <tr><td><?php echo isset($user_data->street) ? sprintf('%s, %s %s', $user_data->street, $user_data->zip, $user_data->city) : ''; ?>&nbsp;</td></tr>
    <tr><td class="bt-dotted w-100">trvalý pobyt dle průkazu totožnosti/<i>permanent residence as indicated in ID</i></td></tr>
</table>
<table class="form w-100 mt-3">
    <tr><td>&nbsp;</td></tr>
    <tr><td class="bt-dotted w-100">kontaktní adresa (pokud se liší od trvalého pobytu)/<i>contact address (if different from the permanent residence)</i></td></tr>
</table>
<div class="clearfix mt-3">
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo @$user_data->phone; ?>&nbsp;</td></tr>
            <tr><td class="bt-dotted">mobilní telefon/<i>mobile phone number</i></td></tr>
        </table>
    </div>
    <div class="float-left w-50">
        <table class="form">
            <tr><td><?php echo @$user_data->email; ?>&nbsp;</td></tr>
            <tr><td class="bt-dotted">e-mailová adresa/<i>e-mail address</i></td></tr>
        </table>
    </div>
</div>
<p>jako člen (as member)<br/>dále jen „člen“ <i>(hereinafter referred to as „the member“)</i>,</p>
<p>uzavírají dnešního dne tuto smlouvu o členství:<br/><i>have entered on this day into the following membership contract:</i></p>
<p>Údaje o členství/<i>membership details</i>:</p>
<p>Klub/<i>club</i>: GymIt! Ústí nad Labem, Masarykova 592/93</p>
<p>Druh členství/<i>membership type</i>:&emsp; <span class="bb-dotted">&emsp;&emsp;<?php echo isset($subInfo->name) ? $subInfo->name : '&emsp;&emsp;&emsp;&emsp;'; ?>&emsp;&emsp;</span></p>
<p>
Cena/<i>price</i>:&emsp; <span class="bb-dotted">&emsp;&emsp;&emsp;<?php echo isset($subInfo->price) ? number_format($subInfo->price, 0, ',', ' ') . ' Kč' : '&emsp;&emsp;'; ?>&emsp;&emsp;&emsp;</span><br />
od/<i>as of</i>:&emsp; <span class="bb-dotted">&emsp;&emsp;&emsp;<?php echo isset($subPayments->createdOn) ? mongoDateToDatetime($subPayments->createdOn)->format('j. n. Y'): '&emsp;&emsp;'; ?>&emsp;&emsp;&emsp;</span>
</p>
<pagebreak>
<ol class="terms pl-0">
    <li>&emsp;&emsp;Podpisem této smlouvy vzniká členovi ke shora uvedenému dni členství v klubu GymIt! Premium Fitness shora uvedeného typu. Práva a závazky mezi členem a provozovatelem se řídí touto smlouvou, provozním řádem a příslušnými právními předpisy, zejména občanským zákoníkem (zákon č. 89/2012 Sb., v platném znění).</li>
    <li>&emsp;&emsp;Člen podpisem této smlouvy potvrzuje, že se seznámil s provozním řádem (dostupný na internetu pod www.gymit.cz, příloha této smlouvy), a zavazuje se ho dodržovat. Provozovatel si vyhrazuje právo provádět čas od času změny provozního řádu podle své úvahy s přihlédnutím k potřebám členům a místním podmínkám; tyto změny jsou zveřejňovány na www.gymit.cz a na recepci klubu.</li>
    <li>&emsp;&emsp;Člen se zavazuje platit za členství členský poplatek ve výši dle platného ceníku. Při podpisu této smlouvy člen již zaplatil členství na první měsíční nebo roční období trvání členství podle údajů uvedených v záhlaví. Další členské poplatky jsou splatné vždy nejpozději v první den měsíčního nebo ročního období bezprostředně následujícího po prvním, již zaplaceném období trvání členství.</li>
    <li>&emsp;&emsp;Není-li členský poplatek za takové další období včas uhrazen, členství se přerušuje a člen není oprávněn využívat služeb klubu do jeho uhrazení. V případě prodlení delšího než tři měsíce členství zaniká. Zaniklé členství nelze obnovit, lze však uzavřít novou smlouvu o členství.</li>
    <li>&emsp;&emsp;Smlouva o členství se uzavírá na dobu neurčitou. Smlouvu lze vypovědět v případě měsíčního členství v jednoměsíční výpovědní lhůtě písemnou výpovědí doručenou provozovateli nebo členovi na adresu uvedenou v záhlaví této smlouvy nebo předanou na recepci klubu nejpozději v poslední den kalendářního měsíce předcházejícího v měsíci, k němuž začne plynout výpovědní lhůta. V případě ročního členství hrazeného na rok dopředu lze členství ukončit pouze formou převodu členství na jinou osobu za poplatek uvedený v provozním řádu.</li>
    <li>&emsp;&emsp;Strany potvrzují, že tuto smlouvu uzavírají podle svobodné a vážné vůle, nejsouce si vědomy žádných okolností, jež by jejímu uzavření bránily. Tato smlouva se uzavírá ve dvou vyhotoveních, z nichž každá ze stran obdrží po jednom.</li>
</ol>
<p style="margin-top:100px;">
V Ústí nad Labem dne <span class="bb-dotted">&emsp;&emsp;&emsp;<?php echo isset($subPayments->createdOn) ? mongoDateToDatetime($subPayments->createdOn)->format('j. n. Y'): '&emsp;&emsp;'; ?>&emsp;&emsp;&emsp;</span>
</p>
<div class="clearfix" style="margin-top:100px;">
    <div class="float-left w-50">
        <table class="form">
            <tr><td></td></tr>
            <tr><td class="bt-dotted">za provozovatele <?php echo $subject_info->name; ?> (podpis manažera pověřeného uzavíráním členských smluv)		</i></td></tr>
        </table>
    </div>
    <div class="float-left w-50">
        <table class="form">
            <tr><td></td></tr>
            <tr><td class="bt-dotted">za člena (podpis člena nebo jeho zákonného zástupce s uvedením, že se 	jedná 	o zákonného zástupce)</i></td></tr>
        </table>
    </div>
</div>
<?php if(isset($subPayments->subPeriod) && $subPayments->subPeriod=='month'): ?>
    <pagebreak>
    <div class="text-center">
        <b>INFORMACE K OČEKÁVANÝM PLATBÁM<br />při zakoupení ročního členství s měsíční úhradou</b>
    </div>
    <p class="mt-5">
    Typ členství:&emsp;<span class="bb-dotted">&emsp;<?php echo @$subInfo->name; ?>&emsp;</span><br />
    Platnost Od:&emsp;<span class="bb-dotted">&emsp;&emsp;&emsp;<?php echo date('j. n. Y', strtotime($subPayments->transactions[0]->start)); ?>&emsp;&emsp;&emsp;</span><br />
    Platnost Do:&emsp;<span class="bb-dotted">&emsp;&emsp;&emsp;<?php echo date('j. n. Y', strtotime($subPayments->transactions[count($subPayments->transactions) - 1]->end)); ?>&emsp;&emsp;&emsp;</span>
    </p>
    <p>Platební údaje:</p>
    <p>
    Č. účtu:&emsp;<span class="bb-dotted">&emsp;&emsp;<?php echo $subject_info->bank_account; ?>&emsp;&emsp;</span><br />
    Variabilní symbol&emsp;<span class="bb-dotted">&emsp;&emsp;<?php echo $contractNumber; ?>&emsp;&emsp;</span><br />
    Splatnost: viz rozpis níže
    </p>
    <p class="mt-5">Předpis plateb:</p>
    <table id="subPayments" class="mt-4">
        <thead>
            <tr>
                <th style="width:120px;">Začátek</th>
                <th style="width:120px;">Konec</th>
                <th style="width:120px;">Cena s DPH</th>
                <th style="width:120px;">Splatnost</th>
                <th>Stav platby</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($subPayments->transactions as $t): ?>
                <tr>
                    <td><?php echo date('d.m.Y', strtotime($t->start)); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($t->end)); ?></td>
                    <td><?php echo $t->value+$t->vat_value; ?> Kč</td>
                    <td><?php echo date('d.m.Y', strtotime($t->start)); ?></td>
                    <td style="width:150px;"><?php echo $t->paid ? 'Zaplaceno' : 'Očekávaná platba'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="mt-5">Předem děkujeme za úhradu a přejeme příjemný den.</p>
    <p>Váš</p>
    <img width="180px" src="<?php echo config_item('app')['img_folder'].'pdf/contract/logo_gymit.png'; ?>" />
<?php endif; ?>

