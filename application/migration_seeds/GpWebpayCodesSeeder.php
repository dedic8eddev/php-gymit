<?php


use Phinx\Seed\AbstractSeed;

class GpWebpayCodesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {


        // membership types
        $data = [
            [
                'type' => 'PRCODE',
                'code' => 0,
                'text' => 'OK',
                'text_en' => 'OK'
            ],
            [
                'type' => 'PRCODE',
                'code' => 1,
                'text' => 'Pole příliš dlouhé',
                'text_en' => 'Field too long'
            ],
            [
                'type' => 'PRCODE',
                'code' => 2,
                'text' => 'Pole příliš krátké',
                'text_en' => 'Field too short'
            ],
            [
                'type' => 'PRCODE',
                'code' => 3,
                'text' => 'Chybný obsah pole',
                'text_en' => 'Incorrect content of field'
            ],
            [
                'type' => 'PRCODE',
                'code' => 4,
                'text' => 'Pole je prázdné',
                'text_en' => 'Field is null'
            ],
            [
                'type' => 'PRCODE',
                'code' => 5,
                'text' => 'Chybí povinné pole',
                'text_en' => 'Missing required field'
            ],
            [
                'type' => 'PRCODE',
                'code' => 6,
                'text' => 'Pole neexistuje',
                'text_en' => 'Missing field'
            ],
            [
                'type' => 'PRCODE',
                'code' => 11,
                'text' => 'Neznámý obchodník',
                'text_en' => 'Unknown merchant'
            ],
            [
                'type' => 'PRCODE',
                'code' => 14,
                'text' => 'Duplikátní číslo platby',
                'text_en' => 'Duplicate order number'
            ],
            [
                'type' => 'PRCODE',
                'code' => 15,
                'text' => 'Objekt nenalezen',
                'text_en' => 'Object not found'
            ],
            [
                'type' => 'PRCODE',
                'code' => 16,
                'text' => 'Částka k autorizaci překročila původní částku platby',
                'text_en' => 'Amount to approve exceeds payment amount'
            ],
            [
                'type' => 'PRCODE',
                'code' => 17,
                'text' => 'Částka k zaplacení překročila povolenou (autorizovanou) částku',
                'text_en' => 'Amount to deposit exceeds approved amount'
            ],
            [
                'type' => 'PRCODE',
                'code' => 18,
                'text' => 'Součet vracených částek překročil zaplacenou částku',
                'text_en' => 'Total sum of credited amounts exceeded deposited amount'
            ],
            [
                'type' => 'PRCODE',
                'code' => 20,
                'text' => 'Objekt není ve stavu odpovídajícím této operaci Info: Pokud v případě vytváření platby (CREATE_ORDER) obdrží obchodník tento návratový kód, vytvoření platby již proběhlo a platbyje v určitém stavu – tento návratový kód je zapříčiněn aktivitou držitele karty (například pokusem o přechod zpět, použití refresh…).',
                'text_en' => 'Object not in valid state for operation'
            ],
            [
                'type' => 'PRCODE',
                'code' => 25,
                'text' => 'Uživatel není oprávněn k provedení operace',
                'text_en' => 'Operation not allowed for user'
            ],
            [
                'type' => 'PRCODE',
                'code' => 26,
                'text' => 'Technický problém při spojení s autorizačním centrem',
                'text_en' => 'Technical problem in connection to authorization center'
            ],
            [
                'type' => 'PRCODE',
                'code' => 27,
                'text' => 'Chybný typ platby',
                'text_en' => 'Incorrect payment type'
            ],
            [
                'type' => 'PRCODE',
                'code' => 28,
                'text' => 'Zamítnuto v 3D Info: důvod zamítnutí udává SRCODE',
                'text_en' => 'Declined in 3D '
            ],
            [
                'type' => 'PRCODE',
                'code' => 30,
                'text' => 'Zamítnuto v autorizačním centru Info: Důvod zamítnutí udává SRCODE',
                'text_en' => 'Declined in AC'
            ],
            [
                'type' => 'PRCODE',
                'code' => 31,
                'text' => 'Chybný podpis',
                'text_en' => 'Wrong digest'
            ],
            [
                'type' => 'PRCODE',
                'code' => 32,
                'text' => 'Expirovaná karta',
                'text_en' => 'Expired card'
            ],
            [
                'type' => 'PRCODE',
                'code' => 33,
                'text' => 'Originální/Master platba není autorizovaná',
                'text_en' => 'Original/Master order was not authorized'
            ],
            [
                'type' => 'PRCODE',
                'code' => 34,
                'text' => 'Originální/Master platbu nelze použít pro následné platby',
                'text_en' => 'Original/Master order is not valid for subsequent payment'
            ],
            [
                'type' => 'PRCODE',
                'code' => 35,
                'text' => 'Expirovaná session (Nastává při vypršení webové session při zadávání karty)',
                'text_en' => 'Session expired'
            ],
            [
                'type' => 'PRCODE',
                'code' => 38,
                'text' => 'Nepodporovaná karta',
                'text_en' => 'Card not supported'
            ],
            [
                'type' => 'PRCODE',
                'code' => 40,
                'text' => 'Zamítnuto ve Fraud detection system',
                'text_en' => 'Declined in Fraud detection systém'
            ],
            [
                'type' => 'PRCODE',
                'code' => 50,
                'text' => 'Držitel karty zrušil platbu',
                'text_en' => 'The cardholder canceled the payment'
            ],
            [
                'type' => 'PRCODE',
                'code' => 80,
                'text' => 'Duplicitní MessageId',
                'text_en' => 'Duplicate MessageId'
            ],
            [
                'type' => 'PRCODE',
                'code' => 82,
                'text' => 'V HSM chybí název šifrovacího klíče',
                'text_en' => 'HSM key label missing'
            ],
            [
                'type' => 'PRCODE',
                'code' => 83,
                'text' => 'Operace zrušena vydavatelem',
                'text_en' => 'Canceled by issuer'
            ],
            [
                'type' => 'PRCODE',
                'code' => 84,
                'text' => 'Duplicitní hodnota',
                'text_en' => 'Duplikate value'
            ],
            [
                'type' => 'PRCODE',
                'code' => 85,
                'text' => 'Zakázáno na základě pravidel obchodníka',
                'text_en' => 'Declined due to merchant’s rules'
            ],
            [
                'type' => 'PRCODE',
                'code' => 200,
                'text' => 'Žádost o doplňující informace',
                'text_en' => 'Additional info request'
            ],
            [
                'type' => 'PRCODE',
                'code' => 300,
                'text' => 'Podmíněně zamítnuto – vydavatel požaduje SCA',
                'text_en' => 'Soft decline – issuer requires SCA'
            ],
            [
                'type' => 'PRCODE',
                'code' => 1000,
                'text' => 'Technický problém',
                'text_en' => 'Technical problem'
            ],
            [
                'type' => 'SRCODE',
                'code' => 0,
                'text' => 'Bez významu',
                'text_en' => ''
            ],
            [
                'type' => 'SRCODE',
                'code' => 1,
                'text' => 'ORDERNUMBER',
                'text_en' => 'ORDERNUMBER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 2,
                'text' => 'MERCHANTNUMBER',
                'text_en' => 'MERCHANTNUMBER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3,
                'text' => 'PAN',
                'text_en' => 'PAN'
            ],
            [
                'type' => 'SRCODE',
                'code' => 4,
                'text' => 'EXPIRY',
                'text_en' => 'EXPIRY'
            ],
            [
                'type' => 'SRCODE',
                'code' => 5,
                'text' => 'CVV',
                'text_en' => 'CVV'
            ],
            [
                'type' => 'SRCODE',
                'code' => 6,
                'text' => 'AMOUNT',
                'text_en' => 'AMOUNT'
            ],
            [
                'type' => 'SRCODE',
                'code' => 7,
                'text' => 'CURRENCY',
                'text_en' => 'CURRENCY'
            ],
            [
                'type' => 'SRCODE',
                'code' => 8,
                'text' => 'DEPOSITFLAG',
                'text_en' => 'DEPOSITFLAG'
            ],
            [
                'type' => 'SRCODE',
                'code' => 10,
                'text' => 'MERORDERNUM',
                'text_en' => 'MERORDERNUM'
            ],
            [
                'type' => 'SRCODE',
                'code' => 11,
                'text' => 'CREDITNUMBER',
                'text_en' => 'CREDITNUMBER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 12,
                'text' => 'OPERATION',
                'text_en' => 'OPERATION'
            ],
            [
                'type' => 'SRCODE',
                'code' => 14,
                'text' => 'ECI',
                'text_en' => 'ECI'
            ],
            [
                'type' => 'SRCODE',
                'code' => 18,
                'text' => 'BATCH',
                'text_en' => 'BATCH'
            ],
            [
                'type' => 'SRCODE',
                'code' => 22,
                'text' => 'ORDER',
                'text_en' => 'ORDER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 24,
                'text' => 'URL',
                'text_en' => 'URL'
            ],
            [
                'type' => 'SRCODE',
                'code' => 25,
                'text' => 'MD',
                'text_en' => 'MD'
            ],
            [
                'type' => 'SRCODE',
                'code' => 26,
                'text' => 'DESC',
                'text_en' => 'DESC'
            ],
            [
                'type' => 'SRCODE',
                'code' => 34,
                'text' => 'DIGEST',
                'text_en' => 'DIGEST'
            ],
            [
                'type' => 'SRCODE',
                'code' => 43,
                'text' => 'ORIGINAL ORDER NUMBER',
                'text_en' => 'ORIGINAL ORDER NUMBER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 45,
                'text' => 'USERPARAM1',
                'text_en' => 'USERPARAM1'
            ],
            [
                'type' => 'SRCODE',
                'code' => 70,
                'text' => 'VRCODE',
                'text_en' => 'VRCODE'
            ],
            [
                'type' => 'SRCODE',
                'code' => 71,
                'text' => 'USERPARAM2',
                'text_en' => 'USERPARAM2'
            ],
            [
                'type' => 'SRCODE',
                'code' => 72,
                'text' => 'FASTPAYID',
                'text_en' => 'FASTPAYID'
            ],
            [
                'type' => 'SRCODE',
                'code' => 73,
                'text' => 'PAYMETHOD',
                'text_en' => 'PAYMETHOD'
            ],
            [
                'type' => 'SRCODE',
                'code' => 83,
                'text' => 'ADDINFO',
                'text_en' => 'ADDINFO'
            ],
            [
                'type' => 'SRCODE',
                'code' => 84,
                'text' => 'MPS_CHECKOUT_ID',
                'text_en' => 'MPS_CHECKOUT_ID'
            ],
            [
                'type' => 'SRCODE',
                'code' => 86,
                'text' => 'PAYMETHODS',
                'text_en' => 'PAYMETHODS'
            ],
            [
                'type' => 'SRCODE',
                'code' => 88,
                'text' => 'DEPOSIT_NUMBER',
                'text_en' => 'DEPOSIT_NUMBER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 89,
                'text' => 'RECURRING_ORDER',
                'text_en' => 'RECURRING_ORDER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 90,
                'text' => 'PAIRING',
                'text_en' => 'PAIRING'
            ],
            [
                'type' => 'SRCODE',
                'code' => 91,
                'text' => 'SHOP_ID',
                'text_en' => 'SHOP_ID'
            ],
            [
                'type' => 'SRCODE',
                'code' => 92,
                'text' => 'PANPATTERN',
                'text_en' => 'PANPATTERN'
            ],
            [
                'type' => 'SRCODE',
                'code' => 93,
                'text' => 'TOKEN',
                'text_en' => 'TOKEN'
            ],
            [
                'type' => 'SRCODE',
                'code' => 95,
                'text' => 'FASTTOKEN',
                'text_en' => 'FASTTOKEN'
            ],
            [
                'type' => 'SRCODE',
                'code' => 96,
                'text' => 'SUBMERCHANT INFO',
                'text_en' => 'SUBMERCHANT INFO'
            ],
            [
                'type' => 'SRCODE',
                'code' => 97,
                'text' => 'TOKEN_HSM_LABEL',
                'text_en' => 'TOKEN_HSM_LABEL'
            ],
            [
                'type' => 'SRCODE',
                'code' => 98,
                'text' => 'CUSTOM INSTALLMENT COUNT',
                'text_en' => 'CUSTOM INSTALLMENT COUNT'
            ],
            [
                'type' => 'SRCODE',
                'code' => 99,
                'text' => 'COUNTRY',
                'text_en' => 'COUNTRY'
            ],
            [
                'type' => 'SRCODE',
                'code' => 100,
                'text' => 'TERMINAL INFO',
                'text_en' => 'TERMINAL INFO'
            ],
            [
                'type' => 'SRCODE',
                'code' => 101,
                'text' => 'TERMINAL ID',
                'text_en' => 'TERMINAL ID'
            ],
            [
                'type' => 'SRCODE',
                'code' => 102,
                'text' => 'TERMINAL OWNER',
                'text_en' => 'TERMINAL OWNER'
            ],
            [
                'type' => 'SRCODE',
                'code' => 103,
                'text' => 'TERMINAL CITY',
                'text_en' => 'TERMINAL CITY'
            ],
            [
                'type' => 'SRCODE',
                'code' => 104,
                'text' => 'MC ASSIGNED ID',
                'text_en' => 'MC ASSIGNED ID'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3000,
                'text' => 'Neověřeno v 3D. Vydavatel karty není zapojen do 3D nebo karta nebyla aktivována. Info: Ověření držitele karty bylo neúspěšné (neplatně zadané údaje, stornování autentikace, uzavření okna pro autentikaci držitele karty se zpětnou vazbou…). V transakci se nesmí pokračovat.',
                'text_en' => 'Declined in 3D. Cardholder not authenticated in 3D. Note: Cardholder authentication failed (wrong password, transaction canceled, authentication window was closed…). Transaction Declined.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3001,
                'text' => 'Držitel karty ověřen. Info: Ověření držitele karty v 3D systémech proběhlo úspěšně. Pokračuje se autorizací platby.',
                'text_en' => 'Authenticated Note: Cardholder was successfully authenticated – transaction continue with authorization.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3002,
                'text' => 'Neověřeno v 3D. Vydavatel karty nebo karta není zapojena do 3D. Info: V 3D systémech nebylo možné ověřit držitele karty – karta, nebo její vydavatel, není zapojen do 3D. V transakci se pokračuje.',
                'text_en' => 'Not Authenticated in 3D. Issuer or Cardholder not participating in 3D. Note: Cardholder wasn’t authenticated – Issuer or Cardholder not participating in 3D. Transaction can continue.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3004,
                'text' => 'Neověřeno v 3D. Vydavatel karty není zapojen do 3D nebo karta nebyla aktivována. Info: V 3D systémech nebylo možné ověřit držitele karty – karta není aktivována, nebo její vydavatel, není zapojen do 3D. V transakci je možné pokračovat.',
                'text_en' => 'Not Authenticated in 3D. Issuer not participating or Cardholder not enrolled. Note: Cardholder wasn’t authenticated – Cardholder not enrolled or Issuer or not participating in 3D. Transaction can continue.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3005,
                'text' => 'Zamítnuto v 3D.Technický problém při ověření držitele karty. Info: V 3D systémech nebylo možné ověřit držitele karty – vydavatel karty nepodporuje 3D, nebo technický problém v komunikaci s 3D systémy finančních asociací, či vydavatele karty. V transakci není možné pokračovat, povoleno z důvodu zabezpečení obchodníka před případnou reklamací transakce držitelem karty.',
                'text_en' => 'Declined in 3D. Technical problem during Cardholder authentication. Note: Cardholder authentication unavailable – issuer not supporting 3D or technical problem in communication between associations and Issuer 3D systems. Transaction cannot continue.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3006,
                'text' => 'Zamítnuto v 3D. Technický problém při ověření držitele karty. Info: V 3D systémech nebylo možné ověřit držitele karty – technický problém ověření obchodníka v 3D systémech, anebo v komunikaci s 3D systémy finančních asociací, či vydavatele karty. V transakci není možné pokračovat.',
                'text_en' => 'Declined in 3D. Technical problem during Cardholder authentication. Note: Technical problem during cardholder authentication – merchant authentication failed or technical problem in communication between association and acquirer. Transaction cannot continue.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3007,
                'text' => 'Zamítnuto v 3D. Technický problém v systému zúčtující banky. Kontaktujte obchodníka. Info: V 3D systémech nebylo možné ověřit držitele karty – technický problém v 3D systémech. V transakci není možné pokračovat.',
                'text_en' => 'Declined in 3D. Acquirer technical problem. Contact the merchant. Note: Technical problem during cardholder authentication – 3D systems technical problem. Transaction cannot continue.'
            ],
            [
                'type' => 'SRCODE',
                'code' => 3008,
                'text' => 'Zamítnuto v 3D. Použit nepodporovaný karetní produkt. Info: Byla použita karta, která není v 3D systémech podporována. V transakci není možné pokračovat.',
                'text_en' => 'Declined in 3D. Unsupported card product. Note: Card not supported in 3D. Transaction cannot continue. V případě PRCODE 30|se mohou vrátit následující SRCODE'
            ],
            [
                'type' => 'SRCODE',
                'code' => 1001,
                'text' => 'Zamitnuto v autorizacnim centru, karta blokovana. Zahrnuje důvody, které naznačují zneužití platební karty – kradená karta, podezření na podvod, ztracená karta apod. Karta je označena jako: Ztracená K zadržení K zadržení (speciální důvody). Ukradená. Většinou pokus o podvodnou transakci.',
                'text_en' => 'Declined in AC, Card blocked'
            ],
            [
                'type' => 'SRCODE',
                'code' => 1002,
                'text' => 'Zamitnuto v autorizacnim centru, autorizace zamítnuta Z autorizace se vrátil důvod zamítnutí “Do not honor“. Vydavatel, nebo finanční asociace zamítla autorizaci BEZ udání důvodu.',
                'text_en' => 'Declined in AC, Declined'
            ],
            [
                'type' => 'SRCODE',
                'code' => 1003,
                'text' => 'Zamitnuto v autorizacnim centru, problem karty Zahrnuje důvody: expirovaná karta, chybné číslo karty, nastavení karty - pro kartu není povoleno použití na internetu, nepovolená karta, expirovaná karta, neplatná karta, neplatné číslo karty, částka přesahuje maximální limit karty, neplatné CVC/CVV, neplatná délka čísla karty, neplatná expirační doba, pro kartu je požadována kontrola PIN.',
                'text_en' => 'Declined in AC, Card problem'
            ],
            [
                'type' => 'SRCODE',
                'code' => 1004,
                'text' => 'Zamitnuto v autorizacnim centru, technicky problem Autorizaci není možné provést z technických důvodů – technické problémy v systému vydavatele karty, nebo finančních asociací a finančních procesorů.',
                'text_en' => 'Declined in AC, Technical problem in authorization process'
            ],
            [
                'type' => 'SRCODE',
                'code' => 1005,
                'text' => 'Zamitnuto v autorizacnim centru, Problem uctu Důvody: nedostatek prostředků na účtu, překročeny limity, překročen max. povolený počet použití…',
                'text_en' => 'Declined in AC, Account problem'
            ],
        ];

        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $truncate = $this->query('truncate gpwebpay_codes');
        $constraintCheck = $this->query('SET FOREIGN_KEY_CHECKS = 1');
        $webpay = $this->table('gpwebpay_codes');
        $webpay->insert($data)->save();
    }
}
